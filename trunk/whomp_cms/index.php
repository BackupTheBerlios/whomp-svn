<?php
/* $Id$ */
/**
 * index.php
 * 
 * Main index file for Whomp.
 * 
 * @package Whomp
 * @copyright � 2005 Schmalls / Joshua Thompson / All Rights Reserved
 * @license http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
 * @version 0.0.0
 * @since 0.0.0
 * @link http://www.schmalls.com
 * @todo fix the way that formats, templates, and layout information is passed from node to template
 */

 /**
  * Make sure all subsequent files are only included by this file
  */
 define('_VALID_WHOMP', true);
 
 /**
  * The script start time
  * 
  * @global float $_whomp_start_time
  */
 $_whomp_start_time = microtime(true);
 
 /**
  * Require the {@link whomp_configuration.php Whomp_Configuration} class file
  */
 require_once('whomp_configuration.php');
 
 /**
  * Access to the {@link Whomp_Configuration Whomp_Configuration} class
  * 
  * @global class $_whomp_configuration
  */
 $_whomp_configuration = new Whomp_Configuration();
 
 // check if debugging is enabled
 if ($_whomp_configuration->debug_setting == 2) {
	 error_reporting(E_ALL);
 } else if ($_whomp_configuration->debug_setting == 1) {
	 error_reporting(E_ALL ^ E_NOTICE);
 } else {
	 error_reporting(0);
 } // end if
 
 /**
  * The Whomp base directory
  * 
  * @global string $_whomp_base_path
  */
 $_whomp_base_path = $_whomp_configuration->site_path;
 //$_whomp_base_path = preg_replace('/\/$/', '', str_replace('/index.php', '', $_SERVER['SCRIPT_FILENAME']));
 
 /**
  * The Whomp storage directory
  * 
  * @global string $_whomp_storage_path
  */
 $_whomp_storage_path = $_whomp_configuration->site_path . $_whomp_configuration->storage_dir;
 //$_whomp_storage_path = $_whomp_base_path . $_whomp_configuration->storage_dir;
 
 /**
  * The Whomp base url
  * 
  * @global string $_whomp_base_url
  */
 $_whomp_base_url = $_whomp_configuration->site_url;
 //$_whomp_base_url = preg_replace('/\/$/', '', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));
 
 /**
  * The Whomp storage url
  * 
  * @global string $_whomp_storage_url
  */
 $_whomp_storage_url = $_whomp_configuration->site_url . $_whomp_configuration->storage_dir;
 //$_whomp_storage_url = $_whomp_base_url . $_whomp_configuration->storage_dir;
 
 // Check if we need to install
 if (!$_whomp_configuration->installed) {
	 // if so, redirect to the installation file
	 header('Location: ' . preg_replace('/\/$/', '', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'])) . $_whomp_configuration->storage_dir . '/installation/index.php?base_path=' . dirname(__FILE__) . '&base_url=' . preg_replace('/\/$/', '', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'])) . '&storage_path=' . dirname(__FILE__) . $_whomp_configuration->storage_dir . '&storage_url=' . preg_replace('/\/$/', '', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'])) . $_whomp_configuration->storage_dir);
 } // end if
 
 /**
  * Require the {@link whomp/includes/functions.php functions} file
  */
 require_once($_whomp_storage_path . '/includes/functions.php');
 
 // create the options array to pass to the Whomp_Cache class
 $whomp_cache_options = array('_cache_dir' => $_whomp_storage_path . $_whomp_configuration->cache_dir,
 							  '_enable_caching' => (boolean)$_whomp_configuration->cache_enable_caching,
							  '_lifetime' => (integer)$_whomp_configuration->cache_default_lifetime,
							  '_compress_output' => (boolean)$_whomp_configuration->cache_compress_output);
							  
 /**
  * Access to the {@link Whomp_Cache Whomp_Cache} class
  * 
  * @global class $_whomp_cache
  */
 $_whomp_cache = new Whomp_Cache($whomp_cache_options);
 
 /**
  * The accept header information
  * 
  * @global array $_whomp_accept_headers
  */
 $_whomp_accept_headers = whomp_get_accept_headers();
 
 /**
  * The requested page and format
  * 
  * @global array $_whomp_requested_page
  */
 $_whomp_requested_page = whomp_get_requested_page();
 
 // starts caching or outputs page and quits if it is available
 $_whomp_cache->start($_whomp_requested_page, true);
 
 // try to get the user's preferred language
 try {
	 $language_included = false;
	 foreach ($_whomp_accept_headers['languages'] as $language => $qvalue) {
		 if ($language == '*') {
			 $language = 'en';
		 } // end if
		 // check if the class exists
		 if (class_exists('Whomp_Language_' . $language)) {
			 // create the language class string
			 $whomp_language_class_string = 'Whomp_Language_' . $language;
			 /**
			  * Access to the {@link Whomp_Language Whomp_Language's} child class
			  * 
			  * @global class $_whomp_language
			  */
			 $_whomp_language = new $whomp_language_class_string();
			 // set language included to true and break
			 $language_included = true;
			 break;
		 } // end if
	 } // end foreach
	 if ($language_included === false) {
		 throw new Exception('No acceptable language found.');
	 } // end if
 } catch (Exception $e) {
	 whomp_output_exception($e, true);
 } // end try
 
 // create the options array to pass to the Whomp_Database class
 $whomp_database_options = array('type' => $_whomp_configuration->database_type,
 								 'host' => $_whomp_configuration->database_host,
								 'username' => $_whomp_configuration->database_username,
								 'password' => $_whomp_configuration->database_password,
								 'database' => $_whomp_configuration->database_database,
								 'table_prefix' => $_whomp_configuration->database_table_prefix,
								 'debug' => false); 
 
 /**
  * Access to the {@link Whomp_Database Whomp_Database} class
  * 
  * @global class $_whomp_database
  */
 $_whomp_database = new Whomp_Database($whomp_database_options);
 
 /**
  * Access to the {@link Whomp_User_Current Whomp_User_Current} class
  * 
  * @global class $_whomp_current_user
  */
 $_whomp_current_user = new Whomp_User_Current();
 
 /**
  * Information to be included in the head of a document
  * 
  * @global array $_whomp_head_data
  */
 $_whomp_head_data = array('base' => '<base href="' . $_whomp_base_url . '" />',
 						   'link' => array(),
						   'meta' => array('generator' => '<meta name="generator" content="' . $_whomp_configuration->version_information . '" />'),
						   'script' => array(),
						   'style' => array(),
						   'title' => '<title>Whomp CMS!</title>');
						   
 /**
  * The requested node information from the database
  * 
  * @global array $_whomp_node_array
  */
 $_whomp_node_array = whomp_get_node_array($_whomp_requested_page);
 
 // check if the node class exists
 try {
	 /**
	  * The whomp node class
	  * 
	  * @global class $_whomp_node_class
	  */
	 $_whomp_node_class = whomp_get_node_class($_whomp_node_array);
	 // load the node information into the node class
	 $_whomp_node_class->loadNode($_whomp_node_array);
 } catch (Exception $e) {
	 whomp_output_exception($e, true);
 } // end try
 
 // check if the template class exists
 try {
	 /**
	  * The whomp template class
	  * 
	  * @global class $_whomp_template_class
	  */
	 $_whomp_template_class = whomp_get_template_class();
	 // load the node information into the template class
	 $_whomp_template_class->loadTemplate($_whomp_node_array);
 } catch (Exception $e) {
	 whomp_output_exception($e);
 } // end try
 
 // check if an operation is requested
 $whomp_operation = whomp_get_param('whomp_operation', null);
 if ($whomp_operation !== null) {
	 // if so, check if the whomp editor class exists
	 try {
		 /**
		  * The whomp editor class
		  * 
		  * @global class $_whomp_editor_class
		  */
		 $_whomp_editor_class = whomp_get_editor_class();
		 // load the node information into the editor class
		 $_whomp_editor_class->loadEditor($_whomp_node_array);
	 } catch (Exception $e) {
		 whomp_output_exception($e);
	 } // end try
	 // switch by operation
	 switch ($whomp_operation) {
		 case 'xml' :
		 	 // check if we are supposed to save
		 	 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				 // if so, save
				 $_whomp_node_class->save();
		 	 } else {
				 // if not, print the node xml
				 $_whomp_node_class->printXml();
		 	 } // end if
			 break;
		 case 'xsl' :
		 	 // print the node xsl
		 	 $_whomp_node_class->printXsl();
			 break;
		 case 'schema' :
		 	 // print the node schema
		 	 $_whomp_node_class->printSchema();
			 break;
		 case 'config' :
		 	 // print the editor config file
		 	 $_whomp_node_class->printConfig();
			 break;
		 case 'preview' :
		 	 // render the preview
		 	 $_whomp_node_class->makePreviewable();
		 	 $_whomp_node_class->render();
			 break;
		 case 'edit' :
		 	 // render the editable version of the node
		 	 $_whomp_node_class->makeEditable();
			 $_whomp_node_class->renderNode();
			 break;
		 case 'save' :
		 	 // save the edited node
		 	 $_whomp_node_class->save();
			 break;
	 } // end switch
 } else {
 	 // if not, render the page with the template and everything
 	 $whomp_end_cache_options = $_whomp_node_class->renderNode();
	 $whomp_end_cache_options['cache'] = true;
	 $whomp_end_cache_options['lifetime'] = 3600;
	 $whomp_end_cache_options['show_logged'] = true;
	 $whomp_end_cache_options['headers'] = '';
	 // end caching
	 $_whomp_cache->end($whomp_end_cache_options);
 } // end if
?> 