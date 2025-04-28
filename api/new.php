<?php
namespace App\Api;

use App\Services\EmployeeCreator;
require_once("../services/EmployeeCreator.php");

/**
 * For the purposes of the test, the way that the data comes in to the system is not relevant. Let's assume that
 * we have a REST API, and this is a POST request to the "new employee" endpoint. The data provided is the array
 * that has been specified here.
 */

//allowed data is "name", "number", "email" & "type"
$receivedData = array(
    "name" => "Bob Smith",
    "email" => "luke.zawadzki@astutepayroll.com",
    "number" => "+61 430 131 409",
    "type" => "full-time"
);

//this simulates a call to the API.
var_dump(handle_API_Request($receivedData));

/**
 * This is the processing code for the API.
 */
function handle_API_Request($data) {
    try {
        // Map the data to match EmployeeCreator's expected format
        $mappedData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['number'],
            'employee_type' => $data['type'],
            'password' => sha1(uniqid()), //TODO: extract into separate service, use secure alogrithm
        ];

        // Use the EmployeeCreator service to handle employee creation
        $employeeCreator = new EmployeeCreator();
        $employee = $employeeCreator->createEmployee($mappedData, 'api');

        return array(
            "status" => "success",
            "message" => $employee->id . " was added"
        );
    }
    catch (\Throwable $e) {
        return array(
            "status" => "error",
            "message" => $e->getMessage()
        );
    }
}
