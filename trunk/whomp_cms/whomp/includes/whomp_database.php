<?php
/**
 * /whomp/includes/whomp_database.php
 * 
 * <p>Whomp database file. Includes the 
 * {@link Whomp_Database Whomp_Database} class.</p>
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
  * Require the ADOdb exceptions file
  */
 require_once($_whomp_storage_path . '/includes/adodb/adodb-exceptions.inc.php');
 
 /**
  * Require the ADOdb include file
  */
 require_once($_whomp_storage_path . '/includes/adodb/adodb.inc.php');
 
 /**
  * The Whomp database class
  * 
  * <p>Implements database access for Whomp. It uses the 
  * {@link http://adodb.sourceforge.net ADOdb} library for database 
  * abstraction.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo add table deletion and backup functionality using AXMLS
  */
 public class Whomp_Database {
	 
	 /**
	  * The database connector provided by ADOdb
	  * 
	  * @var class $_db
	  * @access protected
	  */
	 protected $_db;
	 
	 /**
	  * The database table prefix
	  * 
	  * @var string $_table_prefix
	  * @access protected
	  */
	 protected $_table_prefix = '';
	 
	 /**
	  * The current database query
	  * 
	  * @var string $_query
	  * @access protected
	  */
	 protected $_query = '';
	 
	 /**
	  * The current database result
	  * 
	  * @var class $_result
	  * @access protected
	  */
	 protected $_result;
	 
	 /**
	  * A running count of queries executed
	  * 
	  * @var int $_count
	  * @access protected
	  */
	 protected $_count = 0;
	 
	 /**
	  * Whomp_Database constructor
	  * 
	  * <p>The options array should be in the following form:
	  * <pre>
	  * Array (
	  * 	'type' => 'mysql'
	  * 	'host' => 'localhost'
	  * 	'username' => 'whomp_user'
	  * 	'password' => 'whomp_pass'
	  * 	'database' => 'whomp'
	  * 	'table_prefix' => 'whomp_'
	  * )
	  * </pre>
	  * </p>
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param array $options options for the database
	  */
	 public function __construct($options) {
		 
		 // initialize database connection
		 $this->_db = NewADOConnection($options['type']);
		 // connect to the database
		 $this->_db->PConnect($options['host'], 
		 					  $options['username'], 
							  $options['password'], 
							  $options['database']);
		 // set the table prefix
		 $this->_table_prefix = $options['table_prefix'];
		 // set the fetch mode to associative
		 $this->_db->SetFetchMode(ADODB_FETCH_ASSOC);
	 } // end function
	 
	 /**
	  * Sets the query
	  * 
	  * <p>Sets the database query after replacing the table prefix 
	  * placeholder to the configured table prefix</p>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $query The database query to be set
	  * @param string $prefix_placeholder The table prefix placeholder
	  */
	 public function setQuery($query, $prefix_placeholder = '#__') {
		 
		 // Translate the query and then set it
		 $this->_query = str_replace($prefix_placeholder, $this->_table_prefix, $query);
	 } // end function
	 
	 /**
	  * Gets the current query
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return string the current query
	  */
	 public function getQuery() {
		 
		 // return the query
		 return $this->_query;
	 } // end function
	 
	 /**
	  * Queries the database
	  * 
	  * <p>It sets the results of the query to the internal result, and also 
	  * returns it.</p>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return ADORecordSet the database result
	  * @todo implement variable binding ability
	  */
	 public function query() {
		 
		 // execute the query
		 $this->_result = $this->_db->Execute($this->_query);
		 // update the counter
		 $this->_count++;
		 // return the result
		 return $this->_result;
	 } // end function
	 
	 /**
	  * Returns the next row of the result as an array
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param ADORecordSet $result database result to use
	  * @return array the next row of the result
	  */
	 public function loadRow(ADORecordSet $result = null) {
		 
		 // check if a result was provided
		 if ($result === null) {
			 // if not, use the current result
			 $result = $this->_result;
		 } // end if
		 // return the row
		 return $result->FetchRow();
	 } // end function
	 
	 /**
	  * Returns the entire result as a multidimensional array
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param ADORecordSet $result database result to use
	  * @return array the result set
	  */
	 public function loadRowList(ADORecordSet $result = null) {
		 
		 // check if a result was provided
		 if ($result === null) {
			 // if not, use the current result
			 $result = $this->_result;
		 } // end if
		 // return the results
		 return $result->GetArray();
	 } // end function
	 
	 /**
	  * Returns the next row as an object
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param ADORecordSet $result database result to use
	  * @return object the next row of the result
	  */
	 public function loadObject(ADORecordSet $result = null) {
		 
		 // check if a result was provided
		 if ($result === null) {
			 // if not, use the current result
			 $result = $this->_result;
		 } // end if
		 // return the next row
		 return $result->FetchNextObj();
	 } // end function
	 
	 /**
	  * Returns the result set as an array of objects
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param ADORecordSet $result database result to use
	  * @return array the result set as an array of objects
	  */
	 public function loadObjectList(ADORecordSet $result = null) {
		 
		 // check if a result was provided
		 if ($result === null) {
			 // if not, use the current result
			 $result = $this->_result;
		 } // end if
		 // construct the return array
		 $return = array();
		 // get each object and add it to the return array
		 while (!$result->EOF) {
			 $return[] = $result->FetchNextObj();
		 } // end while
		 // return array
		 return $return;
	 } // end function
	 
	 /**
	  * Returns the number of affected rows for a delete or update statement
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return int the number of affected rows | false if function not supported
	  */
	 public function affectedRows() {
		 
		 // return the number of affected rows
		 return $this->_db->Affected_Rows();
	 } // end function
	 
	 /**
	  * Returns the number of rows in the result
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param ADORecordSet $result database result to use
	  * @return int the number of rows
	  */
	 public function numRows(ADORecordSet $result = null) {
		 
		 // check if a result was provided
		 if ($result === null) {
			 // if not, use the current result
			 $result = $this->_result;
		 } // end if
		 // return the number of rows
		 return $result->RecordCount(); 
	 } // end function
	 
	 /**
	  * Returns the last inserted id
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return int the last inserted id
	  */
	 public function insertId() {
		 
		 // return the inserted id
		 return $this->_db->Insert_ID();
	 } // end function
	 
	 /**
	  * Generate tables from an XML string using AXMLS
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xml the table creation XML
	  * @param string $prefix_placeholder The table prefix placeholder
	  * @global string the whomp storage path
	  */
	 public function createTables($xml, $prefix_placeholder = '#__') {
		 global $_whomp_storage_path;
		 
		 /**
		  * Require the AXMLS include file
		  */
		 require_once($_whomp_storage_path . '/includes/adodb/adodb-xmlschema.inc.php');
		 // create new schema
		 $schema = new adoSchema($this->_db);
		 // parse the xml to sql
		 $sql = $schema->ParseSchemaString($xml);
		 // replace prefix placeholder with correct table prefix
		 foreach ($sql as $key => $value) {
			 $sql[$key] = str_replace($prefix_placeholder, $this->_table_prefix, $value);
		 } // end foreach
		 // run the sql on the database
		 $schema->ExecuteSchema($sql);
	 } // end function
	 
 } // end class
?>