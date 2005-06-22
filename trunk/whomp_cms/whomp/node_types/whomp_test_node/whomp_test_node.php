<?php
/**
 * /whomp/node_types/whomp_test_node.php
 * 
 * <p>Whomp test node file. Includes the 
 * {@link Whomp_Test_Node Whomp_Test_Node} class.</p>
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
  * The Whomp test node class
  * 
  * <p>Implements a testing node for Whomp.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 public class Whomp_Test_Node extends Whomp_Node {
	 
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
	  */
	 public function __construct($options = array()) {
		 global $_whomp_database;
		 
		 // call the parent constructor
		 parent::__construct($options);
		 // get the node information from the database
		 $queryValues = array($this->language,
		 					  $_whomp_database->escapeString($this->id));
		 $query = vsprintf('SELECT * FROM `#__%s_node_types_whomp_test_node` WHERE `id` = \'%d\';', $queryValues);
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
		 
		 return <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<whomp_test_node name="{$this->name}">
	<title>{$this->title}</title>
	<content>
		{$this->content}
	</content>
</whomp_test_node>
XML;
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
		 global $_whomp_storage_path;
		 
		 // xhtml+xml is the only supported format
		 return $_whomp_storage_path . '/node_types/whomp_test_node/xsl/xhtml.xsl';
	 } // end function 
 } // end class
?>