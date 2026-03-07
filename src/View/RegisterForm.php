<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\View;

final class RegisterForm extends BaseForm
{
    protected array $requiredFields = [
        [
            'name' => 'username',
            'type' => 'text',
            'label' => 'Username',
            'autocomplete' => 'username',
            'required' => true,
        ],
        [
            'name' => 'password',
            'type' => 'password',
            'label' => 'Password',
            'autocomplete' => 'new-password',
            'required' => true,
        ],
        [
            'name' => 'confirm_password',
            'type' => 'password',
            'label' => 'Confirm Password',
            'autocomplete' => 'new-password',
            'required' => true,
        ],
    ];
}
