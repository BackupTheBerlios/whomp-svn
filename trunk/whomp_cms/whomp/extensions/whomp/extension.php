<?php
/* $Id$ */
/**
 * /whomp/includes/whomp_extension.php
 * 
 * Whomp extension file. Includes the 
 * {@link Whomp_Extension Whomp_Extension} class.
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
  * The Whomp extension class
  * 
  * Abstract class contains all of the necessary methods for an extension. This 
  * class should be extended by every extension.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo finish implementing this
  */
 interface Whomp_Extension {
	 
	 /**
	  * Whomp extension constructor
	  * 
	  * The constructor should register event functions, etc.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options configuration options
	  */
	 public function __construct($options); // end function
	 
 } // end class