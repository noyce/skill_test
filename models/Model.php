<?php

namespace App\Models;

include_once '../services/DatabaseProvider.php';

class Model {
    protected \PDO $db;
    public function __construct(){
        $this->db = \DatabaseProvider::getInstance();
    }   
}