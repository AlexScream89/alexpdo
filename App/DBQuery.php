<?php

namespace App;

use App\DBQueryInterface;

Class DBQuery implements DBQueryInterface
{
    private $timeBefore;

    private $timeAfter;

    private $queryTime;

    private $connect;

    /**
     * Create new instance DBQuery.
     *
     * @param DBConnectionInterface $DBConnection
     */
    public function __construct(DBConnectionInterface $DBConnection)
    {
        $this->connect = $DBConnection;
	}

    /**
     * Returns the DBConnection instance.
     *
     * @return DBConnectionInterface
     */
    public function getDBConnection()
    {
        return $this->connect;
    }

    /**
     * Change DBConnection.
     *
     * @param DBConnectionInterface $DBConnection
     *
     * @return void
     */
    public function setDBConnection(DBConnectionInterface $DBConnection)
    {
        $this->connect = $DBConnection;
    }

    /**
     * @param $query
     * @param null $params
     * @return mixed
     */
    public function queryMain($query, $params = null)
	{
		$this->timeBefore = microtime(true);
        $sth = $this->connect->getPdoInstance()->prepare($query);       
        $sth->execute($params);
		$this->timeAfter = microtime(true);
		return $sth;	
	}

    /**
     * Executes the SQL statement and returns query result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed if successful, returns a PDOStatement on error false
     */
    public function query($query, $params = null)
    {
		$sth = $this->queryMain($query, $params);	
        return $sth->fetch();
    }

    /**
     * Executes the SQL statement and returns all rows of a result set as an associative array
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryAll($query, array $params = null)
    {
		$sth = $this->queryMain($query, $params);				
        return $sth->fetchAll();
    }

    /**
     * Executes the SQL statement returns the first row of the query result
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryRow($query, array $params = null)
    {
		$sth = $this->queryMain($query, $params);	
        return $sth->fetch();
    }

    /**
     * Executes the SQL statement and returns the first column of the query result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryColumn($query, array $params = null)
    {
		$sth = $this->queryMain($query, $params);		
        return $sth->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * Executes the SQL statement and returns the first field of the first row of the result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed  column value
     */
    public function queryScalar($query, array $params = null)
    {
        $sth = $this->queryMain($query, $params);
		return $sth->fetchColumn(0);	
    }

    /**
     * Executes the SQL statement.
     * This method is meant only for executing non-query SQL statement.
     * No result set will be returned.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return integer number of rows affected by the execution.
     */
    public function execute($query, array $params = null)
    {
        $sth = $this->queryMain($query, $params);
        return $sth->rowCount();
    }

    /**
     * Returns the last query execution time in seconds
     *
     * @return float query time in seconds
     */
    public function getLastQueryTime()
    {
        $this->queryTime = $this->timeAfter - $this->timeBefore;
        return $this->queryTime;
    }

}

