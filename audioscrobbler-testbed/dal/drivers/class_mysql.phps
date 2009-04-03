<?php

		/**
		 * class mysqlDriver
		 * This class is a driver for the mysql database
		 * it has standard functions for connecting to the database
		 * and executing queries on the database.
		 * @author		Patrick van Zweden 
		 * @author 		Ben Efros
		 * @version	0.1.6
		 * @package dal
		 * @subpackage drivers
		 */
	class mysqlDriver	{
		
		var $version;
		var $fullname;
		var $capabilities;
		var $connection;
		var $resultID;
		var $lastInsert;
		var $activeDB;
		var $connectString;
		var $userName;
		var $password;
		var $curRow;
		var $insertQuery;

		/**
		 * Constructor of the mysql Driver
		 */
		function mysqlDriver()
		{
			$this->initialize();
		}

		/**
		 * Initialize stuff that needs initializing
		 *
		 * @access private
		 */
		function initialize()
		{
			$this->version = "0.1.6";
			$this->fullname = "Mysql Driver";
			$this->connection = 0;
			$this->resultID = 0;
			$this->curRow = 0;
			$this->capabilities = array("return_object", "return_array", "return_row", "return_last_insert", "return_insert_ID",
			                            "seekrecord", "nextrecord", "prevrecord");
		}

		/**
		 * Gets called when the driver is destroyed
		 */
		function onDestroy()
		{
		}

		/**
		 * Select the database to be used
		 *
		 * @param	(string)	$database	name of the database to be used
		 * @return (integer) database_selected
		 * @access public
		 */
		function selectDB($database)
		{
			$this->activeDB = $database;
			return @mysql_select_db($database, $this->connection);
		}

		/**
		 * Returns the capabilities of the driver
		 *
		 * @return (array) capabilities of the driver
		 * @access public
		 */
		function capabilities()
		{
			return $this->capabilities;
		}

		/**
		 * Connects to the database
		 *
		 * @return (int) connection
		 * @access public
		 */
		function connectDB()
		{
			$this->connection = @mysql_connect($this->connectString, $this->userName , $this->password);
			return $this->connection;
		}

		/**
		 * Closes the database connection
		 *
		 * @return (boolean) success
		 * @access public
		 */
		function closeDB()
		{
			return @mysql_close($this->connection);
		}

		/**
		 * Returns the errormessage.
		 *
		 * @scope public
		 * @return (String) errormessage
		 */
		function getDBerror()
		{
			if ($this->connection != 0) {
				return @mysql_error($this->connection);
			}
			else {
				return @mysql_error();
			}
		}

		/**
		 * Executes the query on the database
		 *
		 * @return (boolean) success
		 * @access public
		 */
		function executeQuery($query)
		{
			$this->resultID = @mysql_query($query, $this->connection);

			if (eregi("^insert", $query)) {
				$this->insertQuery = $query;
			}

			if ($this->resultID != 0) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * Updates the lastInsert variable with the last inserted record.
		 *
		 * @param  (string) $query text of the query
		 * @access private
		 */
		function getInsertedRow($query) {
			$queryfields = preg_split("/values.*\(/", $query);

			$queryfields[1] = preg_replace("/\)/", "", $queryfields[1]);
			$queryfields[1] = preg_replace("/;/", "", $queryfields[1]);

			$values = preg_split("/,\s/", $queryfields[1]);
			$values = $this->checkFields($values);
			
			$table = preg_split("/\s/", $queryfields[0]);
			$table = $table[2];

			$tmpresult = @mysql_list_fields($this->activeDB, $table, $this->connection);
			
			for ($i=0;$i < count($values);$i++) {
				$fieldnames[] = @mysql_field_name($tmpresult, $i);
			}

			$query = "select * from $table where ";
			$a = "";

			for ($i=0;$i < count($values);$i++) {
				if (!preg_match("/null/i", $values[$i])) {
					$query .= $a.$fieldnames[$i].'='.$values[$i];
					$a = ' and ';
				}
			}

			if ($this->executeQuery($query)) {
				$this->lastInsert = $this->fetchObject();
			}
		}

		/**
		 * Checks the field split and pastes it together
		 *
		 * @return (array) field_values
		 * @param (array) field_values Values of the array after the field split
		 */
		function checkFields($fieldvalues)
		{
			reset ($fieldvalues);
			
			for ($i=0;$i<count($fieldvalues);$i++) {
				if (preg_match('/^("|\').*[^"\']$/s', $fieldvalues[$i]) || $fieldvalues[$i][strlen($fieldvalues[$i])-2] == '\\') {
					$value = $fieldvalues[$i];

					for ($j=$i+1;$j<count($fieldvalues);$j++) {
						if (preg_match('/.*"$/', $fieldvalues[$j])) {
							$value .= ", " . $fieldvalues[$j];
						}
						else 
							$value .= ', ' . $fieldvalues[$j];

						$i = $j +1;
					}
				}
				else 
					$value = $fieldvalues[$i];

				$returnvalues[] = $value;
			}

			return $returnvalues;
		}

								
		/**
		 * Updates the last inserted id
		 *
		 * @return (integer) last inserted id
		 * @access public
		 */
		function updateLastID()
		{
			$this->lastID = @mysql_insert_id($this->connection);
		}

		/**
		 * Returns the number of affected rows from an update, insert or delete query
		 *
		 * @return (integer) affected rows
		 * @access public
		 */
		function affectedRows()
		{
			return @mysql_affected_rows($this->connection);
		}

		/**
		 * Returns the number or rows in a result
		 *
		 * @return (integer) numer of rows
		 * @access public
		 */
		function resultRowcount()
		{
			return @mysql_num_rows($this->resultID);
		}

		/**
		 * fetches the resulting row as an object from the db
		 *
		 * @return (object) row as an object
		 * @access public
		 */
		function fetchObject()
		{
			return @mysql_fetch_object($this->resultID, MYSQL_ASSOC);
		}


		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return (array) row as an array
		 * @access public
		 */
		function fetchArray()
		{
			return @mysql_fetch_array($this->resultID, MYSQL_ASSOC);
		}

		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array row as an array
		 * @access public
		 */
		function fetchRow()
		{
			return @mysql_fetch_row($this->resultID);
		}

		/**
		 * fetches last inserted record
		 *
		 * @return (object) last inserted record
		 * @access public
		 */
		function fetchLastInsert()
		{
			$this->savedResult = $this->resultID;
			$this->resultID = 0;
			$this->getInsertedRow($this->insertQuery);
			$this->updateLastID();
			@mysql_free_result($this->resultID);
			$this->resultID = $this->savedResult;
			unset($this->savedResult);

			return $this->lastInsert;
		}

		/**
		 * Fetches last autogenerated id
		 *
		 * @return (integer) last generated id
		 * @access public
		 */
		function fetchInsertID()
		{
			return $this->lastID;
		}


		/**
		 * seeks to the next row
		 * Returns false when seeking fails
		 *
		 * @return boolean
		 */
		function nextRecord()
		{
			return $this->seekRecord($this->curRow + 1);
		}

		/**
		 * Seeks to the previous row
		 * Returns false when seeking fails
		 *
		 * @return boolean
		 */
		function prevRecord()
		{
			return $this->seekRecord($this->curRow - 1);
		}

		/**
		 * Seeks to the specified row
		 * Returns false when seeking fails
		 *
		 * @param (int) $rowID ID of the row to seek to
		 * @return boolean
		 */
		function seekRecord($rowID)
		{
			if (@mysql_data_seek($this->resultID, $rowID)) {
				$this->curRow = $rowID;
				return true;
			}
			else {
				return false;
			}
		}


		/**
		 * Saves the current state of the database driver
		 * It saves the current state and sets the resultID
		 * to zero to prevent erasing of the resultset.
		 *
		 * @access public
		 */
		function saveState()
		{
			$this->savedState = $this;

			$this->resultID = 0;
		}

		/**
		 * Restores the saved state of the database driver
		 * Trows away a produced result and restores the
		 * saved state. After that it deletes the stored
		 * state.
		 *
		 * @access public
		 */
		function restoreState()
		{
			if ($this->resultID != 0) {
				mysql_free_result($this->resultID);
			}

			$this = $this->savedState;
			unset($this->savedState);
		}


	}
?>
