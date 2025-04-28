<?php

namespace App\Services;

include_once '../services/validation/CreateEmployeeValidator.php';
include_once '../services/MailSender.php';
include_once '../services/CsvAppender.php';
include_once '../models/AuditLog.php';

use App\Models\Employee;
use App\Models\AuditLog;
use App\Services\Validation\CreateEmployeeValidator;
use App\Services\MailSender;
use App\Services\CsvAppender;

/**
 * Class EmployeeCreator
 * 
 * This service handles the employee creation process
 */
class EmployeeCreator
{
    private CreateEmployeeValidator $validator;
    private MailSender $mailSender;
    private CsvAppender $csvAppender;

    public function __construct()
    {
        $this->validator = new CreateEmployeeValidator();
        $this->mailSender = new MailSender();
        $this->csvAppender = new CsvAppender();
    }

    /**
     * Creates a new employee from the provided data
     * 
     * @param array $data The employee data
     * @param string $context The validation context (e.g., 'web', 'api')
     * @return Employee The created employee
     * @throws \Exception If the data is invalid or employee creation fails
     */
    public function createEmployee(array $data, string $context = 'web'): Employee
    {
        // Validate the data
        $isValid = $this->validator->validate($data, $context);
        if (!$isValid) {
            throw new \Exception("Invalid employee data: " . implode(', ', $this->validator->getErrors()));
        }

        // Create and save the employee
        $employee = new Employee();
        $employee->name = $data['name'];
        $employee->phoneNumber = $data['phone_number'];
        $employee->email = $data['email'];
        $employee->type = $data['employee_type'];
        isset($data['password']) && $employee->password = $data['password'];
        isset($data['gender']) && $employee->gender = $data['gender'];
        $employee->save();

        // Create audit log entry
        $timestamp = date("d/m/y h:i:s");
        $logMessage = "{$data['name']} was added on $timestamp";
        AuditLog::log($logMessage);

        // can be made async in future
        $this->sendRegistrationEmail($employee);
        $this->addToCsvReport($employee);

        return $employee;
    }

    /**
     * Sends a registration email to the employee
     * 
     * @param Employee $employee The employee to send the email to
     * @return bool Whether the email was sent successfully
     */
    private function sendRegistrationEmail(Employee $employee): bool
    {
        $mailSent = $this->mailSender->sendRegistrationEmail(
            $employee->email,
            $employee->name,
            $employee->password
        );

        if ($mailSent) {
            $employee->markEmailAsSent();
        }

        return $mailSent;
    }

    /**
     * Adds the employee to the CSV report
     * 
     * @param Employee $employee The employee to add to the report
     * @return void
     */
    private function addToCsvReport(Employee $employee): void
    {
        $this->csvAppender->appendEmployee(
            $employee->id ?? null,
            $employee->name,
            $employee->email
        );
    }
}
