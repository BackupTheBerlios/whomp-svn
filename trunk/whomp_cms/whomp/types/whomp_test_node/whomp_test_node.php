<?php
/* $Id$ */
/**
 * /whomp/node_types/whomp_test_node.php
 * 
 * Whomp test node file. Includes the {@link Whomp_Test_Node Whomp_Test_Node} 
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
  * The Whomp test node class
  * 
  * Implements a testing node for Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Test_Node extends Whomp_Node {
	 
	 /**
	  * Whomp_Test_Node constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the node
	  * @global class access to the cache
	  * @global class access to the database
	  * @todo finish this
	  */
	 public function __construct($options = array()) {
		 global $_whomp_database;
		 
		 // call the parent constructor
		 parent::__construct($options);
		 // get the node information from the database
		 $queryValues = array($this->id);
		 $query = 'SELECT * FROM `#__' . $this->language . '_node_types_whomp_test_node` WHERE `id` = %d;';
		 $_whomp_database->setQuery($query, $queryValues);
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
<whomp_test_node name="{$this->name}">
	<title>{$this->title}</title>
	<content>
		{$this->content}
	</content>
</whomp_test_node>
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
		 return $_whomp_storage_url . '/types/whomp_test_node/xsl/xhtml.xsl';
	 } // end function 
 } // end class
?>