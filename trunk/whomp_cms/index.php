<?php
/**
 * index.php
 * 
 * <p>Main index file for Whomp.</p>
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
	 error_reporting(E_STRICT);
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
 $_whomp_base_path = str_replace('/index.php', '', $_SERVER['SCRIPT_FILENAME']);
 
 /**
  * The Whomp storage directory
  * 
  * @global string $_whomp_storage_path
  */
 $_whomp_storage_path = $_whomp_base_path . $_whomp_configuration->storage_dir;
 
 /**
  * The Whomp base url
  * 
  * @global string $_whomp_base_url
  */
 $_whomp_base_url = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
 
 // Check if we need to install
 if (!$_whomp_configuration->installed) {
	 // if so, redirect to the installation file
	 header('Location: ' . $_whomp_base_url . '/' . $_whomp_configuration->storage_dir . '/includes/installation.php?base_path=' . $_whomp_base_path . '&base_url=' . $_whomp_url_path);
 } // end if
 
 /**
  * Require the {@link whomp/includes/functions.php functions} file
  */
 require_once($_whomp_storage_path . '/includes/functions.php');
 
 /**
  * Require the {@link whomp/includes/whomp_cache.php Whomp_Cache} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_cache.php');
 
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
  * @global array $_whomp_accept
  */
 $_whomp_accept_headers = whomp_get_accept_headers();
 
 /**
  * The requested page and format
  * 
  * @global array $_whomp_requested_page
  */
 $_whomp_requested_page = whomp_get_requested_page();
 
 // starts caching or outputs page and quits if it is available
 $_whomp_cache->start($_whomp_requested_node, true);
 
 /**
  * Require the {@link /whomp/includes/whomp_database.php Whomp_Database} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_database.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_language.php Whomp_Language} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_language.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_current_user.php Whomp_Current_User} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_current_user.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_xml.php Whomp_Xml} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_xml.php');
 
 /**
  * Require the {@link /whomp/includes/whomp_node.php Whomp_Node} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_node.php');
 
 // create the options array to pass to the Whomp_Database class
 $whomp_database_options = array('type' => $_whomp_configuration->database_type,
 								 'host' => $_whomp_configuration->database_host,
								 'username' => $_whomp_configuration->database_username,
								 'password' => $_whomp_configuration->database_password,
								 'database' => $_whomp_configuration->database_database,
								 'table_prefix' => $_whomp_configuration->database_table_prefix); 
 
 /**
  * Access to the {@link Whomp_Database Whomp_Database} class
  * 
  * @global $_whomp_database
  */
 $_whomp_database = new Whomp_Database($whomp_database_options);
 /*
 // create languages array
 $whomp_languages_array = explode(',', str_replace(' ', '', $_whomp_configuration->language_languages));
 // check if default (en) is in the language array
 if (!in_array('en', $whomp_languages_array)) {
	 // if not, add it
	 $whomp_languages_array[] = 'en';
 } // end if
 */
 // create the options array to pass to the Whomp_Language class
 $whomp_language_options = array('directory' => $_whomp_base_path . '/languages',
 								 'class_prefix' => 'Whomp_Language_',
								 'languages' => $whomp_configuration->language_languages);
 /**
  * Access to the {@link Whomp_Language Whomp_Language} class
  * 
  * @global $_whomp_language
  */
 $_whomp_language = new Whomp_Language($whomp_language_options);
 
 /**
  * Access to the {@link Whomp_Database Whomp_User} class
  * 
  * @global $_whomp_user
  */
 $_whomp_current_user = new Whomp_Current_User();
 
 /**
  * The requested node information from the database
  * 
  * @global $_whomp_node
  */
 $_whomp_node_array = whomp_get_node_array($_whomp_requested_page);
 
 // try to render the page
 try {
	 $whomp_unique_suffix = whomp_render_page($_whomp_node_array);
 } catch (Exception $e) {
	 whomp_output_exception($e, true);
 } // end try
 
 // ends caching
 $_whomp_cache->end($whomp_unique_suffix);
?> 