<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $ruleString) as $rule) {
                $this->applyRule($field, $value, $rule, $data);
            }
        }

        return $this->errors === [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** @param array<string, mixed> $data */
    private function applyRule(string $field, mixed $value, string $rule, array $data): void
    {
        if ($rule === 'nullable' && ($value === null || $value === '')) {
            return;
        }

        if (str_starts_with($rule, 'min:')) {
            $min = (int) substr($rule, 4);
            if (is_string($value) && strlen($value) < $min) {
                $this->errors[$field] = "Mínimo {$min} caracteres.";
            }

            return;
        }

        if (str_starts_with($rule, 'max:')) {
            $max = (int) substr($rule, 4);
            if (is_string($value) && strlen($value) > $max) {
                $this->errors[$field] = "Máximo {$max} caracteres.";
            }

            return;
        }

        match ($rule) {
            'required' => ($value === null || $value === '') && ($this->errors[$field] = 'Campo obligatorio.'),
            'email' => is_string($value) && !filter_var($value, FILTER_VALIDATE_EMAIL) && ($this->errors[$field] = 'Email inválido.'),
            'numeric' => !is_numeric($value) && $value !== null && $value !== '' && ($this->errors[$field] = 'Debe ser numérico.'),
            default => null,
        };
    }
}
