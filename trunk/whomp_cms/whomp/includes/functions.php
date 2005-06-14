<?php
/**
 * /whomp/includes/functions.php
 * 
 * <p>Includes Whomp user functions.</p>
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
 
 /* ++ REQUEST FUNCTIONS ++ */
 
 /**
  * Get the requested page and format
  * 
  * <p>It returns an array in the following form:<br />
  * Array {<br /> 
  * 	'page' => the page requested<br />
  * 	'format' => the format requested<br />
  * }</p>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @return array the requested node and format
  * @todo implement this function
  */
 function whomp_get_requested_page() {
	 
	 // create requested file and format string
	 $requested = $_SERVER['REQUEST_URI'];
	 // remove index.php from the string
	 $requested = str_replace('index.php', '', $requested);
	 // remove directories from the string
	 $requested = str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $requested);
	 
	 // get the requested format
	 $format = strrchr($requested, '.');
	 // check if a format was supplied
	 if (($format === false) || (strpos($format, '/') === false)) {
		 // if not, set to empty string
		 $format = '';
	 } else {
		 // if so, remove the dot from the beginning of format
		 $format = substr($format, 1);
	 }// end if
	 
	 // get the requested page without the format
	 $page = str_replace('.' . $format, '', $requested);
	 
	 // return the array
	 return array('page' => $page, 'format' => $format);
 } // end function
 
 /**
  * Get the user's accept headers
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @return array the accept headers in a friendlier format
  */
 function whomp_get_accept_headers() {
	 
	 /**
	  * Internal function to get formatted and sorted arrays
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @param string $string the string from which to make the array
	  * @return array the formatted and sorted array
	  */ 
	 function whomp_get_accept_header_array($string) {
		 $array = array();
		 foreach(explode(',', $string) as $item) {
			 $item = explode(';q=', $item);
			 // check if q value was provided
			 if (count($item) == 2) {
				 // if so, add item and q value to array
				 $array[$item[0]] = (float)$item[1];
			 } else if(!empty($item)) {
				 // if not, add item and a q value of 1.0 to array
				 $array[$item[0]] = (float)1.0;
			 } // end if
		 } // end foreach
		 // sort the array and return it
		 asort($array, SORT_NUMERIC);
		 return $array;
	 } // end function
	 
	 // create the accepted formats array
	 $formats = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT']);
	 
	 // create the accepted languages array
	 $languages = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	 
	 // create the accepted character sets array
	 $charsets = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT_CHARSET']);
	 
	 // create the accepted encodings array
	 $encodings = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT_ENCODING']);
	 
	 // return the array
	 return array('formats' => $formats, 
	 			  'languages' => $languages, 
				  'charsets' => $charsets, 
				  'encodings' => $encodings);
 } // end function
 
 /* -- REQUEST FUNCTIONS -- */
 
 /* ++ NODE FUNCTIONS ++ */
 
 /**
  * Gets the requested node object
  * 
  * <p>Retrieves the node information from the database as an object.</p>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param array $node the requested node information
  * @return object the node object
  */
 function whomp_get_node_object($node) {
	 
 } // end function
 
 /**
  * Loads the node object's classes
  * 
  * <p>Creates 
  */
 
 /* -- NODE FUNCTIONS -- */
 
 /* ++ EXCEPTION FUNCTIONS ++ */
 
 /**
  * Ouputs the exception
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param Exception $exception the exception to output information about
  * @param string $message the message to display if debugging is disabled
  * @param boolean $exit whether the script should exit or not
  * @global class access to the configuration options
  */
 function whomp_output_exception(Exception $exception, $exit = false) {
	 global $_whomp_configuration;
	 
	 // create debug message
	 $message = <<<ERROR
<p>Error [{$exception->getCode()}] in {$exception->getFile()} on line {$exception->getLine()}:<br /><br />
{$exception->getMessage()}<br /><br />
{$exception->getTraceAsString()}</p>
ERROR;
	 // check if this should be logged or printed
	 if ($_whomp_configuration->debug_log) {
		 // if so, write to log file
		 try {
			 // create whomp file write options
			 $file_write_options = array('file' => $_whomp_base_path . $_whomp_configuration->debug_log_file,
			 							 'data' => $message,
										 'append' => true);
			 // append the debug log file
			 whomp_file_write($file_write_options);
		 } catch (Exception $e) {
			 var_dump($e);
		 } // end try
	 } // end if
	 // check if we should exit
	 if ($exit) {
		 // if so, exit
		 exit $message;
	 } else {
		 // if not, print message
		 echo $message; 
	 } // end if
 } // end function
 
 /* -- EXCEPTION FUNCTIONS -- */
?>