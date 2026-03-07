<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\View;

final class ForgotPasswordForm extends BaseForm
{
    protected array $requiredFields = [
        [
            'name' => 'login_identifier',
            'type' => 'text',
            'label' => 'Username or Email',
            'autocomplete' => 'username',
            'required' => true,
        ],
    ];
}
