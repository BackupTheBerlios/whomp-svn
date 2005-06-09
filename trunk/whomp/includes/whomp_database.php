<?php
/**
 * /whomp/includes/whomp_database.php
 * 
 * <p>Whomp database file. Includes the 
 * {@link Whomp_Database Whomp_Database} class.</p>
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
  * The Whomp database class
  * 
  * <p>Implements database access for Whomp. It uses the 
  * {@link http://adodb.sourceforge.net ADOdb} library for database 
  * abstraction.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  */
 class Whomp_Database {
	 
	 /**
	  * Whomp_Database constructor
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @param array $options options for the database
	  */
	 public function __construct($options) {
		 
	 } // end function
 } // end class
?>