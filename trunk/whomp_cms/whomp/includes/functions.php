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
  * <pre>Array ( 
  * 	'page' => the page requested
  * 	'format' => the format requested
  * 	'node' => the node requested
  * 	'content_type' => the best content type available
  * )
  * </pre>
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
	 
	 // try to get the content-type
	 try {
		 $content_type = whomp_get_content_type($format, $_whomp_configuration->known_content_types);
	 } catch (Exception $e) {
		 whomp_output_exception($e, true);
	 } // end try
	 
	 // return the array
	 return array('page' => $page, 
	 			  'format' => $format, 
				  'node' => $the_node, 
				  'content_type' => $content_type);
 } // end function
 
 /**
  * Get the user's accept headers
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @global class access to the configuration information
  * @return array the accept headers in a friendlier format
  */
 function whomp_get_accept_headers() {
	 global $_whomp_configuration;
	 
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
	 // make the languages only include those that are available
	 $languages = array_intersect_key($languages, $_whomp_configuration->language_languages);
	 
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
  * @param array $options the requested node options
  * @global class access to the database
  * @global array the accept headers
  * @return object the node object
  */
 function whomp_get_node_array($options) {
	 global $_whomp_database, $_whomp_accept_headers;
	 
	 // get the node information from the database
	 try {
		 // check if any languages are available
		 if (!empty($_whomp_accept_headers['languages'])) {
			 // if so, go until we find a node in an acceptable language
			 foreach ($_whomp_accept_headers['languages'] as $language) {
				 $queryValues = array($language,
				 					  $_whomp_database->escapeString($options['node']));
				 $query = vsprintf('SELECT * FROM `#__%s_nodes` WHERE `name` = \'%s\';', $queryValues);
				 $_whomp_database->setQuery($query);
				 $_whomp_database->query();
				 $node_array = $_whomp_database->loadRow();
				 // check if the node was available
				 if (!empty($node_array)) {
					 // if so, set language and break
					 $node_language = $language;
					 break;
				 } // end if
			 } // end foreach
		 } else {
			 // if not, throw exception
			 throw new Exception('No languages are available.');
		 } // end if
	 } catch (Exception $e) {
		 whomp_output_exception($e, true);
	 } // end try
	 // check if the node was found
	 if (!empty($node_array)) {
		 // if so, set status to ok
		 header('Status: 200 OK');
	 } else {
		 // if not, set status to 404 and get error node
		 header('Status: 404 Not Found');
		 try {
			 // go until we find an error node in an acceptable language
			 foreach ($_whomp_accept_headers['languages'] as $language) {
				 $queryValues = array($language,
				 					  $_whomp_database->escapeString($_whomp_configuration->node_error_node));
				 $query = vsprintf('SELECT * FROM `#__%s_nodes` WHERE `name` = \'%s\';', $queryValues);
				 $_whomp_database->setQuery($query);
				 $_whomp_database->query();
				 $node_array = $_whomp_database->loadRow();
				 // check if the node was available
				 if (!empty($node_array)) {
					 // if so, set language and break
					 $node_language = $language;
					 break;
				 } // end if
			 } // end foreach
		 } catch (Exception $e) {
			 whomp_output_exception($e, true);
		 } // end try
	 } // end if
	 // add page and format information to the node array
	 $node_array['_page'] = $options['page'];
	 $node_array['_format'] = $options['format'];
	 $node_array['language'] = $node_language;
	 return $node_array;
 } // end function
 
 /**
  * Renders the page with the specified node
  * 
  * <p>This function finds the correct node type class file and loads the 
  * node type class with the node's parameters. Then it calls the node's 
  * renderPage function to render the page. It throws an Exception if there 
  * are any errors.</p>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception
  * @param array $options the node information
  * @global string the whomp storage path
  * @return array information about the page suitable for sending to Whomp_Cache::end()
  */
 function whomp_render_page($options) {
	 global $_whomp_storage_path;
	 
	 // check if the node type class file exists
	 if (is_file($_whomp_storage_path . '/node_types/' . strtolower($options['type']) . '/' . strtolower($options['type']) . '.php')) {
		 // if so, require it
		 require_once($_whomp_storage_path . '/node_types/' . strtolower($options['type']) . '/' . strtolower($options['type']) . '.php');
		 // create the node class
		 $class_string = $options['type'];
		 $node_class = new $class_string($options);
		 // render the page
		 return $node_class->renderPage();
	 } else {
		 // if not, throw exception
		 throw new Exception('The ' . $options['type'] . ' class file does not exist.');
	 } // end if
 } // end function
 
 /**
  * Gets a node's XML data and XSL path and returns it in an array
  * 
  * <p>First the node's information is retrieved from the database. Then the 
  * node's type class file is required and the class is loaded with the 
  * node's options. Then the getNodeXml and getNodeXslPath methods are 
  * called and the information is returned. The array is in the following 
  * format:
  * <ul>Array(
  * 	<li>'xml' => the xml as a string</li>
  * 	<li>'xsl' => the path to the xsl file</li>
  * </ul>
  * </p>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception
  * @param array $options the node information
  * @global string the whomp storage path
  * @return array the XML data and XSL path
  */
 function whomp_get_node_xml_xsl($options) {
	 global $_whomp_storage_path;
	 
	 // get the node information
	 $options = whomp_get_node_array($options);
	 // check if the node type class file exists
	 if (is_file($_whomp_storage_path . '/node_types/' . $options['type'] . '/' . $options['type'] . '.php')) {
		 // if so, require it
		 require_once($_whomp_storage_path . '/node_types/' . $options['type'] . '/' . $options['type'] . '.php');
		 // create the node class
		 $class_string = $options['type'];
		 $node_class = new $class_string($options);
		 // get the XML data
		 $return['xml'] = $node_class->getNodeXml();
		 // get the XSL path
		 $return['xsl'] = $node_class->getNodeXslPath();
	 } else {
		 // if not, throw exception
		 throw new Exception('The ' . $options['type'] . ' class file does not exist.');
	 } // end if
	 // return the information
	 return $return;
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
		 file_put_contents($_whomp_base_path . $_whomp_configuration->debug_log_file, $message, FILE_APPEND);
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