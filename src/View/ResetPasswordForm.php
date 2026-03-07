<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\View;

final class ResetPasswordForm extends BaseForm
{
    protected array $requiredFields = [
        [
            'name' => 'reset_token',
            'type' => 'hidden',
            'value' => '',
        ],
        [
            'name' => 'password',
            'type' => 'password',
            'label' => 'New Password',
            'autocomplete' => 'new-password',
            'required' => true,
        ],
        [
            'name' => 'confirm_password',
            'type' => 'password',
            'label' => 'Confirm New Password',
            'autocomplete' => 'new-password',
            'required' => true,
        ],
    ];

    public function setToken(string $token): static
    {
        $this->requiredFields[0]['value'] = $token;

        return $this;
    }
}
