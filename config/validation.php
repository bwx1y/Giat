<?php

include_once __DIR__ . "/csrf.php";

function validate(array $rules, ?string $fallbackUrl = null): array
{
    $data = $_POST;
    $errors = [];

    if (!verify_csrf()) {
        $errors['csrf_token'][] = 'Token CSRF tidak valid atau sudah kadaluarsa.';
    }

    foreach ($rules as $field => $ruleString) {
        $ruleList = explode('|', $ruleString);
        $value = $data[$field] ?? null;

        foreach ($ruleList as $rule) {
            $params = [];
            if (str_contains($rule, ':')) {
                list($ruleName, $paramStr) = explode(':', $rule, 2);
                $params = explode(',', $paramStr);
            } else {
                $ruleName = $rule;
            }

            if ($ruleName === 'required') {
                if (is_null($value) || trim((string)$value) === '') {
                    $errors[$field][] = "Field {$field} wajib diisi.";
                    break;
                }
            }

            if (!is_null($value) && trim((string)$value) !== '') {
                if ($ruleName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "Field {$field} harus berupa email yang valid.";
                }

                if ($ruleName === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = "Field {$field} harus berupa angka.";
                }

                if ($ruleName === 'min') {
                    $min = (int)($params[0] ?? 0);
                    if (is_numeric($value)) {
                        if ($value < $min) {
                            $errors[$field][] = "Field {$field} minimal bernilai {$min}.";
                        }
                    } else {
                        if (mb_strlen((string)$value) < $min) {
                            $errors[$field][] = "Field {$field} minimal {$min} karakter.";
                        }
                    }
                }

                if ($ruleName === 'max') {
                    $max = (int)($params[0] ?? 0);
                    if (is_numeric($value)) {
                        if ($value > $max) {
                            $errors[$field][] = "Field {$field} maksimal bernilai {$max}.";
                        }
                    } else {
                        if (mb_strlen((string)$value) > $max) {
                            $errors[$field][] = "Field {$field} maksimal {$max} karakter.";
                        }
                    }
                }

                if ($ruleName === 'in' && !in_array((string)$value, $params, true)) {
                    $errors[$field][] = "Field {$field} harus salah satu dari: " . implode(', ', $params);
                }
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['field_errors'] = $errors;
        $_SESSION['old_input'] = $data;

        $redirectTo = $fallbackUrl ?? $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: " . $redirectTo);
        exit;
    }

    unset($_SESSION['field_errors'], $_SESSION['old_input']);

    return $data;
}

function error(string $field): ?string
{
    return $_SESSION['field_errors'][$field][0] ?? null;
}

function has_error(string $field): bool
{
    return !empty($_SESSION['field_errors'][$field]);
}

function old(string $field, string $default = ''): string
{
    return htmlspecialchars($_SESSION['old_input'][$field] ?? $default, ENT_QUOTES, 'UTF-8');
}

function clear_flash(): void
{
    unset($_SESSION['field_errors'], $_SESSION['old_input']);
}