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
  * <ul>Array ( 
  * 	<li>'page' => the page requested</li>
  * 	<li>'format' => the format requested</li>
  * 	<li>'node' => the node requested</li>
  * )</ul>
  * </p>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @return array the requested node and format
  * @global class access to the configuration options
  */
 function whomp_get_requested_page() {
	 global $_whomp_configuration;
	 
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
	 
	 // get the node
	 $node = explode('/', $page);
	 $the_node = array_pop($node);
	 if (empty($the_node)) {
		 $the_node = array_pop($node);
	 } // end if
	 if ($the_node === null) {
		 $the_node = $_whomp_configuration->node_default_node;
	 } // end if
	 
	 // return the array
	 return array('page' => $page, 'format' => $format, 'node' => $the_node);
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
 
 /**
  * Gets the best content type
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access protected
  * @throws Exception
  * @param string $format the requested format
  * @param array $formats the formats as keys and the content type as values
  * @global array the user's accept headers
  * @return string the selected content type
  */
 protected function whomp_get_content_type($format, $formats) {
	 global $_whomp_accept_headers;
	 
	 // check if format was supplied
	 if ($format != '') {
		 // if so, see if it is a known format
		 if (array_key_exists($format, $formats)) {
			 // if so, set the content type accordingly
			 return $formats($format); 
		 } else {
			 // if not, check if it is a known content type
			 if (in_array($format, $formats)) {
				 // if so, set it to the content type
				 return $format;
			 } else {
				 // if not, throw exception
				 throw new Exception('Unknown content type: ' . $format);
			 } // end if
		 } // end if
	 } else {
		 // if not, use the user's accept headers
		 foreach ($_whomp_accept_headers['formats'] as $format) {
			 // see if it is a known format
			 if (array_key_exists($format, $formats)) {
				 // if so, set the content type accordingly
				 return $formats($format); 
			 } else {
				 // if not, check if it is a known content type
				 if (in_array($format, $formats)) {
					 // if so, set it to the content type
					 return $format;
				 } // end if
			 } // end if
		 } // end foreach
		 // if it is not found, throw an exception
		 throw new Exception('No acceptable format was found.');
	 } // end if
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
  * @param string $node the requested node
  * @return object the node object
  */
 function whomp_get_node_array($node) {
	 
	 // get the node information from the database
	 try {
		 $query = 'SELECT * FROM `#__nodes` WHERE `name` = \'' . $node . '\';';
		 $_whomp_database->setQuery($query);
		 $_whomp_database->query();
		 $node_array = $_whomp_database->loadRow();
	 } catch (Exception $e) {
		 whomp_output_exception($e, true);
	 } // end try
	 // check if the node was found
	 if (empty($node_array)) {
		 // if not, set status to 404 and get error node
		 header('Status: 404 Not Found');
		 $node = $_whomp_configuration->node_error_node;
		 try {
			 $query = 'SELECT * FROM `#__nodes` WHERE `name` = \'' . $node . '\';';
			 $_whomp_database->setQuery($query);
			 $_whomp_database->query();
			 $node_array = $_whomp_database->loadRow();
		 } catch (Exception $e) {
			 whomp_output_exception($e, true);
		 } // end try
	 } // end if
	 return $node_array;
 } // end function
 
 /* -- NODE FUNCTIONS -- */
 
 /* ++ FILE FUNCTIONS ++ */
 
 /**
  * Get's the contents of an included file
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param string $file the file to include
  * @return string the contents of the file after it is evaluated
  */
 function whomp_include_file_string($file) {
	 
	 // start output buffering to capture output
	 ob_start();
	 // include the file
	 include($file);
	 // return the string and end output buffering
	 return ob_get_clean();
 } // end function
 
 /* -- FILE FUNCTIONS -- */
 
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