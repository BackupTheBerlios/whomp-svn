<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/installer.php
 * 
 * Whomp installer file. Includes the 
 * {@link Whomp_Installer Whomp_Installer} class.
 * 
 * @package Whomp
 * @copyright © 2005 Schmalls / Joshua Thompson / All Rights Reserved
 * @license http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
 * @version 0.0.0
 * @since 0.0.0
 * @link http://www.schmalls.com
 */

 /**
  * Make sure this file is being included by a parent file
  */
 defined('_VALID_WHOMP') or exit('Direct access to this location is not allowed!');
 
 /**
  * The Whomp installer class
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo finish implementing this
  */
 class Whomp_Installer {
	 
	 /**
	  * Upload file array
	  * 
	  * @var array $_upload_file
	  * @access protected
	  */
	 protected $_upload_file;
	 
	 /**
	  * Whomp installer constructor
	  * 
	  * Takes the upload file array as an option
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $upload_file the upload file array
	  */
	 public function __construct($upload_file) {
		 
		 $this->_upload_file = $upload_file;
	 } // end function
	 
	 /**
	  * Install function
	  * 
	  * Unzips the install file, moves the files to the correct locations, runs database queries, and 
	  * registers available options.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if the install file is not available
	  * @global string the whomp storage path
	  * @global class access to the database
	  * @todo add register ability and callbacks
	  */
	 public function install() {
		 global $_whomp_storage_path, $_whomp_database;
		 
		 /**
		  * Require the pclzip library
		  */
		 require_once($_whomp_storage_path . '/extensions/pclzip/pclzip.lib.php');
		 /**
		  * Require the pclzip error function
		  */
		 require_once($_whomp_storage_path . '/extensions/pclzip/pclerror.lib.php');
		 // create the zipfile option
		 $zipfile = new PclZip($this->_upload_file['tmp_name']);
		 // extract to a temporary directory
		 $extract_directory = $_whomp_storage_path . '/repository/whomp/installer/temp/' . $this->_upload_file['name'] . '/';
		 $zipfile->extract(PCLZIP_OPT_PATH, $extract_directory);
		 // check if there is an install file
		 if (is_file($extract_directory . 'install.xml')) {
			 // if so, create a new dom document
			 $install_xml = simplexml_load_file($extract_directory . 'install.xml');
		 } else {
			 // if not, throw exception
			 throw new Exception('Install options file not found.');
		 } // end if
		 // copy files
		 foreach ($install_xml->files->file as $file) {
			 $name = $file;
			 if (array_key_exists($file['name'])) {
				 $name = $file['name'];
			 } // end function
			 // copy the file
			 copy($extract_directory . $name, $_whomp_storage_path . '/' . $file);
		 } // end foreach
		 // run database queries
		 $tables = array();
		 foreach ($install_xml->database->create as $create) {
			 // get the table options
			 $table_options = array();
			 foreach ($create->option as $table_option) {
				 $table_options[$table_option['type']] = $table_option;
			 } // end foreach
			 // get the index fields
			 $index_fields = array();
			 foreach ($create->index->field as $field) {
				 $index_fields[] = $field;
			 } // end foreach
			 // get the field options
			 $field_options = array();
			 foreach ($create->index->option as $index_option) {
				 $index_options[$table_option['type']] = $table_option;
			 } // end foreach
			 // create the create table array
			 $tables[$create->table] = array('fields' => $create->fields,
			 								'table_options' => $table_options,
											'index' => $create->index['name'],
											'index_fields' => $index_fields,
											'index_options' => $index_options);
		 } // end foreach
		 // create the tables
		 $_whomp_database->createTables($tables);
		 // register available options
		 foreach ($install_xml->add->register) {
			 // TODO
		 } // end foreach
	 } // end function
	 
 } // end class
?>