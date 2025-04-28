<?php

namespace App\Services;

class CsvAppender {
    private $filePath;
    
    public function __construct($filePath = '/tmp/employee_report.csv') {
        $this->filePath = $filePath;
    }
    
    /**
     * Append employee data to the CSV file
     * 
     * @param int $employeeId The employee ID
     * @param string $employeeName The employee name
     * @param string $employeeEmail The employee email
     * @return bool Success or failure
     */
    public function appendEmployee($employeeId, $employeeName, $employeeEmail) {
        // Create directory if it doesn't exist
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Format the data as CSV
        $line = sprintf("%d,%s,%s\n", 
            $employeeId, 
            str_replace(',', ' ', $employeeName), // Avoid CSV injection
            str_replace(',', ' ', $employeeEmail)
        );
        
        // Append to the file
        return (bool) file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX);
    }
}
