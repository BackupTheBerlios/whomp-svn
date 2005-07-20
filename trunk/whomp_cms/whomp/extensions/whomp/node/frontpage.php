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
 class Whomp_Node_Frontpage implements Whomp_Node, Whomp_Editable {
	 
	 /**
	  * The specified content type
	  * 
	  * @var string $_content_type
	  * @access protected
	  */
	 protected $_content_type;
	 
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
	  * This should be an array with formats as keys and content-types as 
	  * values. For example:
	  * <pre>
	  * Array (
	  * 	'html' => 'text/html'
	  * 	'xhtml+xml' => 'application/xhtml+xml'
	  * 	'xhtml' => 'application/xhtml+xml'
	  * )
	  * </pre>
	  * 
	  * @var array $layouts
	  * @access public
	  * @deprecated
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
	  * This should be an array with formats and/or content types as the 
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
	  * The node content
	  * 
	  * @var string $content
	  * @access public
	  */
	 public $content;
	 
	 /* ++ Whomp_Node Methods ++ */
	 
	 /**
	  * Whomp_Node_Frontpage constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the node
	  * @global class access to the database
	  * @todo finish this
	  */
	 public function loadNode($options) {
		 global $_whomp_database;
		 
		 // set the node information
		 foreach ($options as $key => $value) {
			 $this->$key = $value;
		 } // end foreach
		 // get the node information from the database
		 $queryValues = array($this->id);
		 $query = 'SELECT * FROM `#__' . $this->language . '_node_types_whomp_node_frontpage` WHERE `id` = %d;';
		 $_whomp_database->setQuery($query, $queryValues);
		 $_whomp_database->query();
		 $result = $_whomp_database->loadRow();
		 // set the content
		 $this->content = $result['content'];
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
	  * @throws Exception if the specified format does not exist
	  * @global string the whomp storage url
	  * @global array the user's accept headers
	  * @global class access to the configuration options
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
	  */
	 public function renderPage() {
		 global $_whomp_storage_url, $_whomp_accept_headers, $_whomp_template_class;
		 
		 // check if content type was supplied
		 if ($this->_content_type == '') {
			 // if not, find the most acceptable content type
			 $content_types = array_intersect_key($_whomp_accept_headers['formats'], $this->layouts);
			 $content_types = array_keys($content_types);
			 $this->_content_type = $content_types[0];
		 } // end if
		 // check if the format is available
		 if (array_key_exists($this->_content_type, $this->layouts)) {
			 // if so, place the node xml in the template xml
			 $_whomp_template_class->insertNodeXml($this->getNodeXml(), $this->layouts[$this->_content_type]['layout']);
			 // insert the node xsl
			 $_whomp_template_class->insertNodeXsl($_whomp_storage_url . $this->getNodeXslPath(), $this->layouts[$this->_content_type]['template'], $this->layouts[$this->_content_type]['format']);
			 // transform the xml to the desired format with xsl
			 $_whomp_template_class->transform();
			 // output the page
			 $options = $_whomp_template_class->render();
		 } else {
			 // if not, throw exception
			 throw new Exception('The specified file format does not exist.');
		 } // end if
			 
		 // add more information to the options array
		 $options['language'] = $this->language;
		 $options['page'] = $this->_page;
		 return $options;	 
	 } // end function
	 
	 /* -- Whomp_Node methods -- */
	 
	 /* ++ Whomp_Editable methods ++ */
	 
	 /**
	  * Renders the node in an editable form
	  * 
	  * This function should use the configured editor to determine what is 
	  * required to make the node editable or a custom interface may be used.
	  * Also should check for adequate permissions to edit.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global class access to the whomp editor class
	  * @global string the whomp storage url
	  */
	 public function renderEditable() {
		 global $_whomp_template_class, $_whomp_storage_url;
		 
		 // place the node xml in the template xml
		 $_whomp_template_class->insertEditableNodeXml($this->getNodeXml(), $this->layouts[$this->_content_type]['layout']);
		 // insert the node xsl
		 $_whomp_template_class->insertEditableNodeXsl($_whomp_storage_url . $this->getNodeXslPath(), $this->layouts[$this->_content_type]['template'], $this->layouts[$this->_content_type]['format']);
		 // transform the editable xml to the desired format with xsl
		 $_whomp_template_class->transformEditable();
		 // output the page
		 $options = $_whomp_template_class->render();
	 } // end function
	 
	 /**
	  * Saves the edited node
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
	  * @todo Implement this
	  */
	 public function save() {
	 } // end function
	 
	 /**
	  * Prints the node xml
	  * 
	  * Outputs only the node xml file. The type should be set to 'text/xml' for
	  * compatibility.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function printXml() {
		 
		 header('Content-type: text/xml');
		 echo $this->getNodeXml()->saveXml();
	 } // end function
	 
	 /**
	  * Prints the node xsl
	  * 
	  * Outputs only the node xsl file. The type should be set to 'text/xml' for
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
		 readfile($_whomp_storage_path . $this->getNodeXslPath());
	 } // end function
	 
	 /**
	  * Prints the node schema validation information
	  * 
	  * Outputs only the node schema validation file. Should communicate with
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
		 readfile($_whomp_storage_path . $this->getNodeSchemaPath());
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
		 global $_whomp_editor_class, $_whomp_base_url, $_whomp_storage_url;
		 
		 // send the whomp editor the required information
		 echo $_whomp_editor_class->getConfig($_whomp_base_url . $this->_page . '?whomp_operation=xml', $_whomp_storage_url . $this->getNodeXslPath(), $_whomp_storage_url . $this->getNodeSchemaPath());
	 } // end function
	 
	 /* -- Whomp_Editable methods -- */
	 
	 /**
	  * Gets the node's XML representation
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 protected function getNodeXml() {
		 
		 $xml = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<whomp_node_frontpage name="{$this->name}">
	<title>Test frontpage</title>
	<content>
		{$this->content}
	</content>
</whomp_node_frontpage>
XML;
		 $dom = new DOMDocument();
		 $dom->loadXml($xml);
		 return $dom;
	 } // end function
	 
	 /**
	  * Gets the path to the node's XSL file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @todo add more output formats
	  */
	 protected function getNodeXslPath() {
		 
		 // xhtml+xml is the only currently supported format
		 return '/repository/whomp/node/frontpage/xsl/xhtml.xsl';
	 } // end function
	 
	 /**
	  * Gets the path to the node's Schema file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 protected function getNodeSchemaPath() {
		 
		 return '/repository/whomp/node/frontpage/schema.rng.xml';
	 } // end function
 } // end class
?>