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
	  * The template class
	  * 
	  * @var class $_template_class
	  * @access private
	  */
	 private $_template_class;
	 
	 /**
	  * The node's id
	  * 
	  * @var int $id
	  * @access public
	  */
	 public $id;
	 
	 /**
	  * The node's name
	  * 
	  * @var string $name
	  * @access public
	  */
	 public $name;
	 
	 /**
	  * The node's type
	  * 
	  * @var string $type
	  * @access public
	  */
	 public $type;
	 
	 /**
	  * The node's last modification date
	  * 
	  * @var string $modified
	  * @access public
	  */
	 public $modified;
	 
	 /**
	  * The userid of who last modified the node
	  * 
	  * @var int $modified_by
	  * @access public
	  */
	 public $modified_by;
	 
	 /**
	  * The node's preferred layouts
	  * 
	  * @var array $layouts
	  * @access public
	  */
	 public $layouts;
	 
	 /**
	  * The node's parents
	  * 
	  * @var array $parents
	  * @access public
	  */
	 public $parents;
	 
	 /**
	  * The node's children
	  * 
	  * @var array $children
	  * @access public
	  */
	 public $children;
	 
	 /**
	  * The node's relatives
	  * 
	  * @var array $relatives
	  * @access public
	  */
	 public $relatives;
	 
	 /**
	  * The node's group permissions
	  * 
	  * @var array $group
	  * @access private
	  */
	 private $_group;
	 
	 /**
	  * The node's user permissions
	  * 
	  * @var array $user
	  * @access private
	  */
	 private $_user;
	 
	 /**
	  * Whomp_Node constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
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
		 // create layout array
		 $layouts = explode("\n", $this->layouts);
		 $this->layouts = array();
		 foreach ($layouts as $layout) {
			 $layout = explode(',', $layout);
			 $this->layouts[$layout[0]] = array('template' => $layout[1], 'layout' => $layout[2]);
		 } // end foreach
		 // create parents array
		 $this->parents = explode(',', $this->parents);
		 // create children array
		 $this->children = explode(',', $this->children);
		 // create relatives array
		 $this->relatives = explode(',', $this->relatives);
		 // create the group permissions array
		 $group_permissions = explode("\n", $this->_group);
		 $this->_group = array();
		 foreach ($group_permissions as $group_permission) {
			 $group_permission = explode(',', $group_permission);
			 $this->_group[$group_permission[0]] = $group_permission[1];
		 } // end foreach
		 // create the user permissions array
		 $user_permissions = explode("\n", $this->_user);
		 $this->_user = array();
		 foreach ($user_permissions as $user_permission) {
			 $user_permission = explode(',', $user_permission);
			 $this->_user[$user_permission[0]] = $user_permission[1];
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
	  * <p>This method finds the template engine class file and initializes 
	  * the template class. It then uses the template class to insert the 
	  * node's XML and XSL information. After it has been inserted, the 
	  * template is transformed to the desired ouput format with XSL and 
	  * then printed to the screen.</p> 
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  */
	 public function renderPage() {
		 global $_whomp_storage_path;
		 
		 // check if the format is available
		 if (array_key_exists($this->_format, $this->layouts)) {
			 // if so, check if the template file exists
			 if (is_file($_whomp_storage_path . '/templates/' . $this->layouts[$this->_format]['template'] . '/template.php')) {
				 // if so, require it
				 require_once($_whomp_storage_path . '/templates/' . $this->layouts[$this->_format]['template'] . '/' . $this->layouts[$this->_format]['template'] . '.php');
				 // create the template class
				 $class_string = $this->layouts[$this->_format]['template'];
				 $this->_template_class = new $class_string($this->layouts[$this->_format]['layout'], $this->_format, $this->_node_class->formats);
				 // place the node xml in the template xml
				 $this->_template_class->insertNodeXml($this->_node_class->getNodeXml());
				 // place the node xsl in the template xsl
				 $this->_template_class->insertXslImport($this->_node_class->getNodeXslPath());
				 // transform the xml to the desired format with xsl
				 $this->_template_class->transform();
				 // output the page
				 $this->_template_class->render();
			 } else {
				 // if not, throw exception
				 throw new Exception('The specified template could not be found. The file does not exist.');
			 } // end if
		 } else {
			 // if not, throw exception
			 throw new Exception('The specified file format does not exist.');
		 } // end if		 
	 } // end function
	 
	 /**
	  * Checks if the group has adequate permissions
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param int $groupid the group's id
	  * @param int $level the permissions level
	  * @return boolean whether the group has high enough permissions
	  */
	 public function checkGroup($groupid, $level) {
		 
		 // check if the group's permissions are defined
		 if (array_key_exists($groupid, $this->_group)) {
			 // if so, check if they have adequate permissions
			 if ($this->_group[$groupid] >= $level) {
				 // if so, return true
				 return true;
			 } else {
				 // if not, return false
				 return false;
			 } // end if
		 } else {
			 // if not, return false
			 return false;
		 } // end if
	 } // end function
	 
	 /**
	  * Checks if the user has adequate permissions
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param int $userid the user's id
	  * @param int $level the permissions level
	  * @return boolean whether the user has high enough permissions
	  */
	 public function checkUser($userid, $level) {
		 
		 // check if the user's permissions are defined
		 if (array_key_exists($userid, $this->_user)) {
			 // if so, check if they have adequate permissions
			 if ($this->_user[$userid] >= $level) {
				 // if so, return true
				 return true;
			 } else {
				 // if not, return false
				 return false;
			 } // end if
		 } else {
			 // if not, return false
			 return false;
		 } // end if
	 } // end function
	 
 } // end class
?>