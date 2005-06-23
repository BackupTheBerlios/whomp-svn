<?php
/* $Id$ */
/**
 * /whomp/includes/whomp_language.php
 * 
 * <p>Whomp language file. Includes the 
 * {@link Whomp_Language Whomp_Language} class.</p>
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
  * The Whomp language class
  * 
  * <p>Abstract class contains all of the English language definitions. This 
  * class cannot be instantiated directly. Instead a subclass should be made 
  * and named 'Whomp_Language_en' or instead of 'en' the language code. The 
  * subclass should extend 'Whomp_Language' and define all of the properties 
  * in the specified language.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo finish implementing this
  */
 abstract public class Whomp_Language {
	 
	 /**
	  * Returns the specified message
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $message the property to return
	  * @return string the property's value
	  */
	 final public function getMessage($message) {
		 
		 // return the message
		 return $this->$message;
	 } // end function
 } // end class
?>