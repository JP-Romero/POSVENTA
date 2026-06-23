<?php

namespace App\Helpers;

class Validator
{
    private array $data = [];
    private array $errors = [];
    private array $rules = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->setRules($rules);
        return $validator;
    }

    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $ruleSet) {
            $rules = is_array($ruleSet) ? $ruleSet : explode('|', $ruleSet);
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    protected function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;
        $params = [];

        if (strpos($rule, ':') !== false) {
            [$rule, $paramString] = explode(':', $rule);
            $params = explode(',', $paramString);
        }

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "El campo {$field} es requerido");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$field} debe ser un email válido");
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$params[0]) {
                    $this->addError($field, "El campo {$field} debe tener al menos {$params[0]} caracteres");
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$params[0]) {
                    $this->addError($field, "El campo {$field} no puede exceder {$params[0]} caracteres");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "El campo {$field} debe ser numérico");
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->addError($field, "El campo {$field} solo puede contener letras");
                }
                break;

            case 'alpha_num':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->addError($field, "El campo {$field} solo puede contener letras y números");
                }
                break;

            case 'unique':
                // Custom validation - requires database check
                // Should be handled separately in service layer
                break;
        }
    }

    protected function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function validated(): array
    {
        $result = [];
        foreach (array_keys($this->rules) as $field) {
            if (isset($this->data[$field])) {
                $result[$field] = $this->data[$field];
            }
        }
        return $result;
    }
}
