<?php

namespace App\Core\Validation;

use App\Http\ErrorBag;

class Validator
{
    protected $data;
    protected $rules;
    protected $errors;
    protected $customMessages = [];
    protected $fieldRulesList = [];

    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
        $this->errors = new ErrorBag();
    }

    public function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = $this->parseRules($ruleString, $field);
            $value = $this->getValue($field);
            foreach ($rules as $ruleObj) {
                if (!$ruleObj->passes($field, $value, $this->data)) {
                    $message = $this->getMessage($field, $ruleObj);
                    $this->errors->add($field, $message);
                    break; // یک خطا برای هر فیلد کافی است
                }
            }
        }
        return $this;
    }

    public function fails()
    {
        return !empty($this->errors->all());
    }

    public function errors()
    {
        return $this->errors;
    }

    public function validated()
    {
        if ($this->fails()) {
            throw new \Exception('Validation failed, cannot get validated data.');
        }
        // بازگرداندن فقط فیلدهایی که قوانین دارند (اختیاری)
        return array_intersect_key($this->data, $this->rules);
    }

    protected function parseRules($ruleString, $field)
    {
        $rules = [];
        $parts = explode('|', $ruleString);
        foreach ($parts as $part) {
            $ruleObj = $this->createRule($part, $field);
            if ($ruleObj) {
                $rules[] = $ruleObj;
                // ذخیره نام قانون
                $ruleName = explode(':', $part)[0];
                $this->fieldRulesList[$field][] = $ruleName;
            }
        }
        return $rules;
    }

    protected function createRule($rule, $field)
    {
        if (strpos($rule, ':') !== false) {
            list($name, $params) = explode(':', $rule, 2);
            $params = explode(',', $params);
        } else {
            $name = $rule;
            $params = [];
        }

        switch ($name) {
            case 'required':
                return new Rules\Required();

            case 'integer':
                return new Rules\Integer();

            case 'string':
                return new Rules\StringRule();

            case 'email':
                return new Rules\Email();

            case 'min':
                $hasInteger = in_array('integer', $this->fieldRulesList[$field] ?? []);
                return new Rules\Min($params[0], $hasInteger);

            case 'max':
                $hasInteger = in_array('integer', $this->fieldRulesList[$field] ?? []);
                return new Rules\Max($params[0], $hasInteger);

            case 'confirmed':
                return new Rules\Confirmed();

            case 'in':
                return new Rules\In($params);

            case 'not_in':
                return new Rules\NotIn($params);

            case 'file':
                return new Rules\File();

            case 'date':
                return new Rules\Date();

            case 'url':
                return new Rules\Url();

            case 'boolean':
                return new Rules\Boolean();

            case 'unique':
                $table = $params[0];
                $column = $params[1] ?? $field;
                $except = $params[2] ?? null;
                return new Rules\Unique($table, $column, $except);

            case 'required_if':
                $otherField = $params[0];
                $requiredValue = $params[1];
                return new Rules\RequiredIf($otherField, $requiredValue);

            case 'required_with':
                return new Rules\RequiredWith($params);

            case 'required_without':
                return new Rules\RequiredWithout($params);

            case 'different':
                return new Rules\Different($params[0]);

            case 'same':
                return new Rules\Same($params[0]);

            case 'prohibited':
                return new Rules\Prohibited();

            case 'exists':
                $table = $params[0];
                $column = $params[1] ?? $field;
                return new Rules\Exists($table, $column);

            case 'regex':
                return new Rules\Regex($params[0]);
                
            default:
                return null; // قانون ناشناخته نادیده گرفته شود
        }
    }

    protected function getValue($field)
    {
        return $this->data[$field] ?? null;
    }

    protected function getMessage($field, $ruleObj)
    {
        $ruleClass = (new \ReflectionClass($ruleObj))->getShortName();
        $customKey = $field . '.' . strtolower($ruleClass);
        if (isset($this->customMessages[$customKey])) {
            return $this->customMessages[$customKey];
        }
        return $ruleObj->message($field);
    }
}
