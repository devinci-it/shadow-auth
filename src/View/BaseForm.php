<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\View;

use DevinciIT\ShadowAuth\Utils\CSRF;

abstract class BaseForm
{
    protected array $requiredFields = [];

    protected array $extraFields = [];

    public function setExtraFields(array $fields): static
    {
        $this->extraFields = $fields;

        return $this;
    }

    public function render(): string
    {
        return $this->renderFieldArray($this->getAllFields());
    }

    protected function getAllFields(): array
    {
        $fields = $this->requiredFields;
        $requiredNames = [];

        foreach ($fields as $field) {
            if (isset($field['name']) && is_string($field['name']) && $field['name'] !== '') {
                $requiredNames[] = $field['name'];
            }
        }

        foreach ($this->extraFields as $field) {
            if (!is_array($field) || !isset($field['name']) || !is_string($field['name'])) {
                continue;
            }

            $name = trim($field['name']);
            if ($name === '' || in_array($name, $requiredNames, true)) {
                continue;
            }

            $fields[] = $field;
        }

        $fields[] = [
            'name' => 'csrf_token',
            'type' => 'hidden',
            'value' => CSRF::token(),
        ];

        return $fields;
    }

    protected function renderFieldArray(array $fields): string
    {
        $html = '';

        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }

            $nameRaw = isset($field['name']) && is_string($field['name']) ? $field['name'] : '';
            if ($nameRaw === '') {
                continue;
            }

            $name = htmlspecialchars($nameRaw, ENT_QUOTES, 'UTF-8');
            $typeRaw = isset($field['type']) && is_string($field['type']) ? $field['type'] : 'text';
            $type = htmlspecialchars($typeRaw, ENT_QUOTES, 'UTF-8');

            $defaultLabel = ucfirst(str_replace('_', ' ', $nameRaw));
            $labelRaw = isset($field['label']) && is_string($field['label']) ? $field['label'] : $defaultLabel;
            $label = htmlspecialchars($labelRaw, ENT_QUOTES, 'UTF-8');

            $autocomplete = '';
            if (isset($field['autocomplete']) && is_string($field['autocomplete'])) {
                $autocomplete = ' autocomplete="' . htmlspecialchars($field['autocomplete'], ENT_QUOTES, 'UTF-8') . '"';
            }

            $required = !empty($field['required']) ? ' required' : '';

            $value = '';
            if (isset($field['value']) && (is_scalar($field['value']) || $field['value'] === null)) {
                $value = ' value="' . htmlspecialchars((string) $field['value'], ENT_QUOTES, 'UTF-8') . '"';
            }

            $extra = '';
            foreach ($field as $key => $attributeValue) {
                if (!is_string($key) || in_array($key, ['name', 'type', 'label', 'autocomplete', 'required', 'value'], true)) {
                    continue;
                }

                if (!preg_match('/^[a-zA-Z_:][a-zA-Z0-9_:\-.]*$/', $key)) {
                    continue;
                }

                if (!(is_scalar($attributeValue) || $attributeValue === null)) {
                    continue;
                }

                $extra .= ' ' . $key . '="' . htmlspecialchars((string) $attributeValue, ENT_QUOTES, 'UTF-8') . '"';
            }

            if ($typeRaw === 'hidden') {
                $html .= '<input name="' . $name . '" type="' . $type . '"' . $value . $extra . ">\n";
                continue;
            }

            $html .= '<label>' . $label . '</label><input name="' . $name . '" type="' . $type . '"' . $autocomplete . $required . $value . $extra . ">\n";
        }

        return $html;
    }
}
