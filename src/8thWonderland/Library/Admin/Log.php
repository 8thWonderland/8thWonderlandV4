<?php

namespace Wonderland\Library\Admin;

use Wonderland\Library\Database\PdoDriver;

class Log {
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages
    /** @var \Wonderland\Library\Database\Connection **/
    protected $connection;
    /** @var array **/
    protected $priorities = [];
    /** @var array **/
    protected $writers = ['DB', 'MAIL', 'FILE'];
    /** @var string **/
    protected $writer;
    
    /**
     * @param \Wonderland\Library\Database\PdoDriver $connection
     */
    public function __construct(PdoDriver $connection) {
        $this->connection = $connection;
    }
    
    /**
     * @param string $writer
     */
    public function setWriter($writer) {
        $this->priorities = array_flip((new \ReflectionClass($this))->getConstants());
        if (in_array(strtoupper($writer), $this->writers)) {
            $this->writer = strtoupper($writer);
        }
    }
    
    /**
     * @param string $message
     * @param int $priority
     */
    public function log($message, $priority) {       
        if ($this->writer === 'DB') {
            $this->connection->exec(
                "INSERT INTO logs (level, msg) VALUES ($priority, '$message')"
            );
        }
    }
        
    /**
     * @return array
     */
    public static function displayDbLogs() {
        return $this->connection->query(
            'SELECT level, timelogs, msg FROM logs ORDER BY timelogs DESC'
        )->fetchAll();
    }
}