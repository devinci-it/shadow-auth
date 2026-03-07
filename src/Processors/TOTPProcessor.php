<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Processors;

use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Facade\Auth;
use DevinciIT\ShadowAuth\Utils\CSRF;

final class TOTPProcessor extends BaseProcessor
{
    public function __construct(
        private readonly string $successRedirect = '/dashboard.php',
        private readonly string $loginRedirect = '/views/login.php'
    ) {
    }

    public function handle(): void
    {
        if (!Auth::isTotpPending()) {
            $this->redirect($this->loginRedirect);
        }

        if (!$this->isPost()) {
            return;
        }

        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            Flash::set('Invalid CSRF token.');
            return;
        }

        $code = preg_replace('/\s+/', '', (string) ($_POST['totp_code'] ?? ''));
        if (!is_string($code) || $code === '') {
            Flash::set('TOTP code is required.');
            return;
        }

        if (!Auth::verifyPendingTotp($code)) {
            Flash::set('Invalid TOTP code.');
            return;
        }

        Flash::set('2FA verification successful.');
        $this->redirect($this->successRedirect);
    }
}
