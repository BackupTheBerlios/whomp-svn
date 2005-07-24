<?php
/* $Id$ */
/**
 * /whomp/includes/functions.php
 * 
 * Includes Whomp user functions.
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
  * Autoloads class and interface extensions as necessary
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param string $class the class name
  * @global string the whomp storage directory
  */
 function __autoload($class) {
	 global $_whomp_storage_path;
	 
	 // check if it is in the extension directory
	 if (is_file($_whomp_storage_path . '/extensions/' . str_replace('_', '/', strtolower($class)) . '.php')) {
		 // include extension
		 require_once($_whomp_storage_path . '/extensions/' . str_replace('_', '/', strtolower($class)) . '.php');
	 } // end if
 } // end function
 
 /* ++ COMPATIBILITY FUNCTIONS ++ */
 
 if (!function_exists('array_intersect_key')) {
	 /**
	  * Compatibility function
	  * 
	  * @author Tom Buskens <ortega@php.net>
	  */
	 function array_intersect_key() {
		 $args = func_get_args();
		 if (count($args) < 2) {
			 user_error('Wrong parameter count for array_intersect_key()', E_USER_WARNING);
			 return;
		 } // end if
		 // Check arrays
		 $array_count = count($args);
		 for ($i = 0; $i !== $array_count; $i++) {
			 if (!is_array($args[$i])) {
				 user_error('array_intersect_key() Argument #' .
				 ($i + 1) . ' is not an array', E_USER_WARNING);
				 return;
			 } // end if
		 } // end for
		 // Compare entries
		 $result = array();
		 foreach ($args[0] as $key1 => $value1) {
			 for ($i = 1; $i !== $array_count; $i++) {
				 foreach ($args[$i] as $key2 => $value2) {
					 if ((string) $key1 === (string) $key2) {
						 $result[$key1] = $value1;
					 } // end if
				 } // end foreach
			 } // end for
		 } // end foreach
		 return $result;
	 } // end function
 } // end if
 
 /* -- COMPATIBILITY FUNCTIONS -- */
 
 /* ++ REQUEST FUNCTIONS ++ */
 
 /**
  * Get the requested page and format
  * 
  * It returns an array in the following form:
  * <pre>
  * Array ( 
  * 	'page' => the page requested
  * 	'format' => the format requested
  * 	'node' => the node requested
  * 	'content_type' => the best content type available
  * )
  * </pre>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception if there is an unknown format
  * @return array the requested node and format
  * @global class access to the configuration options
  */
 function whomp_get_requested_page() {
	 global $_whomp_configuration;
	 
	 // create requested file and format string
	 $requested = $_SERVER['REQUEST_URI'];
	 // remove the query string from the string
	 $requested = str_replace('?' . $_SERVER['QUERY_STRING'], '', $requested);
	 // remove index.php from the string
	 $requested = str_replace('index.php', '', $requested);
	 // remove directories from the string
	 $requested = str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $requested);
	 
	 // get the requested format
	 $format = strrchr($requested, '.');
	 // check if a format was supplied
	 if (($format === false) || (strpos($format, '/') !== false)) {
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
		 if (array_key_exists($format, $_whomp_configuration->known_content_types)) {
			 $content_type = $_whomp_configuration->known_content_types[$format];
		 } else {
			 throw new Exception('Unknown format: ' . $format);
		 } // end if
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
				 $array[trim($item[0])] = (float)trim($item[1]);
			 } else if(!empty($item)) {
				 // if not, add item and a q value of 1.0 to array
				 $array[trim($item[0])] = (float)1.0;
			 } // end if
		 } // end foreach
		 // sort the array and return it
		 arsort($array, SORT_NUMERIC);
		 return $array;
	 } // end function
	 
	 // create the accepted formats array
	 if (array_key_exists('HTTP_ACCEPT', $_SERVER)) {
		 $content_types = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT']);
	 } else {
		 $content_types = array('*/*' => (float)1.0);
	 } // end if
	 
	 // create the accepted languages array
	 if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
		 $languages = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		 // make the languages only include those that are available
		 $languages = array_intersect_key($languages, $_whomp_configuration->language_languages);
	 } else {
		 $languages = array('*' => (float)1.0);
	 } // end if
	 
	 // create the accepted character sets array
	 if (array_key_exists('HTTP_ACCEPT_CHARSET', $_SERVER)) {
		 $charsets = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT_CHARSET']);
	 } else {
		 $charsets = array('*' => (float)1.0);
	 } // end if
	 
	 // create the accepted encodings array
	 if (array_key_exists('HTTP_ACCEPT_ENCODING', $_SERVER)) {
		 $encodings = whomp_get_accept_header_array($_SERVER['HTTP_ACCEPT_ENCODING']);
	 } else {
		 $encodings = array();
	 } // end if
	 
	 // return the array
	 return array('content_types' => $content_types, 
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
  * @throws Exception if no acceptable format is found
  * @param string $content_type the requested content type
  * @param array $layouts an array with content types as keys
  * @global array the user's accept headers
  * @return string the selected content type
  */
 function whomp_get_content_type($content_type, $layouts) {
	 global $_whomp_accept_headers;
	 
	 // check if format was supplied
	 if ($content_type != '') {
		 // if so, see if it is a known format
		 if (array_key_exists($content_type, $layouts)) {
			 // if so, set the content type accordingly
			 return $content_type; 
		 } else {
			 // if not, throw exception
			 throw new Exception('Unknown content type: ' . $content_type);
		 } // end if
	 } else {
		 // if not, use the user's accept headers
		 foreach ($_whomp_accept_headers['content_types'] as $content_type => $qvalue) {
			 // see if it is */*
			 if ($content_type == '*/*') {
				 return $content_type;
			 } else {
				 // see if it is a known format
				 if (array_key_exists($content_type, $layouts)) {
					 // if so, return the content type
					 return $content_type; 
				 } // end if
			 } // end if
		 } // end foreach
		 // if it is not found, throw an exception
		 throw new Exception('No acceptable format was found.');
	 } // end if
 } // end function
 
 /**
  * Gets the requested parameter or returns the default value
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param string $param the parameter for which to get the value
  * @param string $default the default return value
  * @param array $array the array from which to get the parameter
  * @return string the parameter value
  */
 function whomp_get_param($param, $default, $array = null) {
	 
	 // check if an array was supplied
	 if ($array === null) {
		 // if not, set it to request global
		 $array = $_REQUEST;
	 } // end if
	 return array_key_exists($param, $array) ? $array[$param] : $default;
 } // end function
 
 /* -- REQUEST FUNCTIONS -- */
 
 /* ++ NODE FUNCTIONS ++ */
 
 /**
  * Gets the requested node array
  * 
  * Retrieves the node information from the database as an array.
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param array $options the requested node options
  * @global class access to the database
  * @global array the accept headers
  * @global class access to the configuration options
  * @return object the node object
  */
 function whomp_get_node_array($options) {
	 global $_whomp_database, $_whomp_accept_headers, $_whomp_configuration;
	 
	 // get the node information from the database
	 $node_language = '';
	 try {
		 // check if any languages are available
		 if (!empty($_whomp_accept_headers['languages'])) {
			 // if so, go until we find a node in an acceptable language
			 foreach ($_whomp_accept_headers['languages'] as $language => $qvalue) {
				 $queryValues = array($options['node']);
				 $query = 'SELECT * FROM `#__' . $language . '_nodes` WHERE `name` = %s;';
				 $_whomp_database->setQuery($query, $queryValues);
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
			 foreach ($_whomp_accept_headers['languages'] as $language => $qvalue) {
				 $queryValues = array($_whomp_configuration->node_error_node);
				 $query = 'SELECT * FROM `#__' . $language . '_nodes` WHERE `name` = %s;';
				 $_whomp_database->setQuery($query, $queryValues);
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
	 $node_array['page'] = $options['page'];
	 $node_array['language'] = $node_language;
	 // create layout array
	 if ($node_array['layouts'] != '') {
		 $layouts = explode("\n", $node_array['layouts']);
		 $node_array['layouts'] = array();
		 foreach ($layouts as $layout) {
			 $layout = explode(',', $layout . ',,,');
			 $node_array['layouts'][trim($layout[0])] = array('layout' => trim($layout[1]),
				 											  'template' => trim($layout[2]),
															  'format' => trim($layout[3]));
		 } // end foreach
	 } else {
		 $node_array['layouts'] = array();
	 } // end if
	 // create parents array
	 $node_array['parents'] = explode(',', $node_array['parents']);
	 // create children array
	 $node_array['children'] = explode(',', $node_array['children']);
	 // create relatives array
	 $node_array['relatives'] = explode(',', $node_array['relatives']);
	 // create the group permissions array
	 if ($node_array['_group'] != '') {
		 $group_permissions = explode("\n", $node_array['_group']);
		 $node_array['_group'] = array();
		 foreach ($group_permissions as $group_permission) {
			 $group_permission = explode(',', $group_permission);
			 $node_array['_group'][trim($group_permission[0])] = trim($group_permission[1]);
		 } // end foreach
	 } else {
		 $node_array['_group'] = array();
	 } // end if
	 // create the user permissions array
	 if ($node_array['_user'] != '') {
		 $user_permissions = explode("\n", $node_array['_user']);
		 $node_array['_user'] = array();
		 foreach ($user_permissions as $user_permission) {
			 $user_permission = explode(',', $user_permission);
			 $node_array['_user'][trim($user_permission[0])] = trim($user_permission[1]);
		 } // end foreach
	 } else {
		 $node_array['_user'] = array();
	 } // end if
	 // get the most acceptable content type
	 $node_array['content_type'] = whomp_get_content_type(,$node_array['formats']);
	 $node_array['layout'] = $node_array['layouts'][$node_array['content_type']]['layout'];
	 $node_array['template'] = $node_array['layouts'][$node_array['content_type']]['template'];
	 $node_array['format'] = $node_array['layouts'][$node_array['content_type']]['format'];
	 return $node_array;
 } // end function
 
 /**
  * Returns the specified node class
  * 
  * Checks if the node class exists and returns a new instance of it if it does 
  * exist.
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception if the node class does not exist
  * @return class an instance of the node class
  */
 function whomp_get_node_class($options) {
	 
	 $class_string = $options['type'];
	 if (class_exists($class_string)) {
		 // if so, create the node class
		 return new $class_string();
	 } else {
		 // if not, throw exception
		 throw new Exception('The ' . $class_string . ' class does not exist.');
	 } // end if
 } // end function
 
 /**
  * Returns the template engine class
  * 
  * @author Schmalls / Joshua Thomspon <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception if the template class does not exist
  * @global class the whomp configuration options
  */
 function whomp_get_template_class() {
	 global $_whomp_configuration;
	 
	 // check if the template class exists
	 $class_string = $_whomp_configuration->template_engine;
	 if (class_exists($class_string)) {
		 // if so, return the template class
		 return new $class_string();
	 } else {
		 // if not throw an exception
		 throw new Exception('The ' . $class_string . ' template class could not be found.');
	 } // end if
 } // end function
 
 /**
  * Returns the editor class
  * 
  * @author Schmalls / Joshua Thomspon <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception if the template class does not exist
  * @global class the whomp configuration options
  */
 function whomp_get_editor_class() {
	 global $_whomp_configuration;
	 
	 // check if the template class exists
	 $class_string = $_whomp_configuration->editor_default;
	 if (class_exists($class_string)) {
		 // if so, return the template class
		 return new $class_string();
	 } else {
		 // if not, throw an exception
		 throw new Exception('The ' . $class_string . ' editor class could not be found.');
	 } // end if
 } // end function
 
 /**
  * Gets a node's XML data and XSL path and returns it in an array
  * 
  * First the node's information is retrieved from the database. Then the 
  * node's type class file is required and the class is loaded with the 
  * node's options. Then the getNodeXml and getNodeXslPath methods are 
  * called and the information is returned. The array is in the following 
  * format:
  * <pre>
  * Array (
  * 	'xml' => the xml as a string
  * 	'xsl' => the path to the xsl file
  * )
  * </pre>
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @throws Exception if the class file does not exist
  * @param array $options the node information
  * @return array the XML data and XSL path
  * @deprecated
  */
 function whomp_get_node_xml_xsl($options) {
	 
	 // get the node information
	 $options = whomp_get_node_array($options);
	 // check if the node type class file exists
	 $class_string = $options['type'];
	 if (class_exists($class_string)) {
		 // if so, create the node class
		 $node_class = new $class_string($options);
		 // get the XML data
		 $return['xml'] = $node_class->getNodeXml();
		 // get the XSL path
		 $return['xsl'] = $node_class->getNodeXslPath();
	 } else {
		 // if not, throw exception
		 throw new Exception('The ' . $class_string . ' class file does not exist.');
	 } // end if
	 // return the information
	 return $return;
 } // end function
 
 /**
  * Returns the head information as a string
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @global array the head data
  */
 function whomp_get_head_data_string() {
	 global $_whomp_head_data;
	 
	 // generate the output string
	 // title
	 $head = "\t" . $_whomp_head_data['title'];
	 // base
	 $head .= "\n\t" . $_whomp_head_data['base'];
	 // link
	 foreach ($_whomp_head_data['link'] as $link) { 
		 $head .= "\n\t" . $link;
	 } // end foreach
	 // meta
	 foreach ($_whomp_head_data['meta'] as $meta) {
		 $head .= "\n\t" . $meta;
	 } // end foreach
	 // style
	 foreach ($_whomp_head_data['style'] as $style) {
		 $head .= "\n\t" . $style;
	 } // end foreach
	 // script
	 foreach ($_whomp_head_data['script'] as $script) {
		 $head .= "\n\t" . $script;
	 } // end foreach
	 return $head;
 } // end function
 
 /* -- NODE FUNCTIONS -- */
 
 /* ++ FILE FUNCTIONS ++ */
 
 /**
  * Gets the contents of an included file
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param string $file the file to include
  * @param array $options array of options for the included file to have access
  * @return string the contents of the file after it is evaluated
  */
 function whomp_include_file_string($file, $options) {
	 
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
  * Checks to see if the exception should be logged and logs it if so. Then 
  * checks if the message should be detailed, simple, or not displayed. Then 
  * checks if the script should be exited or not and either exits with the 
  * message or prints the message.
  * 
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @param Exception $exception the exception to output information about
  * @param string $message the message to display if debugging is disabled
  * @param boolean $exit whether the script should exit or not
  * @global class access to the configuration options
  * @global string the whomp base path
  */
 function whomp_output_exception(Exception $exception, $exit = false) {
	 global $_whomp_configuration, $_whomp_base_path;
	 
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
	 // check if we should show a detailed, simple, or no message
	 if ($_whomp_configuration->debug_setting == 1) {
		 // show a simple message
		 $message = '<p>' . $exception->getMessage() . '</p>';
	 } else if ($_whomp_configuration->debug_setting == 0) {
		 // do not display the message
		 $message = '';
	 } // end if
	 // check if we should exit
	 if ($exit) {
		 // if so, exit
		 exit($message);
	 } else {
		 // if not, print message
		 echo $message; 
	 } // end if
 } // end function
 
 /* -- EXCEPTION FUNCTIONS -- */
?>