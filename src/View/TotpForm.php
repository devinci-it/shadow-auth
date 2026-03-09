<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\View;

/**
 * Field schema for one-time passcode verification.
 */
final class TotpForm extends BaseForm
{
    protected array $requiredFields = [
        [
            'name' => 'totp_code',
            'type' => 'text',
            'label' => 'TOTP Code',
            'inputmode' => 'numeric',
            'autocomplete' => 'one-time-code',
            'required' => true,
        ],
    ];
}
