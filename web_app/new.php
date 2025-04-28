<?php
// Start session at the very beginning before any output
session_start();

namespace App\WebApp;

use App\Models\Employee;
use App\Models\AuditLog;
use App\Services\Validation\CreateEmployeeValidator;
use App\Services\MailSender;
use App\Services\CsvAppender;
use App\Services\HttpRedirector;
use App\Services\EmployeeCreator;
use App\Services\SessionManager;

include_once '../services/DatabaseProvider.php';
include_once '../models/Model.php';
include_once '../models/Employee.php';
include_once '../models/AuditLog.php';
include_once '../services/MailSender.php';
include_once '../services/CsvAppender.php';
include_once '../services/HttpRedirector.php';
include_once '../services/EmployeeCreator.php';
include_once '../services/SessionManager.php';


class EmployeeRegistrationController{

    private EmployeeCreator $employeeCreator;
    
    public function __construct(){
        $this->employeeCreator = new EmployeeCreator();
    }

    public function processRequest(): void
    {
        if($_POST){ //form submission
            $this->processPostRequest($_POST);
        }else{ //form rendering
            $this->processGetRequest();
        }
    }

    private function processGetRequest()
    {
        include_once '../templates/createEmployee.html.php';
        exit();
    }

    private function processPostRequest(array $requestBody): void
    {
        try{
            // Use the EmployeeCreator service to handle employee creation
            $employee = $this->employeeCreator->createEmployee($requestBody, 'web');

            $_SESSION['logged_in_user_id'] = $employee->id;
            
            // Redirect to dashboard using HttpRedirector service
            HttpRedirector::redirect("dashboard.php");
        }catch(\Throwable $e){
            echo $e->getMessage();
            exit();
        }
    }
}

$newEmployee = new EmployeeRegistrationController();
$newEmployee->processRequest();
exit();



