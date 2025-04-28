<?php

/**
 * DatabaseProvider - A singleton PDO database connection provider
 */
class DatabaseProvider
{
    /**
     * @var PDO|null The PDO instance
     */
    private static ?PDO $instance = null;
    
    /**
     * Database connection settings
     */
    private static array $config = [
        'host' => 'mysql',
        'dbname' => 'test',
        'username' => 'root',
        'password' => '',
        'options' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false
        ]
    ];
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {}
    
    /**
     * Get PDO instance
     * 
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=utf8mb4',
                    self::$config['host'],
                    self::$config['dbname']
                );
                
                self::$instance = new PDO(
                    $dsn,
                    self::$config['username'],
                    self::$config['password'],
                    self::$config['options']
                );
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        
        return self::$instance;
    }
    
    /**
     * Configure database connection settings
     * 
     * @param array $config New configuration to merge with defaults
     * @return void
     */
    public static function configure(array $config): void
    {
        self::$config = array_merge(self::$config, $config);
        self::$instance = null; // Reset the instance to apply new config
    }
} 