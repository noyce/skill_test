<?php

namespace App\Models;

/**
 * Class AuditLog
 * 
 * This class adds an audit log entry to the database
 */
class AuditLog extends Model {

    private string $message;
    
    /**
     * Creates a new audit log entry
     * 
     * @param string $message The message to log
     * @return AuditLog The created audit log instance
     */
    public static function log(string $message): AuditLog {
        $log = new self();
        $log->message = $message;
        $log->save();
        return $log;
    }
    
    /**
     * Save method
     * 
     * @return void
     * @throws \Exception if we had a problem saving.
     */
    private function save(): void {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("INSERT INTO audit_log (`message`) VALUES (:message)");
            $stmt->bindParam(':message', $this->message);
            
            if (!$stmt->execute()) {
                throw new \Exception("Could not save audit log.");
            }
            
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}