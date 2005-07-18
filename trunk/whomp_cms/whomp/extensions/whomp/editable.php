<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/editable.php
 * 
 * Whomp editable file. Includes the {@link Whomp_Editable Whomp_Editable} interface.
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
  * The Whomp editable interface
  * 
  * Defines editability for whomp extensions.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Editable {
	 
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
	  */
	 public function renderEditable(); // end function
	 
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
	  */
	 public function save(); // end function
	 
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
	 public function printXml(); // end function
	 
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
	  */
	 public function printXsl(); // end function
	 
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
	  */
	 public function printSchema(); // end function
	 
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
	  */
	 public function printConfig(); // end function
	 
 } // end interface
?>