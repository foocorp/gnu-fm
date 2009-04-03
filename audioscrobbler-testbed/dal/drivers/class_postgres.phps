<?php

		/**
		 * class postgresDriver
		 *
		 * This class is the connection between postgres and DAL
		 *
		 * @author		Patrick van Zweden 
		 * @author 		Ben Efros
		 * @version	0.3.5
		 * @package dal
		 * @subpackage drivers
		 */
	class postgresDriver	{
		
		var $version;
		var $fullname;
		var $capabilities;
		var $connection;
		var $resultID;
		var $database;
		var $lateConnection;
		var $errormessage;
		var $curRow;
		var $lastInsert;
		var $connectString;
		var $userName;
		var $password;
		var $savedState;
		var $insertResultID;
		var $insertQuery;
		var $insertID;
					

		/**
		 * Constructor of the postgres Driver
		 */
		function postgresDriver()
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
			$this->version = "0.3.5";
			$this->fullname = "PostgreSQL Driver";
			$this->connection = 0;
			$this->resultID = 0;
			$this->capabilities = array("return_object", "return_array", "return_row", "return_last_insert", "transactions",
			                            "seekrecord", "nextrecord", "prevrecord");
			$this->database = "";
			$this->lateConnection = 0;
			$this->errormessage = "";
			$this->curRow = 0;
			$this->connectString = "";
			$this->userName = "";
			$this->password = "";
			$this->connectString = "";
			$this->userName = "";
			$this->password = "";
		}

		function onDestroy()
		{
			if ($this->resultID != 0) {
				@pg_freeresult($this->resultID);
				$this->resultID = 0;
			}
		}

		/**
		 * Select the database to be used
		 *
		 * input  : string $database - name of the database to be used
		 * @return (int) database selected
		 * @access public
		 */
		function selectDB($database)
		{
			$this->database = $database;
			return true;
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
		 * With postgres we can only connect when the database name to use
		 * is know. So if it is not know we will delay the connection.
		 *
		 * @param (String) $database
		 * @return (int) 
		 * @access public
		 */
		function connectDB()
		{

			if ($this->database != "") {
				$localconnectString = $this->connectString;
				$localconnectString .= " dbname=$this->database";

				if ($this->userName != "" && $this->password != "") {
					$localconnectString .= " user=$this->userName password=$this->password";
				}

				$this->connection = @pg_connect($localconnectString);
			}
			else {
				/*
				 * We don;t make a connection but tell the dal it is there.
				 * And take a note that we still have to connect.
				 */
				$this->lateConnection = 1;
				$this->connection = 0;
				return 1;
			}

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
		  if (@pg_close($this->connection)) {
				$this->connection = 0;
				return true;
			}

			return false;
		}

		/**
		 * Returns the errormessage.
		 */
		function getDBerror()
		{
			if ($this->connection != 0) {
				$localError = @pg_errormessage($this->connection);

				if ($localError == false) {
					return DB_NO_MESSAGE;
				}
				
				return $localError;
			}
			else {
				if ($this->errorMessage == "") {
					return DB_NO_MESSAGE;
				}

				return $this->errorMessage;
			}
		}

		/**
		 * Executes the query on the database
		 *
		 * @return boolean
		 * @access public
		 */
		function executeQuery($query)
		{
			/*
			 * It's possible that we delayed the connection until a database got
			 * selected so we need to check that here.
			 */

			$this->curRow = 0;
			
			if ($this->resultID != 0 && $this->insertResultID == 0) {
				@pg_freeresult($this->resultID);
			}

			if ($this->connection == 0 && $this->lateConnection == 1) {
				$this->connectDB();

				if ($this->connection == 0) {
					$this->errorMessage = DB_CONNECT_ERROR;
					return false;
				}

				$this->lateConnection = 0;
			}
			elseif ($this->connection == 0) {
				$this->errorMessage = ERROR_NO_DB_SELECTED;
				return false;
			}

			$this->resultID = @pg_exec($this->connection, $query);

			if (eregi("^insert", $query)) {
				$this->insertQuery = $query;
				$this->insertID = $this->getInsertID();
			}

			if ($this->resultID != 0) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * Returns the number of affected rows from an update, insert or delete query
		 *
		 * @return int
		 * @access public
		 */
		function affectedRows()
		{
			return @pg_cmdtuples($this->resultID);
		}

		/**
		 * Returns the number or rows in a result
		 *
		 * @return int
		 * @access public
		 */
		function resultRowcount()
		{
			return @pg_numrows($this->resultID);
		}

		/**
		 * fetches the resulting row as an object from the db
		 */
		function fetchObject()
		{
			$object = @pg_fetch_object($this->resultID, $this->curRow, PGSQL_ASSOC);
			$this->curRow++;

			return $object;
		}


		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array
		 * @access public
		 */
		function fetchArray()
		{
			$array =  @pg_fetch_array($this->resultID, $this->curRow, PGSQL_ASSOC);
			$this->curRow++;

			return $array;
		}

		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array
		 * @access public
		 */
		function fetchRow()
		{
			$row = @pg_fetch_row($this->resultID, $this->curRow);
			$this->curRow++;

			return $row;
		}

		/**
		 * Returns the id of the last inserted record
		 *
		 * @return integer
		 * @access public
		 */
		function getInsertID()
		{
		  return @pg_getlastoid($this->resultID);
	  }

		/**
		 * Returns the last inserted record.
		 */
		function fetchLastInsert() 
		{
			$table = preg_split("/[\s]+/", $this->insertQuery);
			$table = $table[2];

			$this->insertResultID = $this->resultID;
				
			$query = "select * from $table where oid=$this->insertID";

			$this->executeQuery($query);
			$this->lastInsert = $this->fetchObject();

			@pg_freeresult($this->resultID);
			$this->resultID = $this->insertResultID;
			$this->insertResultID = 0;
		
			return $this->lastInsert;
		}

		/**
		 * Start a transaction
		 */
		function startTransaction()
		{
			return $this->executeQuery("begin work;");
		}

		/**
		 * Commits the transaction
		 */
		function commitTransaction()
		{
			return $this->executeQuery("commit;");
		}

		/**
		 * Aborts the transaction
		 */
		function abortTransaction()
		{
			return $this->executeQuery("abort;");
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
			if ($rowID > $this->resultRowcount() | $rowID < 1) {
				return false;
			}
			else {
				$this->curRow = $rowID;
				return true;
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
				pg_freeresult($this->resultID);
			}

			$this = $this->savedState;
			unset($this->savedState);
		}
	}
?>
