<?php

namespace LightMVC\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = explode('|', $fieldRules);
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    private function applyRule($field, $rule): void
    {
        if ($rule === 'required' && empty($this->data[$field])) {
            $this->errors[$field][] = "The {$field} field is required.";
        }

        if (strpos($rule, 'min:') === 0) {
            $min = substr($rule, 4);
            if (strlen($this->data[$field]) < $min) {
                $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
            }
        }

        if (strpos($rule, 'max:') === 0) {
            $max = substr($rule, 4);
            if (strlen($this->data[$field]) > $max) {
                $this->errors[$field][] = "The {$field} may not be greater than {$max} characters.";
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
