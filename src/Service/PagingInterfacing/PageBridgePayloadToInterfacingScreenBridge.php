<?php

declare(strict_types=1);

namespace App\Bridging\Service\PagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\ServiceInterface\PagingInterfacing\PageBridgePayloadToInterfacingScreenBridgeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class PageBridgePayloadToInterfacingScreenBridge implements PageBridgePayloadToInterfacingScreenBridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {}

    public function supports(object $payload, string $target): bool
    {
        return BridgeTarget::SCREEN_PAGING_PAGE === $target
            && $this->isPageBridgePayload($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Paging → Interfacing page screen bridge.');
        }

        return $this->bridgeToScreen($payload, $context);
    }

    /**
     * Converts the Paging outbound page bridge contract into the canonical Interfacing screen payload.
     *
     * The source payload is deliberately shape-read instead of type-imported from Paging. This keeps Bridging usable
     * as the sibling connector even when the host application has not installed or booted Paging yet.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array
    {
        $page = $this->pagePayloadToArray($payload);

        $code = $this->stringValue($page, ['code', 'pageCode'], 'page');
        $slug = $this->stringValue($page, ['slug', 'pageSlug'], $code);
        $title = $this->stringValue($page, ['title'], $code);
        $kind = $this->stringValue($page, ['kind', 'pageKind'], 'page');
        $revisionNumber = $this->stringValue($page, ['revisionNumber', 'version', 'versionNumber'], '');
        $publishedAt = $this->stringValue($page, ['publishedAt'], '');
        $effectiveFrom = $this->stringValue($page, ['effectiveFrom'], '');
        $checksum = $this->stringValue($page, ['checksum'], '');
        $renderHints = $this->arrayValue($page, ['renderHints']);
        $legalNotice = $this->arrayValue($page, ['legalNotice']);
        $attachments = $this->normalizeAttachments($page['attachments'] ?? $page['attachmentReferences'] ?? []);

        return $this->payloadNormalizer->normalize([
            'id' => sprintf('page.%s', $code),
            'kind' => 'document',
            'title' => $title,
            'subtitle' => $this->resolveSubtitle($kind, $revisionNumber, $effectiveFrom),
            'eyebrow' => 'Paging · Interfacing',
            'items' => $attachments,
            'itemCount' => count($attachments),
            'facts' => $this->resolveFacts($kind, $revisionNumber, $publishedAt, $effectiveFrom, $checksum),
            'context' => [
                'code' => $code,
                'slug' => $slug,
                'kind' => $kind,
                'status' => $this->stringValue($page, ['status'], ''),
                'publicationStatus' => $this->stringValue($page, ['publicationStatus'], ''),
                'requiresAcceptance' => (bool) ($legalNotice['requiresAcceptance'] ?? false),
            ],
            'meta' => [
                'source' => 'paging.page_bridge_payload',
                'page' => $page,
                'bodyHtml' => $this->stringValue($page, ['bodyHtml'], ''),
                'bodyText' => $this->stringValue($page, ['bodyText'], ''),
                'bodyMarkdown' => $this->stringValue($page, ['bodyMarkdown'], ''),
                'bodyJson' => $page['bodyJson'] ?? null,
                'renderHints' => $renderHints,
                'legalNotice' => $legalNotice,
                'attachments' => $attachments,
            ],
        ], $context);
    }

    private function isPageBridgePayload(object $payload): bool
    {
        if (str_ends_with($payload::class, '\\PageBridgePayload')) {
            return true;
        }

        $page = $this->pagePayloadToArray($payload);
        if ([] === $page) {
            return false;
        }

        $source = strtolower($this->stringValue($page, ['source', 'componentKey'], ''));
        if ('paging.page_bridge_payload' === $source || 'paging' === $source || 'page' === $source) {
            return isset($page['code'], $page['title']);
        }

        return isset($page['code'], $page['slug'], $page['title'])
            && (isset($page['bodyHtml']) || isset($page['bodyText']) || isset($page['bodyMarkdown']) || isset($page['renderHints']));
    }

    /**
     * @return array<string, mixed>
     */
    private function pagePayloadToArray(object $payload): array
    {
        if (method_exists($payload, 'toInterfacingScreenPayload')) {
            $array = $payload->toInterfacingScreenPayload();

            return is_array($array) ? $array : [];
        }

        if (method_exists($payload, 'toArray')) {
            $array = $payload->toArray();

            return is_array($array) ? $array : [];
        }

        $readerMethods = [
            'code' => ['code', 'getCode', 'pageCode', 'getPageCode'],
            'slug' => ['slug', 'getSlug', 'pageSlug', 'getPageSlug'],
            'title' => ['title', 'getTitle'],
            'kind' => ['kind', 'getKind', 'pageKind', 'getPageKind'],
            'status' => ['status', 'getStatus'],
            'publicationStatus' => ['publicationStatus', 'getPublicationStatus'],
            'revisionNumber' => ['revisionNumber', 'getRevisionNumber', 'version', 'getVersion'],
            'bodyHtml' => ['bodyHtml', 'getBodyHtml'],
            'bodyText' => ['bodyText', 'getBodyText'],
            'bodyMarkdown' => ['bodyMarkdown', 'getBodyMarkdown'],
            'bodyJson' => ['bodyJson', 'getBodyJson'],
            'checksum' => ['checksum', 'getChecksum'],
            'publishedAt' => ['publishedAt', 'getPublishedAt'],
            'effectiveFrom' => ['effectiveFrom', 'getEffectiveFrom'],
            'expiresAt' => ['expiresAt', 'getExpiresAt'],
            'attachments' => ['attachments', 'getAttachments', 'attachmentReferences', 'getAttachmentReferences'],
            'renderHints' => ['renderHints', 'getRenderHints'],
            'legalNotice' => ['legalNotice', 'getLegalNotice'],
        ];

        $result = [];
        foreach ($readerMethods as $key => $methods) {
            foreach ($methods as $method) {
                if (method_exists($payload, $method)) {
                    $result[$key] = $this->normalizeMixed($payload->{$method}());
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function normalizeAttachments(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $attachments = [];
        foreach ($value as $row) {
            $row = $this->normalizeMixed($row);
            if (!is_array($row)) {
                continue;
            }

            $attachmentId = $this->stringValue($row, ['attachmentId', 'id'], '');
            $attachmentCode = $this->stringValue($row, ['attachmentCode', 'code'], '');
            $usage = $this->stringValue($row, ['usage'], 'inline');
            $label = $this->stringValue($row, ['label', 'title', 'altText'], $attachmentCode ?: $attachmentId);

            $attachments[] = [
                'title' => $label ?: 'Attachment',
                'subtitle' => $usage,
                'meta' => [
                    'attachmentId' => $attachmentId,
                    'attachmentCode' => $attachmentCode,
                    'usage' => $usage,
                    'position' => $this->stringValue($row, ['position'], ''),
                ],
            ];
        }

        return $attachments;
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function resolveFacts(string $kind, string $revisionNumber, string $publishedAt, string $effectiveFrom, string $checksum): array
    {
        $facts = [
            ['label' => 'Kind', 'value' => $kind],
        ];

        if ('' !== $revisionNumber) {
            $facts[] = ['label' => 'Version', 'value' => $revisionNumber];
        }

        if ('' !== $publishedAt) {
            $facts[] = ['label' => 'Published', 'value' => $publishedAt];
        }

        if ('' !== $effectiveFrom) {
            $facts[] = ['label' => 'Effective from', 'value' => $effectiveFrom];
        }

        if ('' !== $checksum) {
            $facts[] = ['label' => 'Checksum', 'value' => $checksum];
        }

        return $facts;
    }

    private function resolveSubtitle(string $kind, string $revisionNumber, string $effectiveFrom): ?string
    {
        $parts = [ucfirst($kind)];

        if ('' !== $revisionNumber) {
            $parts[] = sprintf('v%s', $revisionNumber);
        }

        if ('' !== $effectiveFrom) {
            $parts[] = sprintf('effective %s', $effectiveFrom);
        }

        return implode(' · ', $parts);
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    private function stringValue(array $source, array $keys, string $default): string
    {
        foreach ($keys as $key) {
            $value = $source[$key] ?? null;
            if (is_scalar($value) && '' !== trim((string) $value)) {
                return (string) $value;
            }
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     *
     * @return array<string, mixed>
     */
    private function arrayValue(array $source, array $keys): array
    {
        foreach ($keys as $key) {
            $value = $source[$key] ?? null;
            if (is_array($value)) {
                return $value;
            }
        }

        return [];
    }

    /**
     * @return array<string, mixed>|list<mixed>|scalar|null
     */
    private function normalizeMixed(mixed $value): mixed
    {
        if (is_array($value) || null === $value || is_scalar($value)) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::ATOM);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            $array = $value->toArray();

            return is_array($array) ? $array : [];
        }

        if (is_object($value)) {
            $result = [];
            foreach (get_object_vars($value) as $key => $item) {
                $result[$key] = $this->normalizeMixed($item);
            }

            return $result;
        }

        return null;
    }
}
