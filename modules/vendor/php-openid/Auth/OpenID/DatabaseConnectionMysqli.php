<?php

require_once "Auth/OpenID/DatabaseConnection.php";

/**
 * The Auth_OpenID_DatabaseConnection class, which is used to emulate
 * a PEAR database connection.
 *
 * @package OpenID
 * @author JanRain, Inc. <openid@janrain.com>
 * @copyright 2005-2008 Janrain, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache
 */

/**
 * An empty base class intended to emulate PEAR connection
 * functionality in applications that supply their own database
 * abstraction mechanisms.  See {@link Auth_OpenID_SQLStore} for more
 * information.  You should subclass this class if you need to create
 * an SQL store that needs to access its database using an
 * application's database abstraction layer instead of a PEAR database
 * connection.  Any subclass of Auth_OpenID_DatabaseConnection MUST
 * adhere to the interface specified here.
 *
 * @package OpenID
 */
class Auth_OpenID_DatabaseConnectionMysqli extends Auth_OpenID_DatabaseConnection {
	
	private $connection = null;
	
	function __construct($connection)
	{
		$this->connection = $connection;
	}
	
    /**
     * Sets auto-commit mode on this database connection.
     *
     * @param bool $mode True if auto-commit is to be used; false if
     * not.
     */
    function autoCommit($mode)
    {
    	
    }

    /**
     * Run an SQL query with the specified parameters, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return mixed $result The result of calling this connection's
     * internal query function.  The type of result depends on the
     * underlying database engine.  This method is usually used when
     * the result of a query is not important, like a DDL query.
     */
    function query($sql, $params = array(), $type = null)
    {
    	//echo "---------------------------------------<br/><br/>";
    	//echo '$sql '. $sql;
    	//echo "<br/><br/>";
    	
    	//print_r($params);
    	
    	$statement = $this->connection->stmt_init();
    	$statement->prepare($sql);
    	if($type == null)
    	{
	    	$type = '';
	    	foreach($params as $param)
	    	{    		
	    		if(is_string($param))
	    		{
	    			$type .= 's';
	    		}
	    		elseif(is_double($param))
	    		{
	    			$type .= 'd';
	    		}
	    		elseif(is_int($param))
	    		{
	    			$type .= 'i';
	    		}
	    		
	    		    		
	    	}
    	}
    	
    	//echo "<h1>here</h1>";    	
    	//move the type to the front of the array    	
    	array_unshift($params, $type);
    	//echo "<br/>-------------------------------------------<br/>";
    	//echo $sql;
    	//echo "<br/>-------------------------------------------<br/>";
    	//print_r($params);
    	//echo "<br/>-------------------------------------------<br/>";
    	
    	//fancy way of calling with an array as a list of params
    	call_user_func_array(array($statement, 'bind_param'), refValues($params));
    	    	
    	$statement->execute();
    	
    	
    	$retVal = fetch($statement);
    	
    	//print_r($retVal);
    	$statement->close();
    	return $retVal;
    	
    }
    
     

    /**
     * Starts a transaction on this connection, if supported.
     */
    function begin()
    {
    }

    /**
     * Commits a transaction on this connection, if supported.
     */
    function commit()
    {
    }

    /**
     * Performs a rollback on this connection, if supported.
     */
    function rollback()
    {
    }

    /**
     * Run an SQL query and return the first column of the first row
     * of the result set, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return mixed $result The value of the first column of the
     * first row of the result set.  False if no such result was
     * found.
     */
    function getOne($sql, $params = array())
    {
    	$results = $this->query($sql,$params);
    	
    	if(isset($results[0]))
    	{
    		return array_shift($results[0]);
    	}
    	return false;
    }

    /**
     * Run an SQL query and return the first row of the result set, if
     * any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return array $result The first row of the result set, if any,
     * keyed on column name.  False if no such result was found.
     */
    function getRow($sql, $params = array())
    {
    	$results = $this->query($sql,$params);
    	
    	if(isset($results[0]))
    	{
    		return $results[0];
    	}

    	return false;
    }

    /**
     * Run an SQL query with the specified parameters, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return array $result An array of arrays representing the
     * result of the query; each array is keyed on column name.
     */
    function getAll($sql, $params = array())
    {
    	
    	$results = $this->query($sql,$params);
    	
    	return $results;
    	
    }
}

function refValues($arr){ 
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+ 
    { 
        $refs = array(); 
        foreach($arr as $key => $value) 
            $refs[$key] = &$arr[$key]; 
        return $refs; 
    } 
    return $arr; 
} 


function fetch($result)
{
	$array = array();

	if($result instanceof mysqli_stmt)
	{
		$result->store_result();

		$variables = array();
		$data = array();
		$meta = $result->result_metadata();

		if ($meta == null)
		{
			return array();
		}
		while($field = $meta->fetch_field())
			$variables[] = &$data[$field->name]; // pass by reference

		call_user_func_array(array($result, 'bind_result'), $variables);

		$i=0;
		while($result->fetch())
		{
			$array[$i] = array();
			foreach($data as $k=>$v)
				$array[$i][$k] = $v;
			$i++;

			// don't know why, but when I tried $array[] = $data, I got the same one result in all rows
		}
	}
	elseif($result instanceof mysqli_result)
	{
		while($row = $result->fetch_assoc())
			$array[] = $row;
	}

	return $array;
}