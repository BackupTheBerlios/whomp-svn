<?php
/* $Id$ */
/**
 * /whomp/includes/whomp_current_user.php
 * 
 * Whomp current user file. Includes the 
 * {@link Whomp_Current_User Whomp_Current_User} class.
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
 require_once($_whomp_storage_path . '/includes/whomp_user.php');
 
 /**
  * The Whomp current user class
  * 
  * Implements methods for the current Whomp user. It extends the 
  * {@link Whomp_User Whomp_User} class.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo implement user deletion functions
  */
 class Whomp_Current_User extends Whomp_User {
	 
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
				 $query = 'SELECT * FROM `#__users` WHERE md5(`id`) = %s;';
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
	 public function isLoggedIn() {
		 
		 return $this->_logged_in;
	 } // end function
	 
	 /**
	  * Logs the user in
	  * 
	  * First it checks if it has the correct user information or not. If it 
	  * doesn't then it gets the correct information from the database and 
	  * adds it to the current user option and sets the user id cookie. Then 
	  * it checks if the supplied password was correct.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if the password is incorrect
	  * @param int $username the username the user supplied
	  * @param string $password the password the user supplied
	  * @global class access to the database
	  * @return boolean whether it was successful or not
	  */
	 public function login($username, $password) {
		 global $_whomp_database;
		 
		 // check if we already have the user's information
		 if ($username != $this->username) {
			 // if not, get the information from the database
			 try {
				 $queryValues = array($username);
				 $query = 'SELECT * FROM `#__users` WHERE `username` = %s;';
				 $_whomp_database->setQuery($query, $queryValues);
				 $_whomp_database->query();
				 $current_user_options = $_whomp_database->loadRow();
				 // call the parent constructor with the new options
				 parent::__construct($current_user_options);
				 // set the user id cookie
				 setcookie('whomp_id', md5($this->id));
			 } catch (Exception $e) {
				 whomp_ouput_exception($e, true);
			 } // end try
		 } // end if	 
		 // check if the password supplied is correct
		 if (md5($password) == $this->_password) {
			 // if so, set user to logged in
			 $this->_logged_in = true;
		 } else {
			 // if not, throw an exception
			 throw new Exception('The supplied password was incorrect.');
		 } // end if
	 } // end function
	 
	 /**
	  * Logs the user out
	  * 
	  * The user id cookie is unset and then all current user options are reset.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function logout() {
		 
		 // remove the user id cookie
		 setcookie('whomp_id', false);
		 unset($_COOKIE['whomp_id']);
		 // reconstruct the current user
		 $this = new Whomp_Current_User();
	 } // end function
	 
	 /**
	  * Creates a new user
	  * 
	  * The options array should be in the following format:
	  * <pre>
	  * Array (
	  * 	'username' => the username
	  * 	'password' => the supplied password
	  * 	'confirm_password' => the confirm password
	  * 	'name' => the user's name
	  * 	'usertype' => the user's type
	  * 	'email' => the user's email address
	  * )
	  * </pre>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if the passwords do not match
	  * @param array $options the user options
	  * @global class access to the database
	  * @todo add confirmation functionality
	  */
	 public function create($options) {
		 global $_whomp_database;
		 
		 // check if the passwords match
		 if ($options['password'] == $options['confimr_password']) {
			 // if so, insert the user
			 try {
				 $insert = array('username' => $options['username'],
			 					 'password' => md5($options['password']),
								 'name' => $options['name'],
								 'usertype' => $options['usertype'],
								 'email' => $options['email'],
								 'last_visit_date' => date('Y-m-d H:i:s'),
								 'register_date' => date('Y-m-d H:i:s'));
				 $_whomp_database->insert('#__users', $insert);
				 // set the user id cookie
				 setcookie('whomp_id', $_whomp_database->insertId());
			 } catch (Exception $e) {
				 whomp_output_exception($e, true);
			 } // end try
		 } else {
			 // if not, throw an exception
			 throw new Exception('The supplied passwords do not match.');
		 } // end if
	 } // end function
	 
 } // end class
?>