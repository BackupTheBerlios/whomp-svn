<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/editor.php
 * 
 * Whomp editor file. Includes the {@link Whomp_Editor Whomp_Editor} interface.
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
  * The Whomp editor interface
  * 
  * Defines functionality for whomp editors.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Editor {
	 
	 /**
	  * Loads the editor
	  * 
	  * Inserts all of the required head data into the head data array.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the node
	  */
	 public function loadEditor($options); // end function
	 
	 /**
	  * Creates a configuration file and returns it
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xml_url the url for the node's xml file
	  * @param string $xsl_url the url for the node's xsl file
	  * @param string $schema_url the url for the node's schema file
	  * @return string the config file
	  */
	 public function getConfig($xml_url, $xsl_url, $schema_url); // end function
 } // end function
?>