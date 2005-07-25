<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/node/frontpage.php
 * 
 * Whomp test node file. Includes the {@link Whomp_Node_Frontpage Whomp_Node_Frontpage} 
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
  * The Whomp frontpage node class
  * 
  * Implements a frontpage node for Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Node_Frontpage implements Whomp_Node, Whomp_Editable, Whomp_Savable {
	 
	 /**
	  * The specified content type
	  * 
	  * @var string $_content_type
	  * @access protected
	  */
	 public $content_type;
	 
	 /**
	  * The requested page
	  * 
	  * @var string $_page
	  * @access protected
	  */
	 public $page;
	 
	 /**
	  * The node's language
	  * 
	  * @var string $language
	  * @access public
	  */
	 public $language;
	 
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
	  * This should be an array with content types as the 
	  * keys and an array containing the template and layout information. 
	  * For example:
	  * <pre>
	  * Array (
	  * 	'text/html' => 
	  * 		Array (
	  * 			'layout' => the layout to use
	  * 			'template' => the template to use
	  * 			'format' => the format to use
	  * 		)
	  * 	'application/xhtml+xml' => 
	  * 		Array (
	  * 			...
	  * 		)
	  * )
	  * </pre>
	  * This information should be in the database.
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
	  * The xml path
	  * 
	  * @var string $_xml_path
	  * @access protected
	  */
	 protected $_xml_path;
	 
	 /**
	  * The xsl path
	  * 
	  * @var string $_xsl_path
	  * @access protected
	  */
	 protected $_xsl_path;
	 
	 /**
	  * The schema path
	  * 
	  * @var string $_schema_path
	  * @access protected
	  */
	 protected $_schema_path;
	 
	 /**
	  * Whether it is being edited or not
	  * 
	  * @var boolean $_edit
	  * @access protected
	  */
	 protected $_edit = false;
	 
	 /* ++ Whomp_Node Methods ++ */
	 
	 /**
	  * Whomp_Node_Frontpage constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the node
	  */
	 public function loadNode($options) {
		 
		 // set the node information
		 foreach ($options as $key => $value) {
			 $this->$key = $value;
		 } // end foreach
		 // set the paths
		 $this->_xml_path = '/repository/whomp/node/frontpage/nodes/' . $options['name'] . '.xml';
		 $this->_xsl_path = '/repository/whomp/node/frontpage/xsl/xhtml.xsl';
		 $this->_schema_path = '/repository/whomp/node/frontpage/schema/relaxng.xml';
	 } // end function
	 
	 /**
	  * Renders the page
	  * 
	  * This method finds the template engine class file and initializes 
	  * the template class. It then uses the template class to insert the 
	  * node's XML and XSL information. After it has been inserted, the 
	  * template is transformed to the desired ouput format with XSL and 
	  * then printed to the screen.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global class access to the template class
	  * @global string the whomp storage url
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
	  */
	 public function renderNode() {
		 global $_whomp_template_class, $_whomp_storage_url;
		
		 // insert the node xml	 
		 $_whomp_template_class->insertXml($_whomp_storage_url . $this->_xml_path);
		 // insert the node xsl
		 $_whomp_template_class->insertXsl($_whomp_storage_url . $this->_xsl_path);
		 // transform the document
		 $_whomp_template_class->transformTemplate();
		 // render the document and get options
		 $options = $_whomp_template_class->renderTemplate();
		 // add more information to the options array
		 $options['language'] = $this->language;
		 $options['page'] = $this->page;
		 // check if we are editing
		 if ($this->_edit) {
			 // if so, set content type to html
			 header('Content-type: text/html');
		 } // end if
		 return $options;	 
	 } // end function
	 
	 /* -- Whomp_Node methods -- */
	 
	 /* ++ Whomp_Editable methods ++ */
	 
	 /**
	  * Makes editable
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function makeEditable() {
		 global $_whomp_template_class;
		 
		 // make template class editable
		 $_whomp_template_class->makeEditable();		 
		 // set to editable
		 $this->_edit = true;
	 } // end function
	 
	 /**
	  * Prints the xml
	  * 
	  * Outputs only the xml file. The type should be set to 'text/xml' for
	  * compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global string the whomp storage path
	  */
	 public function printXml() {
		 global $_whomp_storage_path;
		 
		 header('Content-type: text/xml');
		 readfile($_whomp_storage_path . $this->_xml_path);
	 } // end function
	 
	 /**
	  * Prints the xsl
	  * 
	  * Outputs only the xsl file. The type should be set to 'text/xml' for
	  * compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global string the whomp storage path
	  */
	 public function printXsl() {
		 global $_whomp_storage_path;
		 
		 header('Content-type: text/xml');
		 readfile($_whomp_storage_path . $this->_xsl_path);
	 } // end function
	 
	 /**
	  * Prints the schema validation information
	  * 
	  * Outputs only the schema validation file. Should communicate with
	  * the configured editor to determine which schema type to print. The type
	  * should be set to 'text/xml' for compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global string the whomp storage path
	  */
	 public function printSchema() {
		 global $_whomp_storage_path;
		 
		 header('Content-type: text/xml');
		 readfile($_whomp_storage_path . $this->_schema_path);
	 } // end function
	 
	 /**
	  * Prints the editor configuration file
	  * 
	  * Outputs the editor configuration file. Should communicate with the 
	  * configured editor to detemrine the correct format for output.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global class access to the whomp editor class
	  */
	 public function printConfig() {
		 global $_whomp_editor_class, $_whomp_base_url;
		 
		 // send the whomp editor the required information
		 header('Content-type: text/xml');
		 echo $_whomp_editor_class->getConfig($_whomp_base_url . '/' . $this->page . '?whomp_operation=xml', $_whomp_base_url . '/' . $this->page . '?whomp_operation=xsl', $_whomp_base_url . '/' . $this->page . '?whomp_operation=schema');
	 } // end function
	 
	 /* -- Whomp_Editable methods -- */
	 
	 /* ++ Whomp_Savable methods ++ */
	 
	 /**
	  * Saves the object
	  * 
	  * This function should communicate with the configured editor to determine
	  * what information is returned so that it can be handled properly. Or if 
	  * a custom interface was used for editing this is not required. Also 
	  * should check for adequate permissions to save.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global string the whomp storage path
	  */
	 public function save() {
		 global $_whomp_storage_path;
		 
		 // check if magic quotes is enabled
		 if (get_magic_quotes_gpc() == 1) {
			 // if so, strip slashes
			 $content = stripslashes($_POST['content']);
		 } else {
			 // if not, get content
			 $content = $_POST['content'];
		 } // end if
		 // write the xml file
		 $check = file_put_contents($_whomp_storage_path . $this->_xml_path, $content);
		 // check if we wrote anything
		 if ($check != 0) {
			 // if so, set status to 204
			 header('HTTP/1.x 204 No Content');
		 } else {
			 // if not, set status to 403 and print error message
			 header("HTTP/1.x 403 Forbidden");
			 echo '<parsererror xmlns="http://www.mozilla.org/newlayout/xml/parsererror.xml">';
			 echo 'Could not write to ' . $this->_xml_path;
			 echo '</parsererror>';
		 } // end if
	 } // end function
	 
	 /* -- Whomp_Savable methods -- */
	 
 } // end class
?>