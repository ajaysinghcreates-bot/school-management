<?php
class Validator {
    private $errors = [];
    private $data = [];
    private $rules = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    public function setData($data) {
        $this->data = $data;
        $this->errors = [];
        return $this;
    }

    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }

    public function validate() {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            $fieldRules = is_string($rules) ? explode('|', $rules) : $rules;

            foreach ($fieldRules as $rule) {
                if (!$this->validateRule($field, $value, $rule)) {
                    break; // Stop validating this field if one rule fails
                }
            }
        }

        return empty($this->errors);
    }

    private function validateRule($field, $value, $rule) {
        $ruleParts = explode(':', $rule, 2);
        $ruleName = $ruleParts[0];
        $ruleParam = $ruleParts[1] ?? null;

        $method = 'validate' . ucfirst($ruleName);

        if (!method_exists($this, $method)) {
            throw new Exception("Validation rule '{$ruleName}' does not exist");
        }

        $valid = $this->$method($field, $value, $ruleParam);

        if (!$valid) {
            $this->addError($field, $ruleName, $ruleParam);
        }

        return $valid;
    }

    public function addError($field, $rule, $param = null) {
        $message = $this->getErrorMessage($field, $rule, $param);
        $this->errors[$field][] = $message;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getFirstError($field = null) {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }

        return null;
    }

    public function hasErrors($field = null) {
        if ($field) {
            return isset($this->errors[$field]);
        }

        return !empty($this->errors);
    }

    private function getErrorMessage($field, $rule, $param = null) {
        $messages = [
            'required' => 'The :field field is required',
            'email' => 'The :field field must be a valid email address',
            'min' => 'The :field field must be at least :param characters',
            'max' => 'The :field field must not exceed :param characters',
            'numeric' => 'The :field field must be a number',
            'integer' => 'The :field field must be an integer',
            'alpha' => 'The :field field must contain only letters',
            'alpha_numeric' => 'The :field field must contain only letters and numbers',
            'url' => 'The :field field must be a valid URL',
            'date' => 'The :field field must be a valid date',
            'in' => 'The :field field must be one of: :param',
            'unique' => 'The :field field must be unique',
        ];

        $message = $messages[$rule] ?? "The :field field is invalid";

        return str_replace([':field', ':param'], [$field, $param], $message);
    }

    // Validation rules
    private function validateRequired($field, $value, $param) {
        return !empty($value) || $value === '0' || $value === 0;
    }

    private function validateEmail($field, $value, $param) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin($field, $value, $param) {
        if (is_numeric($value)) {
            return $value >= $param;
        }
        return strlen($value) >= $param;
    }

    private function validateMax($field, $value, $param) {
        if (is_numeric($value)) {
            return $value <= $param;
        }
        return strlen($value) <= $param;
    }

    private function validateNumeric($field, $value, $param) {
        return is_numeric($value);
    }

    private function validateInteger($field, $value, $param) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateAlpha($field, $value, $param) {
        return ctype_alpha($value);
    }

    private function validateAlphaNumeric($field, $value, $param) {
        return ctype_alnum($value);
    }

    private function validateUrl($field, $value, $param) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateDate($field, $value, $param) {
        $date = date_parse($value);
        return $date['error_count'] === 0 && checkdate($date['month'], $date['day'], $date['year']);
    }

    private function validateIn($field, $value, $param) {
        $options = explode(',', $param);
        return in_array($value, $options);
    }

    private function validateUnique($field, $value, $param) {
        // This would need database access - implement in model
        return true; // Placeholder
    }

    // Sanitization methods
    public static function sanitize($data, $rules = []) {
        $sanitized = [];

        foreach ($data as $field => $value) {
            $fieldRules = $rules[$field] ?? [];

            if (in_array('email', $fieldRules)) {
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_EMAIL);
            } elseif (in_array('url', $fieldRules)) {
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_URL);
            } elseif (in_array('string', $fieldRules)) {
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            } elseif (in_array('int', $fieldRules)) {
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            } elseif (in_array('float', $fieldRules)) {
                $sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            } else {
                $sanitized[$field] = Security::sanitizeInput($value);
            }
        }

        return $sanitized;
    }
}