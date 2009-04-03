<?php

		/**
		 * class msqlDriver
		 * This class is a driver for the msql database
		 * it has standard functions for connecting to the database
		 * and executing queries on the database.
		 *
		 * @author		Patrick van Zweden 
		 * @author 		Ben Efros
		 * @version	0.0.6
		 * @package dal
		 * @subpackage drivers
		 */
	class msqlDriver	{
		
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
		 * Constructor of the msql Driver
		 */
		function msqlDriver()
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
			$this->version = "0.0.6";
			$this->fullname = "Msql Driver";
			$this->connection = 0;
			$this->resultID = 0;
			$this->curRow = 0;
			$this->capabilities = array("return_object", "return_array", "return_row", "return_last_insert",
			                            "seekrecord", "nextrecord", "prevrecord");
		}

		function onDestroy()
		{
		}

		/**
		 * Select the database to be used
		 *
		 * @param string $database - name of the database to be used
		 * @return (int) database selected
		 * @access public
		 */
		function selectDB($database)
		{
			$this->activeDB = $database;
			return @msql_select_db($database, $this->connection);
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
		 * @param (String) $database
		 * @return (int) 
		 * @access public
		 */
		function connectDB()
		{
			$this->connection = msql_connect($this->connectString);
			return $this->connection;
		}

		/**
		 * Closes the database connection
		 *
		 * @return (boolean)
		 * @access public
		 */
		function closeDB()
		{
			return @msql_close($this->connection);
		}

		/**
		 * Returns the errormessage.
		 */
		function getDBerror()
		{
			return @msql_error();
		}

		/**
		 * Executes the query on the database
		 *
		 * @return boolean
		 * @access public
		 */
		function executeQuery($query)
		{
			$this->resultID = @msql_query($query, $this->connection);

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
		 * @param (string) $query 
		 * @return nothing
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

			$tmpresult = msql_list_fields($this->activeDB, $table);
			
			for ($i=0;$i < count($values);$i++) {
				$fieldnames[] = @msql_fieldname($tmpresult, $i);
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
		 * Returns the number of affected rows from an update, insert or delete query
		 *
		 * @return int
		 * @access public
		 */
		function affectedRows()
		{
			return @msql_affected_rows($this->resultID);
		}

		/**
		 * Returns the number or rows in a result
		 *
		 * @return int
		 * @access public
		 */
		function resultRowcount()
		{
			return @msql_num_rows($this->resultID);
		}

		/**
		 * fetches the resulting row as an object from the db
		 */
		function fetchObject()
		{
			return @msql_fetch_object($this->resultID, MSQL_ASSOC);
		}


		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array
		 * @access public
		 */
		function fetchArray()
		{
			return @msql_fetch_array($this->resultID, MSQL_ASSOC);
		}

		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array
		 * @access public
		 */
		function fetchRow()
		{
			return @msql_fetch_row($this->resultID);
		}

		/**
		 * fetches last inserted record
		 */
		function fetchLastInsert()
		{
			$this->savedResult = $this->resultID;
			$this->resultID = 0;
			$this->getInsertedRow($this->insertQuery);
			@msql_free_result($this->resultID);
			$this->resultID = $this->savedResult;
			unset($this->savedResult);
		
			return $this->lastInsert;
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
			if (@msql_data_seek($this->resultID, $rowID)) {
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
				msql_free_result($this->resultID);
			}

			$this = $this->savedState;
			unset($this->savedState);
		}


	}
?>
