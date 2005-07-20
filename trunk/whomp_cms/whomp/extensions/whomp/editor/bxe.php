<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/editor/bxe.php
 * 
 * Whomp bxe editor file. Includes the {@link Whomp_Editor_Bxe Whomp_Editor_Bxe} class.
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
  * The Whomp bxe editor class
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Editor_Bxe {
	 
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
	  * @global array the whomp head data
	  * @global string the whomp storage url
	  */
	 public function loadEditor($options) {
		 global $_whomp_head_data, $_whomp_storage_url;
		 
		 $_whomp_head_data['script'][] = '<script src="' . $_whomp_storage_url . '/extensions/whomp/editor/bxe/bxeLoader.js" type="text/javascript"></script>';
	 } // end function
	 
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
	  * @global string the whomp storage path
	  * @global string the whomp storage url
	  * @return string the config file
	  */
	 public function getConfig($xml_url, $xsl_url, $schema_url) {
		 global $_whomp_storage_path, $_whomp_storage_url;
		 
		 // get the configuration xml file
		 $config = file_get_contents($_whomp_storage_path . '/repository/whomp/editor/bxe/config.xml');
		 // replace the url placeholders with the correct urls
		 $config = str_replace('{{xmlfile}}', $xml_url, $config);
		 $config = str_replace('{{xslfile}}', $xsl_url, $config);
		 $config = str_replace('{{schemafile}}', $schema_url, $config);
		 $config = str_replace('{{_whomp_storage_url}}', $_whomp_storage_url, $config);
		 // return the configuration file as a string
		 return $config;
	 } // end function
 } // end function
?>