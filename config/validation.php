<?php

include_once __DIR__ . "/csrf.php";

/**
 * Validates form data ($_POST) based on the specified rules.
 *
 * This function checks the CSRF token, validates each field against rule strings
 * (such as 'required', 'email', 'min:5', 'max:10', 'in:a,b,c'), and handles automatic
 * redirection if validation fails while storing errors and old inputs into the session.
 *
 * @param array<string, string> $rules        List of validation rules in the format `['field_name' => 'rule1|rule2:param']`.
 * @param string|null           $fallbackUrl  Target URL to redirect upon validation failure. If null, uses HTTP_REFERER or root ('/').
 *
 * @return array<string, mixed> Returns the validated $_POST data array on success.
 *
 * @throws void Terminates script execution (`exit`) via header redirection if validation fails.
 */
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

/**
 * Retrieves the first validation error message found for a specific field from the session.
 *
 * @param string $field The name of the form field to check for errors.
 *
 * @return string|null Returns the error message string if it exists, or null if the field is error-free.
 */
function error(string $field): ?string
{
    return $_SESSION['field_errors'][$field][0] ?? null;
}

/**
 * Checks whether a specific field has any validation error messages stored in the session.
 *
 * @param string $field The name of the form field to inspect.
 *
 * @return bool Returns true if errors exist for the field, false otherwise.
 */
function has_error(string $field): bool
{
    return !empty($_SESSION['field_errors'][$field]);
}

/**
 * Retrieves old input data from the previous session to repopulate form fields,
 * safely escaped with HTML entities to prevent XSS vulnerabilities.
 *
 * @param string $field   The name of the form field.
 * @param string $default Optional default value if the old input is not found.
 *
 * @return string Returns the escaped input value string using htmlspecialchars().
 */
function old(string $field, string $default = ''): string
{
    return htmlspecialchars($_SESSION['old_input'][$field] ?? $default, ENT_QUOTES, 'UTF-8');
}

/**
 * Clears all flash session data related to validation errors and old form inputs.
 *
 * @return void
 */
function clear_flash(): void
{
    unset($_SESSION['field_errors'], $_SESSION['old_input']);
}