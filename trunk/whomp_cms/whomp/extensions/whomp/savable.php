<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/savable.php
 * 
 * Whomp editable file. Includes the {@link Whomp_Savable Whomp_Savable} interface.
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
  * The Whomp savable interface
  * 
  * Defines savability for whomp extensions.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Savable {
	 
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
	  */
	 public function save(); // end function
	 
 } // end interface
?>