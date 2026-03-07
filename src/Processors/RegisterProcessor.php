<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Processors;

use Devinci\ShadowAuth\Core\Flash;
use Devinci\ShadowAuth\Facade\Auth;
use Devinci\ShadowAuth\Utils\CSRF;

final class RegisterProcessor extends BaseProcessor
{
    public function __construct(
        private readonly string $loginRedirect = '/views/login.php',
        private readonly array $extraFields = []
    )
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

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($username === '' || $password === '') {
            Flash::set('Username and password are required.');
            return;
        }

        if ($password !== $confirmPassword) {
            Flash::set('Password confirmation does not match.');
            return;
        }

        if (strlen($password) < 8) {
            Flash::set('Password must be at least 8 characters.');
            return;
        }

        $attributes = [];

        foreach ($this->extraFields as $field) {
            if (!is_array($field) || !isset($field['name']) || !is_string($field['name'])) {
                continue;
            }

            $name = trim($field['name']);
            if ($name === '' || in_array($name, ['username', 'password', 'confirm_password', 'csrf_token'], true)) {
                continue;
            }

            $required = (bool) ($field['required'] ?? false);
            $type = strtolower((string) ($field['type'] ?? 'text'));
            $value = trim((string) ($_POST[$name] ?? ''));

            if ($required && $value === '') {
                Flash::set(sprintf('%s is required.', ucfirst(str_replace('_', ' ', $name))));
                return;
            }

            if ($type === 'email' && $value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                Flash::set('Please provide a valid email address.');
                return;
            }

            $attributes[$name] = $value;
        }

        if (!Auth::registerWithData($username, $password, $attributes)) {
            Flash::set(Auth::registrationError() ?? 'Registration failed.');
            return;
        }

        Flash::set('Registration successful. You can login now.');
        $this->redirect($this->loginRedirect);
    }
}
