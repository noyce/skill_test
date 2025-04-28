<?php
namespace App\Models;

require_once 'Model.php';
/**
 * Class Employee
 *
 * This model represents an employee in our system.
 */
class Employee extends Model {

    public bool $email_sent = false;

    /**
     * The unique identifier
     */
    public ?int $id = null;

    /**
     * The employee's full name
     */
    public string $name;

    /**
     * The employee's phone number
     */
    public string $phoneNumber;

    /**
     * The employee's password.
     */
    public ?string $password = null;

    /**
     * The employee's email address
     */
    public string $email;

    /**
     * @see self::TYPE_* constants
     */
    public string $type;

    /**
     * Storage for additional metadata fields
     */
    private array $metaData = [];

    /**
     * Full time employee - works 40 hours week+
     */
    const TYPE_FULL_TIME = "full-time";

    /**
     * Part time employee - works < 40 hours week.
     */
    const TYPE_PART_TIME = "part-time";

    /**
     * Male gender
     */
    const GENDER_MALE = "male";

    /**
     * Female gender
     */
    const GENDER_FEMALE = "female";

    /**
     * Save method (this has been hacked to save to the database, but it should be presumed that this would happen in
     * an ORM of some sort - and that the ORM takes care of the DB connection, query, etc.)
     *
     * It should also be assumed that the save method will work nicely for inserting and saving updates to an already existing employee.
     *
     * @return void
     * @throws Exception if we had a problem saving.
     */
    public function save() {
        
        try {
            $this->db->beginTransaction();
            
            $updateId = false;
            
            if (!isset($this->id)) {
                $stmt = $this->db->prepare("INSERT INTO employee (`name`, `phone_number`, `type`, `email`, `password`, `email_sent`) VALUES (:name, :phone, :type, :email, :password, :email_sent)");
                $updateId = true;
            }
            else {
                $stmt = $this->db->prepare("UPDATE employee SET `name` = :name, `phone_number` = :phone ,`type` = :type, `email` = :email, `email_sent` = :email_sent where id = :id");
                $stmt->bindParam(":id", $this->id);
            }
            
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':phone', $this->phoneNumber);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':email', $this->email);
            if(!isset($this->id)){ //set password only if employee is being created
                $stmt->bindParam(':password', $this->password);
            }
            $emailSent = (int) $this->email_sent;
            $stmt->bindParam(':email_sent', $emailSent);

            if (!$stmt->execute()) {
                throw new \Exception("Could not save employee.");
            }
            
            if ($updateId) {
                $this->id = $this->db->lastInsertId();
            }
            
            // Save any metadata
            if (!empty($this->metaData)) {
                foreach ($this->metaData as $key => $value) {
                    $this->saveMetadata($key, $value);
                }
            }
            
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Saves a metadata value for this employee
     * 
     * @param PDO $pdo The database connection
     * @param string $key The metadata key
     * @param mixed $value The metadata value
     * @throws Exception If the metadata could not be saved
     */
    private function saveMetadata(string $key, $value): void {
        // Check if metadata with this key already exists for this employee
        $stmt = $this->db->prepare("SELECT id FROM employee_meta_data WHERE employee_id = :employee_id AND `key` = :key LIMIT 1");
        $stmt->bindParam(':employee_id', $this->id);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        
        $existingMeta = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($existingMeta) {
            // Update existing metadata
            $stmt = $this->db->prepare("UPDATE employee_meta_data SET `value` = :value WHERE id = :id");
            $stmt->bindParam(':id', $existingMeta['id']);
            $stmt->bindParam(':value', $value);
        } else {
            // Insert new metadata
            $stmt = $this->db->prepare("INSERT INTO employee_meta_data (employee_id, `key`, `value`) VALUES (:employee_id, :key, :value)");
            $stmt->bindParam(':employee_id', $this->id);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
        }
        
        if (!$stmt->execute()) {
            throw new \Exception("Could not save employee metadata.");
        }
    }
    
    /**
     * Load metadata for this employee
     * 
     * @return void
     * @throws Exception if metadata could not be loaded
     */
    public function loadMetadata(): void {
        if (!$this->id) {
            return; // Cannot load metadata without an employee ID
        }
        
        $stmt = $this->db->prepare("SELECT `key`, `value` FROM employee_meta_data WHERE employee_id = :employee_id");
        $stmt->bindParam(':employee_id', $this->id);
        
        if (!$stmt->execute()) {
            throw new \Exception("Could not load employee metadata.");
        }
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->metaData[$row['key']] = $row['value'];
        }
    }
    
    /**
     * Magic method to set properties, including metadata
     * 
     * @param string $name The property name 
     * @param mixed $value The property value
     */
    public function __set(string $name, $value): void {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            $this->metaData[$name] = $value;
        }
    }
    
    /**
     * Marks the email as sent and saves the change to the database
     * 
     * @return void
     */
    public function markEmailAsSent(): void {
        $this->email_sent = true;
        $this->save();
    }
}