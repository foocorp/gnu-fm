<?php

	/**
	 * Config file for dal
	 *
	 * @package dal
	 */

	class dalConfigObject {

		/**
	 	 * Base dir from where dal tries to load drivers and so on
	 	 */
		 var $basedir;

		/**
	 	 * Contains which drivers can be loaded
	 	 * @package dal
	 	 */
		 var $drivers;


		/**
	 	 * Connection info
	 	 */
		var $configurations;

		/**
		 * Configuration lock
		 */
		var $configurationLocked;

		/**
		 * Path to use for storing temporary files.
	 	 */
		var $tmpFilePath;

		/**
	 	 * Which dir we use for the extensions
	 	 */
		 var $extensionDir;

		 /**
			* Do we use extensions which use hooks ?
			*/
		 var $extensionUseHooks;

		 /**
			* Full extension dir name
			*/
		 var $fullExtensionDir;

		 /**
			* Language to use
			*/
		 var $language;


		 /**
			* Extension of the files we include.
			*/
		 var $fileExt;

		 /**
			* Constructor
			*
			* @access private
			*/
		 function dalConfigObject()
		 {
			 $this->init();
			 $this->createExtensionList();
			 $this->includeMessages();
		 }

		 /**
			* Inits all the vars
			*
			* @access private
			*/
		 function init()
		 {
				$this->basedir = "d:/web/audioscrobbler/dal";
				$this->drivers = array("mysql", "postgres", "msql");

				$this->configurations = array("conf1" => array( "dbuser"          => "josh",
						                                "dbpassword"      => "",
										"dbconnectString" => ""),
										"conf2" => array( "dbuser" => "",
										"dbpassword"      => "",
										"dbconnectString" => "")
										      );

				$this->configurationLocked = false;
				$this->tmpFilePath = "./";
				$this->extensionDir = "./";
				$this->extensionUseHooks = true;
				$this->language = "english";

				$this->blockTransAfterError = 1;
				$this->onErrorAbort = 1;

				$this->fileExt = '.phps';
		 }


		 /**
			* Includes the messages dal gives
			*
			* @access private
			*/
		 function includeMessages()
		 {
			 include_once("$this->basedir/config/lang/$this->language.inc");
		 }

		/**
		 * Fills array with which extensions are available
		 *
		 * @access private
	 	 */
		 function createExtensionList()
		 {
			 $this->fullExtensionDir = $this->basedir . "/" . $this->extensionDir;
			 $dirHandle = opendir($this->fullExtensionDir);

			 while ($fileName = readdir($dirHandle)) {
				 $fullName = $this->fullExtensionDir  . $fileName;

				 if (filetype($fullName) == "dir") {
		  		 if (!preg_match("/\.|\.\./", $fullName)) {
						 $this->extensions[] = $fileName;
					 }
				 }
			 }
		 }


	}
?>
