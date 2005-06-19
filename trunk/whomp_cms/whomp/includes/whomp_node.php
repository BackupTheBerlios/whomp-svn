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
 abstract class Whomp_Node {
	 
	 /**
	  * The template class
	  * 
	  * @var class $_template_class
	  * @access protected
	  */
	 protected $_template_class;
	 
	 /**
	  * The specified format
	  * 
	  * @var string $_format
	  * @access protected
	  */
	 protected $_format;
	 
	 /**
	  * The requested page
	  * 
	  * @var string $_page
	  * @access protected
	  */
	 protected $_page;
	 
	 /**
	  * The node's language
	  * 
	  * @var string $language
	  * @access public
	  */
	 public $language;
	 
	 /**
	  * The available layouts for the node type
	  * 
	  * <p>This should be an array with formats as keys and content-types as 
	  * values. For example:
	  * <ul>Array (
	  * 	<li>'html' => 'text/html'</li>
	  * 	<li>'xhtml+xml' => 'application/xhtml+xml'</li>
	  * 	<li>'xhtml' => 'application/xhtml+xml'</li>
	  * )</ul>
	  * </p>
	  * 
	  * @var array $layouts
	  * @access public
	  */
	 public $formats;
	 
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
	  * The node's language
	  * 
	  * @var string $language
	  * @access public
	  */
	 public $language;
	 
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
	  * <p>This should be an array with formats and/or content types as the 
	  * keys and an array containing the template and layout information. 
	  * For example:
	  * <ul>Array (
	  * 	<li>
	  * 		<ul>'html' => Array ( 
	  * 			<li>'template' => the template to use</li>
	  * 			<li>'layout' => the layout to use</li>
	  * 		</ul>
	  * 	</li>
	  * )</ul>
	  * This information should be in the database.</p>
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
	  * @access protected
	  */
	 protected $_group;
	 
	 /**
	  * The node's user permissions
	  * 
	  * @var array $user
	  * @access protected
	  */
	 protected $_user;
	 
	 /**
	  * Headers that should be sent with this node
	  * 
	  * @var array $_headers
	  * @access protected
	  */
	 protected $_headers;
	 
	 /**
	  * Whether this page should be shown if a user is logged in
	  * 
	  * @var boolean $_show_logged
	  * @access protected
	  */
	 protected $_show_logged;
	 
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
		 
		 // set the node information
		 foreach ($options as $key => $value) {
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
	  * @global string the whomp storage path
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
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
				 $this->_template_class = new $class_string($this->layouts[$this->_format]['layout'], $this->_format, $this->formats);
				 // place the node xml in the template xml
				 $this->_template_class->insertNodeXml($this->getNodeXml());
				 // place the node xsl in the template xsl
				 $this->_template_class->insertXslImport($this->getNodeXslPath());
				 // transform the xml to the desired format with xsl
				 $this->_template_class->transform();
				 // output the page
				 $options = $this->_template_class->render();
			 } else {
				 // if not, throw exception
				 throw new Exception('The specified template could not be found. The file does not exist.');
			 } // end if
		 } else {
			 // if not, throw exception
			 throw new Exception('The specified file format does not exist.');
		 } // end if
		 // add more information to the options array
		 $options['language'] = $this->language;
		 $options['page'] = $this->_page;
		 return $options;	 
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
	 
	 /**
	  * Gets the node's XML representation
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 abstract public function getNodeXml();
	 
	 /**
	  * Gets the path to the node's XSL file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 abstract public function getNodeXslPath();
	 
 } // end class
?>