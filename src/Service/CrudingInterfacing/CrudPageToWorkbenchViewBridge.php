<?php

declare(strict_types=1);

namespace App\Bridging\Service\CrudingInterfacing;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\ServiceInterface\CrudingInterfacing\CrudPageToWorkbenchViewBridgeInterface;
use App\Interfacing\Contract\Crud\CrudAction;
use App\Interfacing\Contract\Crud\CrudRouteContext;
use App\Interfacing\Contract\Crud\CrudScreenContext;
use App\Interfacing\Contract\Crud\CrudSidebarSection;
use App\Interfacing\Contract\Crud\CrudTableColumn;
use App\Interfacing\Contract\Crud\CrudWorkbenchView;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.bridge')]
final class CrudPageToWorkbenchViewBridge implements CrudPageToWorkbenchViewBridgeInterface
{
    private const SUPPORTED_PAYLOAD = 'App\\Cruding\\Dto\\Crud\\CrudPageDefinition';

    public function supports(object $payload, string $target): bool
    {
        return self::SUPPORTED_PAYLOAD === $payload::class && BridgeTarget::CRUDING_INTERFACING_WORKBENCH === $target;
    }

    public function bridge(object $payload, string $target, array $context = []): mixed
    {
        if (!$this->supports($payload, $target)) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported bridge request for payload "%s" and target "%s".',
                $payload::class,
                $target,
            ));
        }

        return $this->bridgeToWorkbench($payload, $context);
    }

    public function bridgeToWorkbench(object $payload, array $context = []): CrudWorkbenchView
    {
        $crudContext = $this->readObjectLike($payload, 'context');
        $access = $this->readMixedLike($payload, 'access', []);
        $meta = $this->normalizeArray($this->readMixedLike($payload, 'meta', []));
        $objects = $this->normalizeList($this->readMixedLike($payload, 'objects', []));
        $actions = $this->normalizeList($this->readMixedLike($payload, 'actions', []));

        $routeContext = new CrudRouteContext(
            resourcePath: $this->readScalarLike($crudContext, 'resourcePath', (string) ($meta['resourcePath'] ?? 'resource')),
            operation: $this->readScalarLike($crudContext, 'operation', (string) ($meta['operation'] ?? 'index')),
            surface: $this->readScalarLike($crudContext, 'surface', (string) ($meta['surface'] ?? 'public')),
            identifierField: $this->readNullableScalarLike($crudContext, 'identifierField', $this->nullableString($meta['identifierField'] ?? null)),
            identifierValue: $this->readIdentifierValue($crudContext, $meta),
        );

        $screenContext = new CrudScreenContext(
            routeContext: $routeContext,
            templateIntent: $this->resolveTemplateIntent($payload, $meta, $routeContext->operation),
            accessMode: $this->resolveAccessMode($access),
            capabilityLabel: $this->resolveCapabilityLabel($access, $routeContext),
            ownershipLabel: $this->resolveOwnershipLabel($access),
            readonly: $this->resolveReadonly($access),
            mutationAllowed: $this->resolveMutationAllowed($access),
            urls: $this->resolveUrls($payload, $routeContext, $context),
        );

        $rows = $this->resolveRows($objects, $actions, $payload, $context);
        $columns = $this->resolveColumns($meta, $rows);

        return new CrudWorkbenchView(
            routeContext: $routeContext,
            screenContext: $screenContext,
            eyebrow: (string) ($meta['eyebrow'] ?? 'Bridge-fed CRUD workbench'),
            title: $this->readScalarLike($payload, 'title', 'CRUD Workbench · '.$routeContext->resourceLabel()),
            subtitle: (string) ($meta['subtitle'] ?? 'CRUD page bridged from host business logic into Interfacing workbench contract.'),
            breadcrumbs: $this->resolveBreadcrumbs($meta, $routeContext),
            metaChips: $this->resolveMetaChips($meta, $routeContext, $screenContext, count($rows)),
            headerActions: $this->mapActions($actions, $context),
            panelTitle: (string) ($meta['panelTitle'] ?? 'Bridge orchestration layer'),
            panelHint: (string) ($meta['panelHint'] ?? 'Bridging translates CRUD page semantics into Interfacing workbench semantics without pushing Twig details across the boundary.'),
            panelMeta: (string) ($meta['panelMeta'] ?? sprintf('%s · %s · rows %d', $routeContext->surfaceLabel(), $screenContext->accessToneLabel(), count($rows))),
            filters: [],
            columns: $columns,
            rows: $rows,
            emptyState: (string) ($meta['emptyState'] ?? 'No rows available for this CRUD page.'),
            paginationLabel: (string) ($meta['paginationLabel'] ?? sprintf('%d row(s)', count($rows))),
            formFields: [],
            formSections: [],
            validationSummary: $this->stringList($meta['validationSummary'] ?? []),
            sidebarSections: $this->resolveSidebarSections($meta, $routeContext, $screenContext, count($rows)),
        );
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return list<array<string, scalar|null|array<int, CrudAction>>>
     */
    private function resolveRows(array $objects, array $actions, object $payload, array $context): array
    {
        $identifierField = $this->readScalarLike($this->readObjectLike($payload, 'context'), 'identifierField', 'id');
        $rowActions = $this->mapActions($actions, $context);
        $rows = [];

        foreach ($objects as $index => $object) {
            $row = $this->normalizeRow($object);
            if ([] === $row) {
                $row = ['value' => is_scalar($object) || null === $object ? $object : $this->stringifyValue($object)];
            }

            $rowId = isset($row[$identifierField]) ? (string) $row[$identifierField] : (string) ($index + 1);
            $row = ['id' => $rowId] + $row;
            if ([] !== $rowActions) {
                $row['_actions'] = $rowActions;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $meta
     * @param list<array<string, scalar|null|array<int, CrudAction>>> $rows
     *
     * @return list<CrudTableColumn>
     */
    private function resolveColumns(array $meta, array $rows): array
    {
        $metaColumns = $meta['columns'] ?? null;
        if (is_array($metaColumns) && [] !== $metaColumns) {
            $result = [];
            foreach ($metaColumns as $column) {
                if (is_string($column) && '' !== trim($column)) {
                    $result[] = new CrudTableColumn($column, $this->humanize($column));
                    continue;
                }
                if (is_array($column) && isset($column['key'])) {
                    $key = (string) $column['key'];
                    $result[] = new CrudTableColumn(
                        $key,
                        (string) ($column['label'] ?? $this->humanize($key)),
                        (bool) ($column['isCode'] ?? false),
                        (bool) ($column['isStatus'] ?? false),
                    );
                }
            }
            if ([] !== $result) {
                return $result;
            }
        }

        $firstRow = $rows[0] ?? [];
        $result = [];
        foreach ($firstRow as $key => $value) {
            if ('_actions' === $key) {
                continue;
            }
            $result[] = new CrudTableColumn(
                (string) $key,
                $this->humanize((string) $key),
                'id' === $key,
                'status' === $key,
            );
        }

        return $result;
    }

    /** @param array<string, mixed> $meta */
    private function resolveBreadcrumbs(array $meta, CrudRouteContext $routeContext): array
    {
        $breadcrumbs = $meta['breadcrumbs'] ?? null;
        if (is_array($breadcrumbs) && [] !== $breadcrumbs) {
            return $this->stringList($breadcrumbs);
        }

        return $routeContext->breadcrumbItems();
    }

    /** @param array<string, mixed> $meta */
    private function resolveMetaChips(array $meta, CrudRouteContext $routeContext, CrudScreenContext $screenContext, int $rowCount): array
    {
        $chips = $this->stringList($meta['metaChips'] ?? []);
        if ([] !== $chips) {
            return $chips;
        }

        return [
            'resource: '.$routeContext->resourcePath,
            'operation: '.$routeContext->operation,
            'surface: '.$routeContext->surface,
            'mode: '.$routeContext->mode(),
            'template intent: '.$screenContext->templateIntent,
            'access mode: '.$screenContext->accessMode,
            'rows: '.$rowCount,
        ];
    }

    /** @param array<string, mixed> $meta */
    private function resolveSidebarSections(array $meta, CrudRouteContext $routeContext, CrudScreenContext $screenContext, int $rowCount): array
    {
        $sections = $meta['sidebarSections'] ?? null;
        if (is_array($sections) && [] !== $sections) {
            $result = [];
            foreach ($sections as $section) {
                if (!is_array($section)) {
                    continue;
                }
                $result[] = new CrudSidebarSection(
                    title: (string) ($section['title'] ?? 'Section'),
                    facts: is_array($section['facts'] ?? null) ? $section['facts'] : [],
                    note: (string) ($section['note'] ?? ''),
                    actions: [],
                );
            }
            if ([] !== $result) {
                return $result;
            }
        }

        return [
            new CrudSidebarSection(
                title: 'Bridge facts',
                facts: [
                    'Resource path' => $routeContext->resourcePath,
                    'Operation' => $routeContext->operation,
                    'Surface' => $routeContext->surface,
                    'Access mode' => $screenContext->accessMode,
                    'Rows' => $rowCount,
                ],
                note: 'The business component remains the owner of CRUD semantics; Bridging only translates them into the Interfacing workbench contract.',
            ),
        ];
    }

    /**
     * @param list<mixed> $actions
     * @param array<string, mixed> $context
     *
     * @return list<CrudAction>
     */
    private function mapActions(array $actions, array $context): array
    {
        $result = [];
        foreach ($actions as $action) {
            if (!is_object($action) && !is_array($action)) {
                continue;
            }

            $label = $this->readScalarLike($action, 'label', $this->readScalarLike($action, 'name', 'Action'));
            $href = $this->resolveActionHref($action, $context);
            $variant = $this->resolveActionVariant($action);
            $result[] = new CrudAction($label, $href, $variant);
        }

        return $result;
    }

    /** @param array<string, mixed> $meta */
    private function resolveTemplateIntent(object $payload, array $meta, string $operation): string
    {
        $template = $this->readScalarLike($payload, 'template', (string) ($meta['template'] ?? ''));
        if ('' !== $template) {
            return match (true) {
                str_contains($template, 'index') => 'workbench.index',
                str_contains($template, 'form'), str_contains($template, 'new'), str_contains($template, 'edit') => 'workbench.form',
                str_contains($template, 'delete') => 'workbench.destructive',
                default => 'workbench.detail',
            };
        }

        return match ($operation) {
            'index' => 'workbench.index',
            'new', 'edit' => 'workbench.form',
            'delete' => 'workbench.destructive',
            default => 'workbench.detail',
        };
    }

    private function resolveAccessMode(mixed $access): string
    {
        $mode = $this->readNullableScalarLike($access, 'mode', null);
        if (null !== $mode) {
            return $mode;
        }

        if (true === $this->readMixedLike($access, 'granted', null)) {
            return 'interactive';
        }

        if (true === $this->readMixedLike($access, 'readonly', null)) {
            return 'readonly';
        }

        return 'interactive';
    }

    private function resolveCapabilityLabel(mixed $access, CrudRouteContext $routeContext): string
    {
        return $this->readScalarLike($access, 'capabilityLabel', 'CRUDing · '.$routeContext->resourceDomainLabel());
    }

    private function resolveOwnershipLabel(mixed $access): string
    {
        return $this->readScalarLike($access, 'ownershipLabel', 'Business capability owned by host component');
    }

    private function resolveReadonly(mixed $access): bool
    {
        return (bool) $this->readMixedLike($access, 'readonly', 'readonly' === $this->resolveAccessMode($access));
    }

    private function resolveMutationAllowed(mixed $access): bool
    {
        if (null !== ($value = $this->readMixedLike($access, 'mutationAllowed', null))) {
            return (bool) $value;
        }

        return !$this->resolveReadonly($access) && 'denied' !== $this->resolveAccessMode($access);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array{index:string,new:string,show:string,edit:string,delete:string,next:string}
     */
    private function resolveUrls(object $payload, CrudRouteContext $routeContext, array $context): array
    {
        $resolver = $context['route_context_url_resolver'] ?? null;
        if (is_callable($resolver)) {
            $resolved = $resolver($payload, $routeContext, $context);
            if (is_array($resolved)) {
                return [
                    'index' => (string) ($resolved['index'] ?? '#'),
                    'new' => (string) ($resolved['new'] ?? '#'),
                    'show' => (string) ($resolved['show'] ?? '#'),
                    'edit' => (string) ($resolved['edit'] ?? '#'),
                    'delete' => (string) ($resolved['delete'] ?? '#'),
                    'next' => (string) ($resolved['next'] ?? '#'),
                ];
            }
        }

        $meta = $this->normalizeArray($this->readMixedLike($payload, 'meta', []));
        $urls = $this->normalizeArray($meta['urls'] ?? []);

        return [
            'index' => (string) ($urls['index'] ?? '#'),
            'new' => (string) ($urls['new'] ?? '#'),
            'show' => (string) ($urls['show'] ?? '#'),
            'edit' => (string) ($urls['edit'] ?? '#'),
            'delete' => (string) ($urls['delete'] ?? '#'),
            'next' => (string) ($urls['next'] ?? '#'),
        ];
    }

    /** @param array<string, mixed> $context */
    private function resolveActionHref(mixed $action, array $context): string
    {
        foreach (['href', 'url', 'uri'] as $property) {
            $value = $this->readNullableScalarLike($action, $property, null);
            if (null !== $value && '' !== $value) {
                return $value;
            }
        }

        $routeName = $this->readNullableScalarLike($action, 'routeName', null);
        if (null !== $routeName && isset($context['url_generator']) && is_callable($context['url_generator'])) {
            $parameters = $this->normalizeArray($this->readMixedLike($action, 'routeParameters', []));
            return (string) $context['url_generator']($routeName, $parameters, $action, $context);
        }

        return '#';
    }

    private function resolveActionVariant(mixed $action): string
    {
        $variant = $this->readNullableScalarLike($action, 'variant', null);
        if (null !== $variant) {
            return $variant;
        }

        $name = strtolower($this->readScalarLike($action, 'name', ''));

        return match ($name) {
            'new', 'create', 'add' => 'primary',
            'delete', 'remove' => 'danger',
            default => 'default',
        };
    }

    private function readIdentifierValue(mixed $crudContext, array $meta): string|int|null
    {
        $value = $this->readMixedLike($crudContext, 'identifierValue', $meta['identifierValue'] ?? null);
        if (is_string($value) || is_int($value) || null === $value) {
            return $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        return null;
    }

    private function readObjectLike(mixed $source, string $field): mixed
    {
        $value = $this->readMixedLike($source, $field, null);
        if (null === $value) {
            return (object) [];
        }

        return $value;
    }

    private function readScalarLike(mixed $source, string $field, string $fallback): string
    {
        $value = $this->readMixedLike($source, $field, $fallback);

        if (is_scalar($value) || null === $value) {
            return (string) $value;
        }

        return $fallback;
    }

    private function readNullableScalarLike(mixed $source, string $field, ?string $fallback): ?string
    {
        $value = $this->readMixedLike($source, $field, $fallback);
        if (null === $value) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return $fallback;
    }

    private function readMixedLike(mixed $source, string $field, mixed $fallback): mixed
    {
        if (is_array($source) && array_key_exists($field, $source)) {
            return $source[$field];
        }

        if (is_object($source)) {
            if (isset($source->{$field}) || property_exists($source, $field)) {
                return $source->{$field};
            }

            $ucfirst = ucfirst($field);
            foreach (["get{$ucfirst}", "is{$ucfirst}", "has{$ucfirst}"] as $method) {
                if (method_exists($source, $method)) {
                    return $source->{$method}();
                }
            }
        }

        return $fallback;
    }

    /** @return array<string, mixed> */
    private function normalizeArray(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /** @return list<mixed> */
    private function normalizeList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values($value);
    }

    /** @return array<string, scalar|null> */
    private function normalizeRow(mixed $value): array
    {
        if (is_array($value)) {
            return $this->scalarRow($value);
        }

        if (is_object($value)) {
            return $this->scalarRow(get_object_vars($value));
        }

        return [];
    }

    /** @param array<string, mixed> $row
     *  @return array<string, scalar|null>
     */
    private function scalarRow(array $row): array
    {
        $result = [];
        foreach ($row as $key => $value) {
            if (is_scalar($value) || null === $value) {
                $result[(string) $key] = $value;
                continue;
            }
            $result[(string) $key] = $this->stringifyValue($value);
        }

        return $result;
    }

    private function stringifyValue(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (is_scalar($item) || null === $item) {
                $result[] = (string) $item;
            }
        }

        return $result;
    }

    private function humanize(string $value): string
    {
        $value = preg_replace('/[_\-.]+/', ' ', $value) ?? $value;
        $value = trim($value);

        return '' === $value ? 'Column' : ucfirst($value);
    }

    private function nullableString(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }
}
