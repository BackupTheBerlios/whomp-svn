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
  * The Whomp node editable interface
  * 
  * Defines editability for whomp extensions. Extensions should implement this 
  * functionality if they are editable.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Editable {
	 
	 /**
	  * Makes editable
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function makeEditable(); // end function
	 
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
	  */
	 public function printXml(); // end function
	 
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
	  */
	 public function printXsl(); // end function
	 
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
	  */
	 public function printSchema(); // end function
	 
	 /**
	  * Prints the editor configuration file
	  * 
	  * Outputs the editor configuration file. Should communicate with the 
	  * configured editor to determine the correct format for output.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function printConfig(); // end function
	 
	 
 } // end interface
?>