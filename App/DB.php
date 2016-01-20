<?php

namespace App;

use App\DBConnectionInterface;

//require __DIR__  . '\DBConnectionInterface.php';

Class DB implements DBConnectionInterface
{
    private static $instance = null;

    private $pdo = null;

    private $userPdo = array();

    private $dsn = null;

    private $username = null;

    private $password = null;

    /**
     * Creates a PDO instance representing a connection to a database
     *
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     *
     * @param string $username The user name for the DSN string.
     * @param string $password The password for the DSN string.
     *
     */
    private function __construct($dsn, $username = '', $password = '')
    {
        if (isset($this->userPdo[$dsn."-".$username])) {
            $this->pdo = $this->userPdo[$dsn."-".$username];
        } else {
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->userPdo[$dsn."-".$username] = $this->pdo;
        }		
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    private function __clone(){}

    protected function __sleep(){}

    protected function __wakeUp(){}

    /**
     * Creates new instance representing a connection to a database
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     *
     * @param string $username The user name for the DSN string.
     * @param string $password The password for the DSN string.
     * @throws  PDOException if the attempt to connect to the requested database fails.
     *
     * @return $this DB
     */
    public static function connect($dsn, $username = '', $password = '')
    {       
        self::$instance = new self($dsn, $username, $password);        
        return self::$instance;
    }

    /**
     * Completes the current session connection, and creates a new.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->pdo = null;        
        $this->pdo = new \PDO($this->dsn, $this->username, $this->password);
        $this->userPdo[$this->dsn."-".$this->username] = $this->pdo;        
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO the PDO instance, null if the connection is not established yet
     */
    public function getPdoInstance()
    {
        if ($this->pdo === null && $this->dsn) {
           self::connect($this->dsn, $this->username, $this->password);
        }
        return $this->pdo;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sequenceName name of the sequence object (required by some DBMS)
     *
     * @return string the row ID of the last row inserted, or the last value retrieved from the sequence object
     */
    public function getLastInsertID($sequenceName = '')
    {
        return $this->pdo->lastInsertId($sequenceName);
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close()
    {
        if ($this->pdo !== null) {
            $this->pdo = null;
        }
    }

    /**
     * Sets an attribute on the database handle.
     * Some of the available generic attributes are listed below;
     * some drivers may make use of additional driver specific attributes.
     *
     * @param int $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
        $this->pdo->setAttribute($attribute,$value);
    }

    /**
     * Returns the value of a database connection attribute.
     *
     * @param int $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        $this->pdo->getAttribute($attribute);
    }	
	
    /**
     * Closes the currently active DB connection.
     *
     * @return void
     */
	public function __destruct()
	{
		$this->close();	
	}
}

