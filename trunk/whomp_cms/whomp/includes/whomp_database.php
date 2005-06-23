<?php
/* $Id$ */
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
  * @todo add table deletion and backup functions using AXMLS
  * @todo add date handling functions
  * @todo add blob and/or clob functions
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
	  * Whether magic quotes is enabled or not
	  * 
	  * @var boolean $_magic_quotes
	  * @access protected
	  */
	 protected $_magic_quotes = (boolean)get_magic_quotes_gpc();
	 
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
	  * placeholder to the configured table prefix. It also inserts the 
	  * query values into the query. The query and query values should be in 
	  * a form suitable for the vsprintf function.<br />
	  * Examples:<br />
	  * <code>
	  * $queryValues = array($id, $name);
	  * $query = 'SELECT * FROM `#__example_table` WHERE `id` = %d AND `name` = %s;';
	  * $_whomp_database->setQuery($query, $queryValues);
	  * </code>
	  * The previous example shows the use of query values, which are optional.
	  * <code>
	  * $query = 'SELECT * FROM `#__example_table`;';
	  * $_whomp_database->setQuery($query);
	  * </code>
	  * The previous example shows a query without query values.
	  * </p>
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
				 $queryValues[$key] = $this->escapeString($value);
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
	  * <p>First it checks if a bind values array was provided and uses them 
	  * if so. A multidimensional array can be sent as the bind values. 
	  * After it executes the query it updates the internal counter and 
	  * returns the database result.<br />
	  * Examples:<br />
	  * <code>
	  * $query = 'INSERT INTO `#__example_table` (`name`, `email`) VALUES (' . $_whomp_database->param('name') . ', ' . $_whomp_database->param('email');';
	  * $database->setQuery($query);
	  * </code>
	  * The previous example is the setup for the following two examples. 
	  * This is so that the '#__' placeholder will be replaced.<br />
	  * <code>
	  * $bindValues = array('roger', 'roger@example.com');
	  * $_whomp_database->query($bindValues);
	  * </code>
	  * The previous example uses the previously set query and some new bind 
	  * parameters. The query will be prepared (if the database supports 
	  * it), and the will be executed with the parameters inserted.<br />
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
	  * foreach loop especially if prepared statements are supported.</p>
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
	  * <p>For more information on the XML files please visit the 
	  * {@link http://adodb-xmlschema.sourceforge.net/docs/ AXMLS Documentation Site}.</p>
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
	 
	 /**
	  * Escape a string with the database specified escape function
	  * 
	  * <p>This safeguards against sql injection.</p>
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
	 
 } // end class
?>