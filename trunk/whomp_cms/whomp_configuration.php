<?php
/* $Id$ */
/**
 * whomp_configuration.php
 * 
 * Main configuration file for Whomp. Includes the 
 * {@link Whomp_Configuration Whomp_Configuration} class.
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
  * The Whomp configuration class
  * 
  * Contains configuration information for each aspect of Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  */
 class Whomp_Configuration {
	 
	 /* ++ BASIC ++ */
	 
	 /**
	  * The whomp file storage directory
	  * 
	  * @var string $storage_dir
	  * @access public
	  */
	 public $storage_dir = '/whomp';
	 
	 /**
	  * Whether whomp is currently installed or not
	  * 
	  * @var boolean $installed
	  * @access public
	  */
	 public $installed = false;
	 
	 /* -- BASIC -- */
	 
	 /* ++ ERRORS ++ */
	 
	 /**
	  * Debugging output setting
	  * 
	  * 0 is no output, 1 is php default, and 2 is all ouput.
	  * 
	  * @var int $debug_setting
	  * @access public
	  */
	 public $debug_setting = 2;
	 
	 /**
	  * Whether debug output should be logged
	  * 
	  * @var boolean $debug_log
	  * @access public
	  */
	 public $debug_log = true;
	 
	 /**
	  * The debug log filename
	  * 
	  * @var string $debug_log_file
	  * @access public
	  */
	 public $debug_log_file = 'whomp_debug.log';
	 
	 /* -- ERRORS -- */
	 
	 /* ++ CACHE ++ */
	 
	 /**
	  * The cache directory
	  * 
	  * @var string $cache_dir
	  * @access public
	  */
	 public $cache_dir = '/cache';
	 
	 /**
	  * Whether caching is enabled
	  * 
	  * @var boolean $cache_enable_caching
	  * @access public
	  */
	 public $cache_enable_caching = true;
	 
	 /**
	  * The default lifetime for cache files in seconds
	  * 
	  * @var int $cache_default_lifetime
	  * @access public
	  */
	 public $cache_default_lifetime = 3600;
	 
	 /**
	  * Whether output should be compressed
	  * 
	  * @var boolean $cache_compress_output
	  * @access public
	  */
	 public $cache_compress_output = true;
	 
	 /* -- CACHE -- */
	 
	 /* ++ DATABASE ++ */
	 
	 /**
	  * The database type
	  * 
	  * @var string $database_type
	  * @access public
	  */
	 public $database_type = 'mysql';
	 
	 /**
	  * The database host
	  * 
	  * @var string $database_host
	  * @access public
	  */
	 public $database_host = 'localhost';
	 
	 /**
	  * The database username
	  * 
	  * @var string $database_username
	  * @access public
	  */
	 public $database_username = '';
	 
	 /**
	  * The database password
	  * 
	  * @var string $database_password
	  * @access public
	  */
	 public $database_password = '';
	 
	 /**
	  * The database database
	  * 
	  * @var string $database_database
	  * @access public
	  */
	 public $database_database = '';
	 
	 /**
	  * The database table prefix
	  * 
	  * @var string $database_table_prefix
	  * @access public
	  */
	 public $database_table_prefix = 'whomp_';
	 
	 /* -- DATABASE -- */
	 
	 /* ++ LANGUAGE ++ */
	 
	 /**
	  * The available site languages
	  * 
	  * It is in the following form:
	  * <pre>
	  * Array (
	  * 	language => preference (between 0.000 and 1.000 like q values)
	  * )
	  * </pre>
	  * 
	  * @var array $language_languages
	  * @access public
	  */
	 public $language_languages = array('en' => 1.000);
	 
	 /* -- LANGUAGE -- */
	 
	 /* ++ NODE ++ */
	 
	 /**
	  * The default node
	  * 
	  * @var string $node_default_node
	  * @access public
	  */
	 public $node_default_node = 'default';
	 
	 /**
	  * The error node
	  * 
	  * @var string $node_error_node
	  * @access public
	  */
	 public $node_error_node = 'error';
	 
	 /**
	  * The known content types
	  * 
	  * @var array $known_content_types
	  * @access public
	  * @todo fill this
	  */
	 public $known_content_types = array();
	 
	 /* -- NODE -- */
 } // end class
?>