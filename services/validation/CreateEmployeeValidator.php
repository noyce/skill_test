<?php
namespace App\Services\Validation;
require_once '../models/Employee.php';
use App\Models\Employee;

class CreateEmployeeValidator
{

    private array $errors = [];

    private const VALID_TYPES = [Employee::TYPE_FULL_TIME, Employee::TYPE_PART_TIME];

    protected const REQUIRED_FIELDS = ['name', 'phone_number', 'email', 'employee_type'];
    /**
     * Validates the request payload for creating an employee
     *
     * @param array $data The request payload
     * @return bool Whether the payload is valid
     */
    public function validate(array $data, string $source = 'web'): bool
    {
        $this->errors = [];
        
        // Required fields
        if(!($source === 'web')){
            $requiredFields = self::REQUIRED_FIELDS;
        }else{
            $requiredFields = array_merge(self::REQUIRED_FIELDS, ['password']);
        }


        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->errors[$field] = ucfirst($field) . ' is required';
            }
        }

        // Email validation
        if (isset($data['email']) && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
        }

        // Phone number validation - simple non-empty check for now
        // Can be enhanced with regex pattern for specific phone formats

        // Type validation
        if (isset($data['type']) && !empty($data['type'])) {
            if (!in_array($data['type'], self::VALID_TYPES)) {
                $this->errors['type'] = 'Invalid employee type';
            }
        }

        // password validation, in future should be extended into a separate service to accomodate more complex scenarios
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $this->errors['password'] = 'Password must be at least 8 characters';
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     *
     * @return array The validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
