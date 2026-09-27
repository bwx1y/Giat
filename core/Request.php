<?php

namespace core;

class Request
{
    private array $vars;

    public function __construct(array $vars) {
        $this->vars = $vars;
    }

    public function all(): array
    {
        return $this->vars;
    }

    public function get(string $key) {
        return $this->vars[$key] ?? null;
    }

    public function validate(array $rules): void
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $param = null;
                if (str_contains($rule, ':')) {
                    list($rule, $param) = explode(':', $rule, 2);
                }

                if ($rule === 'required' && (empty($value) && $value !== '0' && $value !== 0)) {
                    $errors[$field] = "The {$field} field is required.";
                    break;
                }

                if (empty($value) && $value !== '0' && $value !== 0 && !in_array('required', $rulesArray)) {
                    continue;
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "The {$field} must be a valid email address.";
                }

                if ($rule === 'min' && strlen((string)$value) < (int)$param) {
                    $errors[$field] = "The {$field} must be at least {$param} characters.";
                }
            }
        }

        if (!empty($errors)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 400,
                'message' => "Validation error",
                'error'  => $errors
            ]);
            exit;
        }
    }
}