<?php
/* $Id: whomp_language.php 37 2005-07-07 06:10:30Z schmalls $ */
/**
 * /whomp/extensions/whomp/language.php
 * 
 * Whomp language file. Includes the 
 * {@link Whomp_Language Whomp_Language} class.
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
  * Abstract class contains all of the English language definitions. This 
  * class cannot be instantiated directly. Instead a subclass should be made 
  * and named 'Whomp_Language_en' or instead of 'en' the language code. The 
  * subclass should extend 'Whomp_Language' and define all of the properties 
  * in the specified language.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo finish implementing this
  */
 abstract class Whomp_Language {
	 
	 /**
	  * Returns the specified message
	  * 
	  * This function takes one or two variables. The first is the name of 
	  * the message requested and the second is an optional replacement 
	  * array. The replacement array should include information that will be 
	  * inserted into the message and the message will contain placeholders 
	  * suitable for the vsprintf function. If the message contains more than 
	  * one placeholder of the same type, it is recommended that the 
	  * placeholders be numbered so that the different word ordering of 
	  * languages can be enabled.
	  * 
	  * Examples:
	  * 
	  * <code>
	  * /*
	  *  * Click here message
	  *  *
	  *  * <p>The first string should be the user's name and the second 
	  *  * should be the link location.</p>
	  *  * @var string $_click_message
	  *  * @access protected
	  *  {@*}
	  * protected $_click_message = '%1%s please click <a href="%2%s">here</a>.'
	  * </code>
	  * The previous is what a message definition should look like. You 
	  * notice that the documentation tells us what we should provide in the 
	  * string.
	  * 
	  * <code>
	  * // create the values array
	  * $values = array($_whomp_current_user->name, 'http://www.example.com');
	  * // get the message to display
	  * $message = $_whomp_language->getMessage('_click_message', $values);
	  * </code>
	  * The previous example shows how a programmer should create the values 
	  * and send it to this function to get the formatted message.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $message the message to return
	  * @param array $values the values to 
	  * @return string the message's value after optionally inserting values
	  */
	 final public function getMessage($message, $values = null) {
		 
		 // get the message
		 $message = $this->$message;
		 // check if values were provided
		 if ($values !== null) {
			 // if so, update the message
			 $message = vsprintf($message, $values);
		 } // end if
		 // return the message
		 return $message;
	 } // end function
 } // end class
?>