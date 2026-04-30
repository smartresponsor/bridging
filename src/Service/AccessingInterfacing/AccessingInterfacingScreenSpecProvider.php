<?php

declare(strict_types=1);

namespace App\Bridging\Service\AccessingInterfacing;

use App\Accessing\Dto\PageView;

final class AccessingInterfacingScreenSpecProvider
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private const SCREEN_MAP = [
        'account.overview' => [
            'title' => 'Account overview',
            'subtitle' => 'Registration, trust, recovery, and active session state.',
            'kind' => 'overview',
            'eyebrow' => 'Accessing · Account',
            'primary_actions' => [
                ['label' => 'Change password', 'route' => 'accessing_account_password', 'variant' => 'primary'],
                ['label' => 'Sessions', 'route' => 'accessing_sessions'],
                ['label' => 'Security events', 'route' => 'accessing_security_events'],
            ],
        ],
        'account.verify_email' => [
            'title' => 'Verify email',
            'subtitle' => 'Confirm email ownership with the most recent challenge code.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Verification',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
            'secondary_actions' => [
                ['label' => 'Resend code', 'route' => 'accessing_verify_email_resend', 'method' => 'post', 'variant' => 'secondary'],
            ],
        ],
        'account.verify_phone_request' => [
            'title' => 'Verify phone',
            'subtitle' => 'Send a phone verification code for recovery and sign-in hardening.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Verification',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
        ],
        'account.verify_phone_confirm' => [
            'title' => 'Confirm phone verification',
            'subtitle' => 'Confirm the phone challenge that was just issued.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Verification',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
        ],
        'account.second_factor' => [
            'title' => 'Second factor',
            'subtitle' => 'Manage enrollment, activation, and recovery posture for the account.',
            'kind' => 'second_factor',
            'eyebrow' => 'Accessing · Hardening',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
            'secondary_actions' => [
                ['label' => 'Sessions', 'route' => 'accessing_sessions'],
            ],
        ],
        'account.sessions' => [
            'title' => 'Sessions',
            'subtitle' => 'Review active and historical session inventory for the account.',
            'kind' => 'sessions',
            'eyebrow' => 'Accessing · Sessions',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
            'secondary_actions' => [
                ['label' => 'Invalidate other sessions', 'route' => 'accessing_sessions_invalidate_others', 'method' => 'post', 'variant' => 'danger'],
            ],
        ],
        'account.password' => [
            'title' => 'Change password',
            'subtitle' => 'Rotate the account credential and keep the trust posture current.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Credential',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
        ],
        'account.operator_index' => [
            'title' => 'Operator accounts',
            'subtitle' => 'Support-facing visibility into account verification and security posture.',
            'kind' => 'operator_accounts',
            'eyebrow' => 'Accessing · Operator',
            'primary_actions' => [
                ['label' => 'Global security events', 'route' => 'accessing_operator_security_events', 'variant' => 'primary'],
            ],
        ],
        'account.operator_detail' => [
            'title' => 'Operator account detail',
            'subtitle' => 'Inspect the selected account together with its recent security activity.',
            'kind' => 'operator_account_detail',
            'eyebrow' => 'Accessing · Operator',
            'primary_actions' => [
                ['label' => 'Back to accounts', 'route' => 'accessing_operator_accounts'],
                ['label' => 'Global security events', 'route' => 'accessing_operator_security_events'],
            ],
        ],
        'account.register' => [
            'title' => 'Sign up',
            'subtitle' => 'Create an account and enter the trust lifecycle.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Entry',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Sign in', 'route' => 'accessing_sign_in'],
            ],
            'secondary_actions' => [
                ['label' => 'Reset password', 'route' => 'accessing_reset_password_request'],
            ],
        ],
        'account.sign_in' => [
            'title' => 'Sign in',
            'subtitle' => 'Use your account credentials. If second factor is enabled, a follow-up challenge will appear.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Entry',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Sign up', 'route' => 'accessing_register'],
            ],
            'secondary_actions' => [
                ['label' => 'Recover account', 'route' => 'accessing_recover_request'],
                ['label' => 'Forgot password?', 'route' => 'accessing_reset_password_request'],
            ],
        ],
        'account.second_factor_challenge' => [
            'title' => 'Second factor challenge',
            'subtitle' => 'Complete the follow-up verification step for the sign-in flow.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Entry',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Back to sign in', 'route' => 'accessing_sign_in'],
            ],
        ],
        'account.recover_request' => [
            'title' => 'Recover account',
            'subtitle' => 'Start account recovery using the configured identity recovery flow.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Recovery',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Sign in', 'route' => 'accessing_sign_in'],
            ],
            'secondary_actions' => [
                ['label' => 'Forgot password?', 'route' => 'accessing_reset_password_request'],
            ],
        ],
        'account.recover_reset' => [
            'title' => 'Complete account recovery',
            'subtitle' => 'Provide the issued recovery challenge and set a fresh password.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Recovery',
            'form_variable' => 'form',
            'primary_actions' => [
                ['label' => 'Sign in', 'route' => 'accessing_sign_in'],
            ],
        ],
        'security_event.index' => [
            'title' => 'Security events',
            'subtitle' => 'Latest recorded security events for the current account scope.',
            'kind' => 'security_events',
            'eyebrow' => 'Accessing · Audit',
            'primary_actions' => [
                ['label' => 'Overview', 'route' => 'accessing_overview'],
            ],
        ],
        'security_event.operator_index' => [
            'title' => 'Operator security events',
            'subtitle' => 'Global security event inventory for support and operations.',
            'kind' => 'operator_security_events',
            'eyebrow' => 'Accessing · Operator',
            'primary_actions' => [
                ['label' => 'Back to accounts', 'route' => 'accessing_operator_accounts'],
            ],
        ],
        'reset_password.request' => [
            'title' => 'Forgot password',
            'subtitle' => 'Request a reset link for an existing account.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Recovery',
            'form_variable' => 'request_form',
            'primary_actions' => [
                ['label' => 'Sign in', 'route' => 'accessing_sign_in'],
            ],
            'secondary_actions' => [
                ['label' => 'Recover account', 'route' => 'accessing_recover_request'],
            ],
        ],
        'reset_password.check_email' => [
            'title' => 'Check reset request',
            'subtitle' => 'If the account exists, a reset link has been prepared.',
            'kind' => 'info',
            'eyebrow' => 'Accessing · Recovery',
            'primary_actions' => [
                ['label' => 'Back to sign in', 'route' => 'accessing_sign_in'],
            ],
        ],
        'reset_password.reset' => [
            'title' => 'New password',
            'subtitle' => 'Complete the password reset with a fresh secret.',
            'kind' => 'form',
            'eyebrow' => 'Accessing · Recovery',
            'form_variable' => 'reset_form',
            'primary_actions' => [
                ['label' => 'Sign in', 'route' => 'accessing_sign_in'],
            ],
        ],
    ];

    /**
     * @return array<string, mixed>
     */
    public function resolve(PageView $pageView, array $context = []): array
    {
        $spec = self::SCREEN_MAP[$pageView->view] ?? null;

        if (null === $spec) {
            throw new \LogicException(sprintf('No Accessing → Interfacing screen specification configured for "%s".', $pageView->view));
        }

        return [
            'id' => $pageView->view,
            'title' => $spec['title'],
            'subtitle' => $spec['subtitle'],
            'kind' => $spec['kind'],
            'eyebrow' => $spec['eyebrow'],
            'formVariable' => $spec['form_variable'] ?? null,
            'primaryActions' => $spec['primary_actions'] ?? [],
            'secondaryActions' => $spec['secondary_actions'] ?? [],
            'context' => $pageView->parameters,
        ];
    }
}
