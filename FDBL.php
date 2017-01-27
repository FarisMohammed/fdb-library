<?php 
	
/**
*
* LiteDtabaseLibrary
* -----------------------------------------------------------------
* Author : Faris Mohammd P (Senior Software Developer)
* Mail  : faris.pachayil@outlook.com
* Phone : 9744115433
* 
*/

class FDBL
{

	// Database Credentials

	private $server = 'localhost';
	private $user = 'root';
	private $pasword = '';
	private $database = 'ehealth_db';
	private $isErrosOn = true;

	// Execution an Manipulation Query and Error
	private function execute($query)
	{
		// Establishing the Connection
		$link = mysqli_connect($this->server, $this->user, $this->pasword, $this->database);
		// Executing the Query
		$data = mysqli_query($link, $query) or ($this->isErrosOn ? die(require 'error.php') : '');
		// Closing the Connection
		$this->close($link);
		// return the data
		return $data;
	}

	// Closing database connection
    private function close($link) {
        mysqli_close($link);
    }

    // Insertion Query Manipulation
	public function insert($table, $array)
	{
		// Key Filtering for Query
		$keys = implode(',', array_keys($array));	
		// Value Filtering for Query
		$values = implode('","', $array);
		// Defining The Query
		$query = 'INSERT INTO ' .$table. ' ('. $keys .') VALUES ("' .$values. '")';
		//Executing The Query
		$status = $this->execute($query);
		// Return the Status
		return $status == true ? true : false;
	}

    // Update Query Manipulation
	public function update($table, $array, $conditionArray)
	{
		// Value and Key Filtering for Query
		$updation = '';
		foreach ($array as $key => $value) {
			$updation = $updation . ',' . ($key . '=' . '"' . $value . '"');
		}
		$updation = substr_replace($updation, '', 0,1);

		// Condition Filtering For Query
		$condition = '';
		if(is_numeric($conditionArray))
			$condition = 'id = '. $conditionArray;
		else
		{
			foreach ($conditionArray as $key => $value) {
				if(!is_numeric($key))
					$condition = $condition  . ($key . '=' . '"' . $value . '"');
				else
					$condition = $condition  . ' ' . $value . ' ';
			}	
		}	

		// Defining The Query
		$query = 'UPDATE ' .$table. ' SET ' . $updation . ' WHERE '. $condition;
		//Executing The Query
		$status = $this->execute($query);
		// Return the Status
		return $status == true ? true : false;
	}

    // Delete Query Manipulation
	public function delete($table, $keys = null)
	{
		$queryTail = '';
		if(!is_null($keys))
		{
			// Condition Filtering For Query
			$condition = '';
			if(is_numeric($keys))
				$queryTail = 'WHERE id = '. $keys;
			else
			{
				foreach ($keys as $index => $value) {
					if(!is_numeric($index))
						$condition = $condition  . ($index . '=' . '"' . $value . '"');
					else
						$condition = $condition  . ' ' . $value . ' ';
				}
				$queryTail = 'WHERE '. $condition;	
			}
		}

		// Defining The Query
		$query = 'DELETE FROM ' . $table . ' '. $queryTail;
		//Executing The Query
		$status = $this->execute($query);
		// Return the Status
		return $status == true ? true : false;
	}

    // Select Query Manipulation
	public function select($table, $array = null, $conditionArray = null)
	{
		// Order Bye The Query
		$numArgs = func_num_args();
		$lastArgs = func_get_arg($numArgs - 1);
		$queryLast = '';
		$limit = '';
		$order = '';
		$by = '';
		if(is_array($lastArgs) && (isset($lastArgs[1])  == 'ASC' || isset($lastArgs[1]) == 'DESC'))
		{
			$by = $lastArgs[0];
			$order = $lastArgs[1];
			if(count($lastArgs) > 2)
			{
				$limit =   'LIMIT '. implode(',', array_splice($lastArgs, 2)); 
			}
			
		}
		else
		{
			$order = $lastArgs;
			$by = 'id';
		}
		if(($order == 'ASC' || $order == 'DESC') && $numArgs > 2)
		{
			$queryLast = 'ORDER BY ' . $by . ' ' . $order . ' ' . $limit;
			if($numArgs == 3)
			  $conditionArray = null;
		}


		$queryTail = '';
		$values = '';
		if(!is_null($array))
		{
			if($array == '*')
			{
				$values = '*';
				if(is_numeric($conditionArray))
				$queryTail = 'WHERE id = '. $conditionArray;
			}

			else
			{	
				$values = implode(',', $array);
			}
			if(!is_null($conditionArray))
			{
				// Condition Filtering For Query
				$condition = '';
				if(is_numeric($conditionArray))
					$queryTail = 'WHERE id = '. $conditionArray;
				else
				{
					foreach ($conditionArray as $key => $value) {
						if(!is_numeric($key))
							$condition = $condition  . ($key . '=' . '"' . $value . '"');
						else
							$condition = $condition  . ' ' . $value . ' ';
					}	
					$queryTail = 'WHERE '. $condition;	
				}	
			}
		}


		// Defining The Query
		$query = 'SELECT ' .$values. ' FROM ' . $table . ' ' .$queryTail . ' '. $queryLast;
		//Executing The Query
	    $status = $this->execute($query);
		// Return the Status
		return $status == true ? $status : false;
	}

	// Get the count From associated query
	public function count($data)
	{
		return mysqli_num_rows($data);
	}

	// Retrieve data as array
	public function fetchArray($data)
	{
		return mysqli_fetch_array($data);
	}

	// Retrieve data Array after Executing
	public function mergeSelect($table, $array = null, $conditionArray = null)
	{
		$data = $this->select($table, $array, $conditionArray);
		return $this->fetchArray($data);
	}

	// Authenticate Session
	public function activateSession()
	{
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}
	}

	// Authenticate Session
	public function authenticate($InOrOut = 'out')
	{
		 $this->activateSession();
		 if($InOrOut == 'out')
		 {
	      	 if(!isset($_SESSION['isUser']))
				header('location:login.php');
		 }
		 else
		 	if(isset($_SESSION['isUser']))
				header('location:home.php');

	}

	// Logout the Session
	public function logout()
	{
		$this->activateSession();
		session_destroy();
		return true;
	}

	// Alert the Session Message
	public function setSessionMsg($value, $key = 'result')
	{	
		$this->activateSession();
		$_SESSION[$key] = $value;
	}

	// Return the Session Message
	public function getSessionMsg($key = 'result')
	{	
		$this->activateSession();
		if(isset($_SESSION[$key]))
	    {
		    return $_SESSION[$key];
	    }
	}

	// Alert the Session Message
	public function alertSessionMsg($key = 'result')
	{	
		$this->activateSession();
		if(isset($_SESSION[$key]))
	    {
		    echo '<script type="text/javascript"> alert("' . $_SESSION[$key] . '"); </script>';
		  	unset($_SESSION[$key]);

	    }
	}
}

?>