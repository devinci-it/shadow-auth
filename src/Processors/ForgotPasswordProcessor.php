<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Processors;

use Devinci\ShadowAuth\Core\Flash;
use Devinci\ShadowAuth\Facade\Auth;
use Devinci\ShadowAuth\Utils\CSRF;

final class ForgotPasswordProcessor extends BaseProcessor
{
    public function __construct(private readonly string $resetPageUrl)
    {
    }

    public function handle(): void
    {
        if (!$this->isPost()) {
            return;
        }

        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            Flash::set('Invalid CSRF token.');
            return;
        }

        $identifier = trim((string) ($_POST['login_identifier'] ?? ''));
        if ($identifier === '') {
            Flash::set('Username or email is required.');
            return;
        }

        $token = Auth::requestPasswordResetToken($identifier);
        $message = 'If the account exists, a password reset link has been generated.';

        // This project is a demo app, so we surface the direct URL instead of sending email.
        if ($token !== null) {
            $message .= ' Demo reset link: ' . $this->buildResetUrl($token);
        }

        Flash::set($message);
    }

    private function buildResetUrl(string $token): string
    {
        $separator = str_contains($this->resetPageUrl, '?') ? '&' : '?';

        return $this->resetPageUrl . $separator . 'token=' . rawurlencode($token);
    }
}
