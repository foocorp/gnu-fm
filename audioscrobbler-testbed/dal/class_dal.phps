<?php

	require("dal/config/config.php");

		/**
		 * class dal
		 * this class is the main class of the abstraction layer.
		 *
		 * @author		Patrick van Zweden 
		 * @author		Ben Efros
		 * @author		Jose Pinto
		 * @version		0.3.6
		 * @package		dal
		 */
	class dal {

		var $driver;
		var $selectedDB;
		var $database;
		var $connected;
		var $lastError;
		var $lastQuery;
		var $rowcount;
		var $errorHandler;
		var $queryType;
		var $transactionRunning;
		var $extensions;
		var $loadedExtensions;
		var $fullExtensionDir;
		var $activeConf;
		var $fakeTransactions;
		var $onErrorAbort;
		var $blockTransAfterError;
		var $inTransError;

		/**
		 * Constructor of the abstraction layer.
		 */
		function dal()
		{
			$this->initialize();
		}

		/**
		 * Initializes stuff that needs initializing
		 *
		 * @access  private
		 */
		function initialize()
		{
			$this->configuration = new dalConfigObject;

			$this->selectedDB = "none";
			$this->driver = "none";
			$this->connected = 0;
			$this->lastError = "none";
			$this->rowcount = 0;
			$this->errorHandler = "\$this->onError";
			$this->queryType = 'N';
			$this->transactionRunning = 0;
			$this->extensions = &$this->configuration->extensions;
			$this->fullExtensionDir = &$this->configuration->fullExtensionDir;
			$this->activeConf = "default";
			$this->fakeTransactions = 0;
			$this->onErrorAbort = &$this->configuration->onErrorAbort; 
			$this->blockTransAfterError = &$this->configuration->blockTransAfterError;
			$this->inTransError = 0;
			
			$this->selectDriver("mysql");
			$this->selectConfiguration("conf1");
			$this->selectDatabase("audioscrobbler");
			
		}

		/**
		 * destructor function.
		 * Unfortunatly this function has to be called to destroy the dal. Unless i find a decent hack to get a
		 * real destructor within php (register_shutdown_funtion doesn't work with objects) the user has
		 * to call the destructor.
		 *
		 * @access public
		 */
	  function destroy()
		{
			$this->onDestroy();
		}

		/**
		 * Function that get's called during shutdown of the script
		 *
		 * @access private
		 */
		function onDestroy()
		{
			if ($this->transactionRunning != 0) {
				$this->abortTransaction();
				$this->errorHandler(sprintf(CLOSE_WARNING, TRANSACTION_STILL_RUNNING));
			}

			if ($this->connected != 0) {
				$this->database->closeDB();
			}

			$this->database->onDestroy();

			if (is_array($this->loadedExtensions)) {
				while (list($extension, $object) = each($this->loadedExtensions)) {
					$this->$extension->onDestroy();
				}
			}
		}

		/**
		 * Returns the full name of the currently used driver
		 *
		 * @return (string) drivername
		 * @access public
		 */
		function getDriverName()
		{
			return $this->database->fullname . " version " . $this->database->version;
		}

		/**
		 * Selects the driver which is going to be used.
		 *
		 * @param  (string) $drivername - Name of the driver to select;
		 * @return boolean
		 * @access public
		 */
		function selectDriver($drivername)
		{
			$basedir = $this->configuration->basedir;
		  $dbDriver = $drivername . "Driver";
			$fileExt = $this->configuration->fileExt;

			if (!$this->driverExist($drivername))  {
					$this->errorHandler(sprintf(DRIVER_NOT_EXIST, $drivername));
					return false;
			}
			
			if ($this->driver != "none") {
				$this->database->onDestroy();
				unset($this->database);
			}

			include_once "$basedir/drivers/class_$drivername" . $fileExt;

			$this->database = new $dbDriver;
			$this->driver = $drivername;

			return true;
		}

		/**
		 * Checks if the specified driver exists.
		 *
		 * @param (string) $drivername - Name of the driver to check
		 * @return (int) exists
		 * @access private
		 */
		function driverExist($drivername)
		{
			return in_array($drivername, $this->enumDrivers());
		}

		/**
		 * Gives back a list of drivers which can be used.
		 *
		 * @return (array) list with available drivers
		 * @access public
		 */
		function enumDrivers()
		{
			$drivers = $this->configuration->drivers;

			return $drivers;
		}

		/**
		 * Executes a query on the database
		 *
		 * @param (string) $query
		 * @return (boolean)
		 * @access public
		 */
		function executeQuery($query)
		{
			$result = false;

			$this->lastQuery = $query;
			/**
			 * If we encountered an error while executing a transaction
			 * block all new queries until transaction is finished.
			 * The transaction is already aborted at this point.
			 */
			if ($this->inTransError && $this->blockTransAfterError) {
				$this->errorHandler(TRANSACTION_BLOCKED_DUE_ERROR);
				return false;
			}

			if ($this->configuration->extensionUseHooks)
			{
				$result = $this->doHook("query", "executeQuery", $query);
			}

			/**
			 * If the extension returned true it means the query is already
			 * executed on the database without errors. So we don't have to
			 * execute it anymore. This prevents double executing queries.
			 */
			if (!$result) {
				if (!$this->database->executeQuery($query)) {
					$this->errorHandler($this->database->getDBerror());
					$this->rowcount = 0;

					return false;
				}
			}

			if (eregi("^select", $query)) {
				$this->rowcount = $this->database->resultRowcount();
				$this->queryType = 'S';
			}
			else {
				$this->rowcount = $this->database->affectedRows();
				$this->queryType = 'N';
			}

			$this->lastQuery = $query;
			return true;
		}

		/**
     * Executes a query on the database.  Same syntax as executeQuery()
		 *  
     * @param (string) $query
     * @return (boolean)
     * @access public
		 */
		function q($query) {
			return $this->executeQuery($query);
		}

		/**
		 * Select the database we want to use
		 *
		 * @param (string) $database
		 * @return boolean
		 * @access public
		 */
		function selectDatabase($database)
		{
			$configurations = &$this->configuration->configurations;

			if ($this->configuration->extensionUseHooks)
				$this->doHook("selectdb", "selectDatabase", "$database");

			if ($this->activeConf == "default") {
				reset($configurations);
				$currentElement = current($configurations);
				$this->database->connectString = $currentElement["dbconnectString"];
				$this->database->userName      = $currentElement["dbuser"];
				$this->database->password      = $currentElement["dbpassword"];
			}
			else {
				$this->database->connectString = $configurations[$this->activeConf]["dbconnectString"];
				$this->database->userName      = $configurations[$this->activeConf]["dbuser"];
				$this->database->password      = $configurations[$this->activeConf]["dbpassword"];
			}


			if ($this->connected == 0) {
				$this->connected = $this->database->connectDB();
				
				if ($this->connected == 0) {
					$this->errorHandler(DB_CONNECT_ERROR);
					return false;
				}
			}

		if (!$this->database->selectDB($database)) {
				$this->errorHandler($this->database->getDBerror());
				return false;
			}
			else {
				return true;
			}
		}


		/**
		 * Selects the configuration where the db connect settings
		 * have to be read from
		 *
		 * @access public
		 * @param (string) $name Name of the configuration
		 * @return boolean
		 */
		function selectConfiguration($name)
		{
			if ($this->checkConfigurationExists($name)) {
				$this->activeConf = $name;
				return true;
			}
			else {
				$this->errorHandler(sprintf(CONFIGURATION_SELECT_ERROR, $name, CONFIG_DOES_NOT_EXIST));
				return false;
			}
		}


		/**
		 * Checks if the specified configuration exists and returns
		 * true or false according to the check
		 *
		 * @access private
		 * @param (string) $confName Name of the configuration to check for
		 * @return boolean
		 */
		function checkConfigurationExists($name)
		{
			$configurations = &$this->configuration->configurations;
			
			reset($configurations);
			while (list($confName, $content) = each($configurations)) {
				if ($name == $confName) {
					return true;
				}
			}

			return false;
		}

		/**
		 * fetches last inserted record.
		 *
		 * @return object
		 * @access public
		 */
		function fetchLastInsert()
		{
			if (!$this->verifyCapability("return_last_insert")) {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, LAST_INSERT_FETCHING));
				return NULL;
			}
			
			return $this->database->fetchLastInsert();
		}

		/**
		 * Fetches last inserted id
		 *
		 * @return integer
		 * @access public
		 */
		function fetchInsertID()
		{
			if (!$this->verifyCapability("return_insert_ID")) {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, LAST_INSERT_ID_FETCH));
				return 0;
			}

			return $this->database->fetchInsertID();
		}

		/**
		 * Returns a row as an object
		 *
		 * @return object
		 * @access public
		 */
		function fetchObject()
		{
			if (!$this->verifyCapability("return_object")) {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, OBJECT_FETCHING));
				return NULL;
			}

			if ($this->queryType == 'N') {
				$this->errorHandler(NO_RESULT_FROM_QUERY);
				return NULL;
			}
		
		  $object = $this->database->fetchObject(); 

			if ($object == NULL) {
				$this->errorHandler(OBJECT_FETCH_FAILED);
				return NULL;
			}

			return $object;
		}

		/**
		 * Returns a row as an array
		 *
		 * @return array
		 * @access public
		 */
		function fetchArray()
		{
			if (!$this->verifyCapability("return_array")) {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, ARRAY_FETCHING));
				return NULL;
			}

			if ($this->queryType == 'N') {
				$this->errorHandler(NO_RESULT_FROM_QUERY);
				return NULL;
			}

			$array = $this->database->fetchArray();

			if ($array == NULL){
				$this->errorHandler(ARRAY_FETCH_FAILED);
				return NULL;
			}

			return $array;
		}

		/**
		 * Returns a row as an array 
		 *
		 * @return array
		 * @access public
		 */
		function fetchRow()
		{
			if (!$this->verifyCapability("return_row")) {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, ROW_FETCHING));
				return NULL;
			}

			if ($this->queryType == 'N') {
				$this->errorHandler(NO_RESULT_FROM_QUERY);
				return NULL;
			}

			$row = $this->database->fetchRow();

			if ($row == NULL) {
				$this->errorHandler(ROW_FETCH_FAILED);
				return NULL;
			}

			return $row;
		}

		/**
		 * Error handling function
		 *
		 * @param (string) $errorMessage
		 * @access private
		 */
		function onError($errorMessage)
		{
			$this->lastError = $errorMessage;
		}

		/**
		 * Error handler wrapper.
		 *
		 * @param (string) $errorMessage
		 * @acess private
		 */
		function errorHandler($errorMessage)
		{
			if ($this->transactionRunning && $this->onErrorAbort) {
				$this->abortTransaction();
				$this->inTransError = 1;
			}
			elseif ($this->transactionRunning && !$this->onErrorAbort) {
				$this->inTransError = 1;
			}

			eval("$this->errorHandler(\$errorMessage);");
		}

		/**
		 * Wrapper to insert custom error Handler
		 *
		 * @param (string) $function_name Function name
		 * @access public
		 */
		function setErrorHandler($errorHandler)
		{
		  $this->lastError = CUSTOM_HANDLER_USED;
			$this->errorHandler = $errorHandler;
		}

		/**
		 * Checks if the driver is capable of providing a certain function
		 *
		 * @param (string) $capability Which capability to check for
		 * @return boolean
		 * @access private
		 */
		function verifyCapability($capability)
		{
			$driverCapabilities = $this->database->capabilities();

			return in_array($capability, $driverCapabilities);
		}

		/**
		 * starts transaction
		 *
		 * @return boolean
		 * @access  public
		 */
		function startTransaction()
		{
			if ($this->verifyCapability("transactions")) {
				if ($this->transactionRunning != 0) {
					$this->errorHandler(sprintf(TRANSACTION_START_FAILED, TRANSACTION_ALREADY_RUNNING));
					return false;
				}

				if ($this->configuration->extensionUseHooks)
					$result = $this->doHook("transaction", "startTransaction");

				/**
				 * Same construction as with executeQuery, when the extension returned true the transaction
				 * is already started. No need to start it again.
				 */
				if ($result) {
					if (!$this->database->startTransaction()) {
						$this->errorHandler(sprintf(TRANSACTION_START_FAILED, $this->database->getDBError()));
						$this->transactionRunning = 0;
						return false;
					}

					$this->transactionRunning = 1;
				} 
			}
			else {
				if ($this->fakeTransactions) {
					$this->transactionRunning = 1;
				}
				else {
					$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, TRANSACTION_SUPPORT));
					return false;
				}
			}

			return true;
		}

		/**
		 * Commits the transaction
		 *
		 * @return boolean
		 * @access public
		 */
		function commitTransaction()
		{

			if ($this->inTransError && $this->blockTransAfterError) {
				$this->inTransError = 0;
			}

			if ($this->verifyCapability("transactions")) {

				if ($this->configuration->extensionUseHooks)
					$result = $this->doHook("transaction", "commitTransaction");

				/**
				 * Same construction as with executeQuery, when the extension returned true the transaction
				 * is already comitted. No need to commit it again.
				 */
				if (!$result) {
					if (!$this->database->commitTransaction()) {
						$this->errorHandler(sprintf(TRANSACTION_COMMIT_FAILED, $this->database->getDBError()));
						return false;
					}

					$this->transactionRunning = 0;
				}
			}
			else {
				if ($this->fakeTransactions) {
					$this->transactionRunning = 0;
				}
				else {
					$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, TRANSACTION_SUPPORT));
					return false;
				}
			}

			return true;
		}

		/**
		 * Aborts the transaction
		 *
		 * @return (boolean) success
		 * @access public
		 */
		function abortTransaction()
		{

			if ($this->inTransError && $this->blockTransAfterError) {
				$this->inTransError = 0;
			}

			if ($this->verifyCapability("transactions")) {

				if ($this->configuration->extensionUseHooks)
					$result = $this->doHook("transaction", "abortTransaction");

				/**
				 * Same construction as executeQuery, starttransaction and the commit.
				 */
				if ($result) {
					if (!$this->database->abortTransaction()) {
						$this->errorHandler(sprintf(TRANSACTION_ABORT_FAILED, $this->database->getDBError()));
						return false;
					}

					$this->transactionRunning = 0;
				}
			}
			else {
				if ($this->fakeTransactions) {
					$this->transactionRunning = 0;
				}
				else {
					$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, TRANSACTION_SUPPORT));
					return false;
				}
			}

			return true;
		}

		/**
		 * Checks if an extension exists
		 *
		 * @param (string) $extensionName Which extension to check for
		 * @return boolean
		 * @access private
		 */
		function checkExtensionExist($extensionName)
		{
			return in_array($extensionName, $this->extensions);
		}

		/**
		 * Loads an extension
		 * Registers the extension in DAL and tries to set the extension up.
		 *
		 * First checks if extension exist, and if it's not already loaded.
		 * After checks loads source of extension and switches to the extension
		 * directory.
		 * Extension gets a reference to the database driver
		 *
		 * @access public
		 * @param (string) $extension Which extension to load
		 */
		function loadExtension($extName = "void")
		{
			$fileExt = $this->configuration->fileExt;
			
			include_once "class_extensionObject" . $fileExt;

			if ($this->checkExtensionExist($extName)) {
				if (is_array($this->loadedExtensions) && in_array($extName, $this->loadedExtensions)) {
					$this->errorHandler(sprintf(EXTENSION_LOADING_ERROR, $extName, EXTENSION_ALREADY_LOADED));
				}
				else {
					$extensionSource = sprintf("%s/%s/class_%s%s", $this->fullExtensionDir, $extName, $extName, $fileExt);
					include_once($extensionSource);
					chdir($this->fullExtensionDir);

					$this->loadedExtensions[$extName] = new extensionObject;

					$this->loadedExtensions[$extName]->name      = $extName;
					$this->loadedExtensions[$extName]->extension = new $extName;

					$this->$extName = &$this->loadedExtensions[$extName]->extension;
					$this->loadedExtensions[$extName]->extension->dbdriver = & $this->database;
					
					$finalHook = $this->loadedExtensions[$extName]->checkFinal();

					if ($this->loadedExtensions[$extName]->checkSelectDBHook()) {
						$this->hooks["selectdb"][$extName] = $finalHook;
					}

					if ($this->loadedExtensions[$extName]->checkQueryHook()) {
						$this->hooks["query"][$extName] = $finalHook;
					}

					if ($this->loadedExtensions[$extName]->checkTransactionHook()) {
						$this->hooks["transaction"][$extName] = $finalHook;
					}

					/**
					 * This sorts the hooks on the final attribute
					 */
					asort($this->hooks["selectdb"]);
					asort($this->hooks["query"]);
					asort($this->hooks["transaction"]);
				}
			}
		}

		/**
		 * Seeks to next record if possible
		 *
		 * @access public
		 * @return boolean
		 */
		function nextRecord()
		{
			if ($this->verifyCapability("nextrecord")) {
				if ($this->queryType == 'N') {
					$this->errorHandler(sprintf(NEXT_RECORD_SEEK_FAILED, NO_RESULT_FROM_QUERY));
					return false;
				}
				else {
					if ($this->database->nextRecord()) {
						return true;
					}
					else {
						$this->errorHandler(sprintf(NEXT_RECORD_SEEK_FAILED, $this->database->getDBError()));
						return false;
					}
				}
			}
			else {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, NEXT_RECORD_SEEK));
				return false;
			}
		}

		/**
		 * Seeks to previous record if possible
		 *
		 * @access public
		 * @return boolean
		 */
		function prevRecord()
		{
			if ($this->verifyCapability("prevrecord")) {
				if ($this->queryType == 'N') {
					$this->errorHandler(sprintf(PREV_RECORD_SEEK_FAILED, NO_RESULT_FROM_QUERY));
					return false;
				}
				else {
					if ($this->database->prevRecord()) {
						return true;
					}
					else {
						$this->errorHandler(sprintf(PREV_RECORD_SEEK_FAILED, $this->database->getDBError()));
						return false;
					}
				}
			}
			else {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, PREV_RECORD_SEEK));
				return false;
			}
		}

		/**
		 * Seeks to specified record if possible
		 *
		 * @access public
		 * @param (int) $id Which record to seek to
		 * @return boolean
		 */
		function seekRecord($id)
		{
			if ($this->verifyCapability("seekrecord")) {
				if ($this->queryType == 'N') {
					$this->errorHandler(sprintf(RECORD_SEEK_FAILED, NO_RESULT_FROM_QUERY));
					return false;
				}
				else {
					if ($id > $this->rowCount | $id < 0) {
						$this->errorHandler(sprintf(RECORD_SEEK_FAILED, RECORD_OUT_OF_BOUNDS));
					}
					else {
						if ($this->database->seekRecord($id)) {
							return true;
						}
						else {
							$this->errorHandler(sprintf(RECORD_SEEK_FAILED, $this->database->getDBError()));
							return false;
						}
					}
				}
			}
			else {
				$this->errorHandler(sprintf(DRIVER_NOT_CAPABLE, RECORD_SEEKING));
				return false;
			}
		}
				
		/**
		 * Fakes transaction functions
		 * Usefull if you want to make code which is transaction compatible
		 * but you don't have a transaction capable system.
		 *
		 * @access public
		 */
		function enableFakeTransactions() {
			$this->fakeTransactions = 1;
		}

		/**
		 * Returns DAL to normal transaction state
		 *
		 * @access public
		 */
		function disableFakeTransactions() {
			$this->fakeTransactions = 0;
		}

		/**
		 * Adds a new configuration to the built-in list
		 * Allows you to install configurations after the configs 
		 * read from the config file.
		 *
		 * @access public
		 * @return boolean
		 * @param (char) $configName Name of the new configuration (must be unique)
		 * @param (char) $userName Username for the configuration
		 * @param (char) $password Password for the configuration
		 * @param (char) $connectString Connectstring for the configuration
		 */
		function addConfiguration($configName='empty', $userName='empty', 
															$password='empty', $connectString='empty')
		{
			$configurations = &$this->configuration->configurations;

			if ($this->configuration->configurationLocked) {
				$this->errorHandler(sprintf(CONF_ADD_ERROR, $configName, CONFIGURATION_LOCKED));
				return false;
			}
			
			if ($this->checkConfigurationExists($configName)) {
				$this->errorHandler(sprintf(CONF_ADD_ERROR, $configName, CONFIGURATION_EXISTS));
				return false;
			}
			else {
				if ($configName == 'empty' || $userName == 'empty' || $password == 'empty') {
					$this->errorHandler(sprintf(CONF_ADD_ERROR, $configName,  sprintf(NOT_ENOUGH_PARAMETERS, 4)));
					return false;
				}
				else {
					if ($connectString == 'empty') {
						$connectString = '';
					}
					$configurations[$configName] = array("dbuser"          => $userName,
														 "dbpassword"      => $password,
														 "dbconnectString" => $connectString);
				}
			}

			return true;
		}

		/**
		 * Handles hooks
		 * Walks trough the list and calls the functions in it.
		 *
		 * @access public
		 * @return boolean
		 * @param (char) $hookList Name of the list to walk through
		 * @param (char) $function Which function we were called from
		 * @param (char) $param1 Parameters for the hooked function
		 */
		function doHook($hookList, $function, $param1="empty") 
		{
			if (!is_array($this->hooks[$hookList]))
					return false;
			
			reset($this->hooks[$hookList]);

			while (list($extName, $final) = each($this->hooks[$hookList])) {
				if ($param1 != "empty") {
					if ($final) {
						return $this->$extName->$function($param1);
					}
					else {
						$this->$extName->$function($param1);
					}
				}
				else {
					if ($final) {
						return $this->$extName->$function();
					}
					else {
						$this->$extName->$function();
					}
				}
			}

			return false;
		}
			
	}


?>
