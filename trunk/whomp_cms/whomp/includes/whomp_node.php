<?php
/**
 * /whomp/includes/whomp_node.php
 * 
 * <p>Whomp node file. Includes the 
 * {@link Whomp_Node Whomp_Node} class.</p>
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
  * The Whomp node class
  * 
  * <p>Implements node objects for Whomp.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  */
 class Whomp_Node {
	 
	 /**
	  * The node class
	  * 
	  * @var class $_node_class
	  * @access private
	  */
	 private $_node_class;
	 
	 /**
	  * Whomp_Node constructor
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the node
	  * @global class access to the database
	  * @global class access to the configuration options
	  * @global string storage path
	  */
	 public function __construct($options = array()) {
		 global $_whomp_database, $_whomp_configuration, $_whomp_storage_path;
		 
		 // set the page and format
		 $this->_page = $options['page'];
		 $this->_format = $options['format'];
		 // get the node
		 $node = explode('/', $this->_page);
		 $the_node = array_pop($node);
		 if (empty($the_node)) {
			 $the_node = array_pop($node);
		 } // end if
		 if ($the_node === null) {
			 $the_node = $_whomp_configuration->node_default_node;
		 } // end if
		 // get the node information from the database
		 try {
			 $query = 'SELECT * FROM `#__nodes` WHERE `name` = \'' . $the_node . '\';';
			 $_whomp_database->setQuery($query);
			 $_whomp_database->query();
			 $node_info = $_whomp_database->loadRow();
		 } catch (Exception $e) {
			 whomp_output_exception($e, true);
		 } // end try
		 // check if the node was found
		 if (empty(node_info)) {
			 // if not, set status to 404 and get error node
			 header('Status: 404 Not Found');
			 $the_node = $_whomp_configuration->node_error_node;
			 try {
				 $query = 'SELECT * FROM `#__nodes` WHERE `name` = \'' . $the_node . '\';';
				 $_whomp_database->setQuery($query);
				 $_whomp_database->query();
				 $node_info = $_whomp_database->loadRow();
			 } catch (Exception $e) {
				 whomp_output_exception($e, true);
			 } // end try
		 } // end if
		 // set the node information
		 foreach ($node_info as $key => $value) {
			 $this->$key = $value;
		 } // end foreach
		 // check if the node class file exists
		 try {
			 if (is_file($_whomp_storage_path . '/node_types/' . $this->name . '/' . $this->name . '.php')) {
				 // if so, require it
				 require_once($_whomp_storage_path . '/node_types/' . $this->name . '/' . $this->name . '.php');
			 } else {
				 // if not, throw exception
				 throw new Exception('Exception opening ' . $this->name . ' class file. File does not exist.');
			 } // end if
		 } catch (Exception $e) {
			 whomp_output_exception($e, true);
		 } // end try
		 // create the node class
		 $class_string = $this->type;
		 $this->_node_class = new $class_string($this->name);
	 } // end function
	 
	 /**
	  * Renders the page
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function renderPage() {
		 
	 } // end function