<?php
/* $Id$ */
/**
 * /whomp/includes/whomp_user.php
 * 
 * <p>Whomp user file. Includes the 
 * {@link Whomp_User Whomp_User} class.</p>
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
  * The Whomp user class
  * 
  * <p>Implements user abilities in Whomp.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 public class Whomp_User {
	 
	 /**
	  * The user's id
	  * 
	  * @var int $id
	  * @access public
	  */
	 public $id = 0;
	 
	 /**
	  * The user's name
	  * 
	  * @var string $name
	  * @access public
	  */
	 public $name = '';
	 
	 /**
	  * The username
	  * 
	  * @var string $username
	  * @access public
	  */
	 public $username = '';
	 
	 /**
	  * The user's type
	  * 
	  * @var string $usertype
	  * @access public
	  */
	 public $usertype = '';
	 
	 /**
	  * The user's email address
	  * 
	  * @var string $email
	  * @access public
	  */
	 public $email = '';
	 
	 /**
	  * The user's join date
	  * 
	  * @var string $join_date;
	  * @access public
	  */
	 public $register_date = '';
	 
	 /**
	  * The user's last visit date
	  * 
	  * @var string $last_visit_date;
	  * @access public
	  */
	 public $last_visit_date = '';
	 
	 /**
	  * Whomp_User constructor
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the user
	  */
	 public function __construct($options = array()) {
		 
		 // initialize variables
		 foreach ($options as $key => $value) {
			 $this->$key = $value;
		 } // end foreach
	 } // end function
 } // end class
?>