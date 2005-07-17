<?php
/* $Id: whomp_database.php 46 2005-07-12 05:17:52Z schmalls $ */
/**
 * /whomp/extensions/whomp/database.php
 * 
 * Whomp database file. Includes the {@link Whomp_Database Whomp_Database} 
 * class.
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
 require_once($_whomp_storage_path . '/extensions/adodb/adodb-exceptions.inc.php');
 
 /**
  * Require the ADOdb include file
  */
 require_once($_whomp_storage_path . '/extensions/adodb/adodb.inc.php');
 
 /**
  * The Whomp database class
  * 
  * Implements database access for Whomp. It uses the 
  * {@link http://adodb.sourceforge.net ADOdb} library for database 
  * abstraction.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo add table deletion and backup functions
  * @todo add date handling functions
  * @todo add blob and/or clob functions
  */
 class Whomp_Database {
	 
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
	  * Whether magic quotes is enabled or not
	  * 
	  * @var boolean $_magic_quotes
	  * @access protected
	  */
	 protected $_magic_quotes;
	 
	 /**
	  * Whomp_Database constructor
	  * 
	  * The options array should be in the following form:
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
		 // set magic quotes
		 $_magic_quotes = (boolean)get_magic_quotes_gpc();
		 // check if debug option was enabled
		 if (array_key_exists('debug', $options) && $options['debug'] === true) {
			 // if so, enable debugging
			 $this->_db->debug = true;
		 } // end if
	 } // end function
	 
	 /**
	  * Sets the query
	  * 
	  * Sets the database query after replacing the table prefix 
	  * placeholder to the configured table prefix. It also inserts the 
	  * query values into the query. The query and query values should be in 
	  * a form suitable for the vsprintf function.
	  * 
	  * Examples:
	  * 
	  * <code>
	  * $queryValues = array($id, $name);
	  * $query = 'SELECT * FROM `#__example_table` WHERE `id` = %d AND `name` = %s;';
	  * $_whomp_database->setQuery($query, $queryValues);
	  * </code>
	  * The previous example shows the use of query values, which are optional.
	  * 
	  * <code>
	  * $query = 'SELECT * FROM `#__example_table`;';
	  * $_whomp_database->setQuery($query);
	  * </code>
	  * The previous example shows a query without query values.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $query the database query to be set
	  * @param array $queryValues values that need to be inserted into the query
	  * @param string $prefix_placeholder the table prefix placeholder
	  */
	 public function setQuery($query, $queryValues = null, $prefix_placeholder = '#__') {
		 
		 // translate the query and then set it
		 $this->_query = str_replace($prefix_placeholder, $this->_table_prefix, $query);
		 // see if any query values were sent
		 if ($queryValues !== null) {
			 // if so, escape each string
			 foreach ($queryValues as $key => $value) {
				 // check if the string is numeric
				 if (!is_numeric($value)) {
					 // if not, escape it
					 $queryValues[$key] = $this->escapeString($value);
				 } // end if
			 } // end foreach
			 // then update the query
			 $this->_query = vsprintf($this->_query, $queryValues);
		 } // end if
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
	  * First it checks if a bind values array was provided and uses them 
	  * if so. A multidimensional array can be sent as the bind values. 
	  * After it executes the query it updates the internal counter and 
	  * returns the database result.
	  * 
	  * Examples:
	  * 
	  * <code>
	  * $query = 'INSERT INTO `#__example_table` (`name`, `email`) VALUES (' . $_whomp_database->param('name') . ', ' . $_whomp_database->param('email');';
	  * $database->setQuery($query);
	  * </code>
	  * The previous example is the setup for the following two examples. 
	  * This is so that the '#__' placeholder will be replaced.
	  * 
	  * <code>
	  * $bindValues = array('roger', 'roger@example.com');
	  * $_whomp_database->query($bindValues);
	  * </code>
	  * The previous example uses the previously set query and some new bind 
	  * parameters. The query will be prepared (if the database supports 
	  * it), and the will be executed with the parameters inserted.
	  * 
	  * <code>
	  * $bindValues = array();
	  * foreach ($people as $person) {
	  * 	$bindValues[] = array($person['name'], $person['email']);
	  * } // end foreach
	  * $_whomp_database->query($bindValues);
	  * </code>
	  * The previous example will do the same as the first, except it will 
	  * run the query for each of the sets of bind values stored in the 
	  * array. This can be faster than running the query each time in a 
	  * foreach loop especially if prepared statements are supported.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $bindValues the values to be bound to the query
	  * @return ADORecordSet the database result
	  */
	 public function query($bindValues = null) {
		 
		 // check if bind values were sent
		 if ($bindValues !== null) {
			 // if so, prepare the query
			 $this->_query = $this->_db->Prepare($this->_query);
			 // execute the query and bind values
			 $this->_result = $this->_db->Execute($this->_query, $bindValues);
		 } else {
			 // if not, execute the query
			 $this->_result = $this->_db->Execute($this->_query);
		 } // end if
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
	 public function loadRow($result = null) {
		 
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
	 public function loadRowList($result = null) {
		 
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
	 public function loadObject($result = null) {
		 
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
	 public function loadObjectList($result = null) {
		 
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
	 public function numRows($result = null) {
		 
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
	  * Generate tables from the provided options
	  * 
	  * The tables array contains options to create tables in the following 
	  * format:
	  * <pre>
	  * Array (
	  * 	tablename1 => array (
	  * 					'fields' => '`id` I KEY AUTO, `name` C(50) NOTNULL DEFAULT \'example\'',
	  * 					'table_options' => array('mysql' => 'TYPE=MyISAM'),
	  * 					'index' => 'name',
	  * 					'index_fields' => '`name`',
	  * 					'index_options' => array('UNIQUE')),
	  * 	tablename2 => array (
	  * 		...
	  * }
	  * </pre>
	  * 
	  * The syntax of the different array options is compatible with the data 
	  * dictionary options of adodb. For more information on syntax read the
	  * {@link http://phplens.com/lens/adodb/docs-datadict.htm Data Dictionary Documentation}.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options the options
	  * @param string $prefix_placeholder the table prefix placeholder
	  * @return boolean whether it was successful or not
	  */
	 public function createTables($tables, $prefix_placeholder = '#__') {
		 
		 // create new datadictionary
		 $data_dictionary = NewDataDictionary($this->_db);
		 // create the sql array
		 $sql_array = array();
		 // check if tables is an array
		 if (is_array($tables)) {
			 // if so, generate the sql
			 foreach ($tables as $table => $options) {
				 // replace the table placeholder
				 $table = str_replace($prefix_placeholder, $this->_table_prefix, $table);
				 // check if fields were provided
				 if (array_key_exists('fields', $options)) {
					 // if so, check if the table options were provided
					 if (array_key_exists('table_options', $options)) {
						 $sql = $data_dictionary->CreateTableSQL($table, $options['fields'], $options['table_options']);
					 } else {
						 $sql = $data_dictionary->CreateTableSQL($table, $options['fields']);
					 } // end if
					 // check if sql is an array
					 if (is_array($sql)) {
						 // if so, combine it with the sql array
						 $sql_array = array_merge($sql_array, $sql);
					 } else if ($sql !== false) {
						 $sql_array[] = $sql;
					 } else {
						 throw new Exception('Error creating table sql for ' . $table);
					 } // end if
				 } // end if
				 // check if an index was provided
				 if (array_key_exists('index', $options) && array_key_exists('index_fields', $options)) {
					 // if so, check if index options were provided
					 if (array_key_exists('index_options', $options)) {
						 $sql = $data_dictionary->CreateIndexSQL($options['index'], $table, $options['index_fields'], $options['index_options']);
					 } else {
						 $sql = $data_dictionary->CreateIndexSQL($options['index'], $table, $options['index_fields']);
					 } // end if
					 // check if sql is an array
					 if (is_array($sql)) {
						 // if so, combine it with the sql array
						 $sql_array = array_merge($sql_array, $sql);
					 } else if ($sql !== false) {
						 $sql_array[] = $sql;
					 } else {
						 throw new Exception('Error creating index sql for ' . $table);
					 } // end if
				 } // end if
			 } // end foreach
		 } // end if
		 // create the tables
		 $result = $data_dictionary->ExecuteSQLArray($sql_array);
		 // check if there were errors
		 if ($result == 0) {
			 throw new Exception('Error running table creation sql.');
		 } else if ($result == 1) {
			 // errors return false
			 return false;
		 } else {
			 // no errors return true
			 return true;
		 } // end if
	 } // end function
	 
	 /**
	  * Escape a string with the database specified escape function
	  * 
	  * Safeguards against sql injection.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $string the string to escape
	  * @return string the escaped string
	  */
	 public function escapeString($string) {
		 
		 // return the escaped string
		 return $this->_db->qstr($string, $this->_magic_quotes);
	 } // end function
	 
	 /**
	  * Returns the correctly formatted bind variable placeholder
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $name the bind variable's name
	  * @return string the bind variable placeholder formatted for the database
	  */
	 public function param($name) {
		 
		 // return the placeholder
		 return $this->_db->Param($name);
	 } // end function
	 
	 /**
	  * Inserts the record into the specified table
	  * 
	  * The record should be an array with the column names as keys and the 
	  * column values as values.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $table the table to insert into
	  * @param array $record the record to insert
	  * @param string $prefix_placeholder the table prefix placeholder
	  */
	 public function insert($table, $record, $prefix_placeholder = '#__') {
		 
		 // change table prefix
		 $table = str_replace($prefix_placeholder, $this->_table_prefix, $table);
		 // insert
		 $this->_db->AutoExecute($table, $record, 'INSERT');
	 } // end function
	 
	 /**
	  * Updates the record in the specified table
	  * 
	  * The record should be an array with the column names as keys and the 
	  * column values as values.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $table the table to insert into
	  * @param array $record the record to insert
	  * @param string $prefix_placeholder the table prefix placeholder
	  */
	 public function update($table, $record, $where = false, $prefix_placeholder = '#__') {
		 
		 // change table prefix
		 $table = str_replace($prefix_placeholder, $this->_table_prefix, $table);
		 // insert
		 $this->_db->AutoExecute($table, $record, 'UPDATE', $where);
	 } // end function
	 
 } // end class
?>