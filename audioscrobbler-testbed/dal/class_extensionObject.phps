	<?php
	/**
	 * class extensionObject
	 *
	 * This class is the framework to put loaded extensions in. It provides all the
	 * nescesarry variables to do the administration involved with loaded extensions.
	 *
	 * @version 0.1.3
	 * @author Patrick van Zweden
	 * @package dal
	 */
	class extensionObject 
	{
		/**
		 * Name of the extension
		 *
		 * @access private
		 */
		var $name;
		
		/**
		 * Provides the pointer to the loaded extension
		 *
		 * @access private
		 */
		var $extension;

		/**
		 * Does the extension override the query functions ?
		 * By this we mean the executeQuery function
		 *
		 * @access private
		 */
		var $queryHook;

	
		/**
		 * Gives a reference to the driver that is used.
		 */
		var $dbdriver;


		/**
		 * Constructor
		 *
		 * @access private
		 */
		function extensionObject()
		{
			$this->init();
		}

		/**
		 * Init routine
		 *
		 * @access private
		 */
		function init()
		{
			$this->name = "undefined";
			$this->extension = NULL;
			
			$this->final = 0;
		}

		/**
		 * Return the name of the extension
		 *
		 * @access public
		 */
		function returnName()
		{
			return $this->name;
		}

		/**
		 * checks if the query functions are overriden
		 *
		 * @access public
		 */
		function checkQueryHook()
		{
			if ($this->extension->queryHook) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * Checks if the selection of database is overriden
		 *
		 * @access public
		 */
		function checkSelectDBHook()
		{
			if($this->extension->selectDBHook) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * Checks if the transaction functions are overridden
		 *
		 * @access public
		 */
		function checkTransactionHook()
		{
			if($this->extension->transactionHook) {
				return true;
			}
			else {
				return false;
			}
		}

		/** 
		 * Checks the final attribute
		 *
		 * @access public
		 */
		function checkFinal()
		{
			if ($this->extension->final == 1) {
				return true;
			}
			else {
				return false;
			}
		}
			

	}

?>
