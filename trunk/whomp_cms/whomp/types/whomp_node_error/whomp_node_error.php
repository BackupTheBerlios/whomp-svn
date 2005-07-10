<?php
/* $Id$ */
/**
 * /whomp/node_types/whomp_node_error.php
 * 
 * Whomp error node file. Includes the {@link Whomp_Node_Error Whomp_Node_Error} 
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
  * Require the {@link /whomp/includes/whomp_node.php Whomp_Node} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_node.php');
 
 /**
  * The Whomp error node class
  * 
  * Implements an error node for Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Node_Error extends Whomp_Node {
	 
	 /**
	  * Whomp_Node_Error constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the node
	  * @global class access to the database
	  * @todo finish this
	  */
	 public function __construct($options = array()) {
		 global $_whomp_database, $_whomp_cache;
		 
		 // call the parent constructor
		 parent::__construct($options);
		 // get the node information from the database
		 $queryValues = array($this->id);
		 $query = 'SELECT * FROM `#__' . $this->language . '_node_types_whomp_node_error` WHERE `id` = %d;';
		 $_whomp_database->setQuery($query, $queryValues);
		 $_whomp_database->query();
		 $result = $_whomp_database->loadRow();
		 // set the content
		 $this->error_message = $result['error_message'];
		 // turn off caching
		 $_whomp_cache->noCache();
	 } // end function
	 
	 /**
	  * Gets the node's XML representation
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function getNodeXml() {
		 
		 $xml = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<whomp_node_error name="{$this->name}">
	<title>404 Error</title>
	<content>
		{$this->error_message}
	</content>
</whomp_node_error>
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
	  */
	 public function getNodeXslPath() {
		 global $_whomp_storage_url;
		 
		 // xhtml+xml is the only supported format
		 return $_whomp_storage_url . '/node_types/whomp_node_error/xsl/xhtml.xsl';
	 } // end function 
 } // end class
?>