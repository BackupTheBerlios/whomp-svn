<?php
/* $Id$ */
/**
 * /whomp/includes/whomp_current_user.php
 * 
 * <p>Whomp current user file. Includes the 
 * {@link Whomp_Current_User Whomp_Current_User} class.</p>
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
  * Require the {@link /whomp/includes/whomp_user.php Whomp_User} class file
  */
 require_once($_whomp_base_path . '/includes/whomp_user.php');
 
 /**
  * The Whomp current user class
  * 
  * <p>Implements methods for the current Whomp user. It extends the 
  * {@link Whomp_User Whomp_User} class.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo implement login functions, etc
  */
 public class Whomp_Current_User extends Whomp_User {
	 
	 /**
	  * md5 of the password
	  * 
	  * @var string $_password
	  * @access private
	  */
	 private $_password = null;
	 
	 /**
	  * Whether the user is logged in or not
	  * 
	  * @var boolean $_logged_in
	  * @access private
	  */
	 private $_logged_in = false;
	 
	 /**
	  * Whomp_Current_User constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global class access to the database
	  */
	 public function __construct() {
		 global $_whomp_database;
		 
		 // check if whomp_id cookie is set
		 if (isset($_COOKIE['whomp_id'])) {
			 // if so, get the user information from the database
			 try {
				 $queryValues = array($_COOKIE['whomp_id']);
				 $query = 'SELECT * FROM `#__whomp_users` WHERE md5(`id`) = %s;';
				 $_whomp_database->setQuery($query, $queryValues);
				 $_whomp_database->query();
				 $current_user_options = $_whomp_database->loadRow();
			 } catch (Exception $e) {
				 whomp_ouput_exception($e, true);
			 } // end try
			 // check if whomp_logout_time cookie is set
			 if ((isset($_COOKIE['whomp_logout_time'])) && ($_COOKIE['whomp_logout_time'] < microtime(true))) {
				 // if not, set logged in to true
				 $this->_logged_in = true;
			 } // end if
		 } else {
			 // if not, create null user information
			 $current_user_options = array();
		 } // end if
		 parent::__construct($current_user_options);
	 } // end function
	 
	 /**
	  * Checks if the user is logged in or not
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return boolean whether the user is logged in or not
	  */
	 function isLoggedIn() {
		 
		 return $this->_logged_in;
	 } // end function
 } // end class
?>