<?php
/**
 * /whomp/includes/whomp_cache.php
 * 
 * <p>Whomp cache file. Includes the 
 * {@link Whomp_Cache Whomp_Cache} class.</p>
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
  * The Whomp cache class
  * 
  * <p>Implements caching abilities in Whomp.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  */
 class Whomp_Cache {
	 
	 /**
	  * Whomp_Cache constructor
	  * 
	  * @author Schmalls / Joshua Thompson
	  * @version 0.0.0
	  * @since 0.0.0
	  * @param array $options options for the cache
	  * @todo implement this
	  */
	 public function __construct($options) {
		 
	 } // end function
	 
	 /**
	  * Starts the cache
	  * 
	  * <p>If the node is available cached, it will output the page with the 
	  * correct header information. If it is not 
	  * available, then it will start output buffering.<p>
	  * <p>It also makes sure that the node is stored in the correct language 
	  * and format for the user.</p>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @param string $requested the requested page or block
	  * @param boolean $exit whether or not we should exit if it is available
	  * @todo implement this
	  */
	 function start($requested, $exit) {
		 
	 } // end function
	 
	 /**
	  * Stops the cache
	  * 
	  * <p>If caching is enabled, it will cache the page or block and write 
	  * it to a file. Then it will stop output buffering and compress the 
	  * page if it is configured to do so.</p>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @todo implement this
	  */
	 function end() {
		 
	 } // end function
 } // end class
?>