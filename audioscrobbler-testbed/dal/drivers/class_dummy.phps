<?php

		/**
		 * class dummy.
		 *
		 * This is a dummy driver. It's meant as an example for which
		 * functions to include in a db driver.
		 *
		 * @author		Patrick van Zweden 
		 * @package dal
		 * @subpackage drivers
		 */
	class dummyDriver	{
		
		var $version;
		var $fullname;
		var $capabilities;
		var $connectString;
		var $userName;
		var $password;
					

		/**
		 * Constructor of the dummy Driver
		 */
		function dummyDriver()
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
			$this->version = "0.0.1";
			$this->fullname = "Dummy Driver";
			$this->capabilities = array("return_object", "return_array", "return_row", "return_last_insert", "transactions",
			                            "seekrecord", "nextrecord", "prevrecord");

			$this->connectString = "";
			$this->userName = "";
			$this->password = "";
		}

		function onDestroy()
		{
			/**
			 * In here you can put stuff such as closing the database connection
			 * and freeing up the last result, .....
			 *
			 * It is called when the DAL object gets destroyed.
			 */
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
			/**
			 * Select the database according to the $database var.
			 */
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
			/**
			 * Code to connect to the database.
			 * Return the success of connecting
			 */
		}

		/**
		 * Closes the database connection
		 *
		 * @return (boolean)
		 * @access public
		 */
		function closeDB()
		{
			/**
			 * Close the connection to the database
			 * Return the success of closing
			 */
		}

		/**
		 * Returns the errormessage.
		 */
		function getDBerror()
		{
			/**
			 * Gets the error message from the database
			 * and returns it to DAL.
			 */
		}

		/**
		 * Executes the query on the database
		 *
		 * @return boolean
		 * @access public
		 */
		function executeQuery($query)
		{
			/**
			 * Execute the query on the database and keep
			 * administration of the resultset we get.
			 * Return the success of executing the query on the
			 * database
			 */
		}

		/**
		 * Returns the number of affected rows from an update, insert or delete query
		 *
		 * @return int
		 * @access public
		 */
		function affectedRows()
		{
			/**
			 * Return the affected rows
			 */
		}

		/**
		 * Returns the number or rows in a result
		 *
		 * @return int
		 * @access public
		 */
		function resultRowcount()
		{
			/**
			 * Return the rowcount
			 */
		}

		/**
		 * fetches the resulting row as an object from the db
		 */
		function fetchObject()
		{
			/**
			 * Fetch on row from the resultset as
			 * an object and return it.
			 */
		}


		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array
		 * @access public
		 */
		function fetchArray()
		{
			/**
			 * Fetch a row from the resultset as an
			 * associative array and return it.
			 */
		}

		/**
		 * fetches the resulting row as an array from the db
		 *
		 * @return array
		 * @access public
		 */
		function fetchRow()
		{
			/**
			 * Fetch a row from the resultset as a
			 * enumerated array and return it.
			 */
		}

		/**
		 * Returns the last inserted record.
		 */
		function fetchLastInsert() 
		{
			/**
			 * Return the last record that was inserted as 
			 * an object.
			 */
		}

		/**
		 * Start a transaction
		 */
		function startTransaction()
		{
			/**
			 * Start a transaction
			 */
		}

		/**
		 * Commits the transaction
		 */
		function commitTransaction()
		{
			/**
			 * Commit a running transaction.
			 */
		}

		/**
		 * Aborts the transaction
		 */
		function abortTransaction()
		{
			/**
			 * abort a running transaction
			 */
		}



		/**
		 * seeks to the next row
		 * Returns false when seeking fails
		 *
		 * @return boolean
		 */
		function nextRecord()
		{
			/**
			 * currentRow = currentRow + 1
			 */
		}

		/**
		 * Seeks to the previous row
		 * Returns false when seeking fails
		 *
		 * @return boolean
		 */
		function prevRecord()
		{
			/**
			 * currentRow = currentRow -1
			 */
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
			/**
			 * currentRow = $rowID
			 */
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
			/**
			 * Save everything that is usefull for the driver
			 * to remember, such as resultsets and so on.
			 * Remeber to set the result to zero after saving the set
			 * as it gets freed when a non-zero and a query is executed.
			 */
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
			/**
			 * Restore the driver to a saved state. Remember to
			 * clean up before restoring.
			 */
		}
	}
?>
