<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/cache.php
 * 
 * Whomp cache file. Includes the {@link Whomp_Cache Whomp_Cache} class.
 * 
 * @package Whomp
 * @copyright � 2005 Schmalls / Joshua Thompson / All Rights Reserved
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
  * Implements caching abilities in Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Cache {
	 
	 /**
	  * The cache directory
	  * 
	  * @var string $_cache_dir
	  * @access protected
	  */
	 protected $_cache_dir;
	 
	 /**
	  * Whether caching is enabled
	  * 
	  * @var boolean $_enable_caching
	  * @access protected
	  */
	 protected $_enable_caching;
	 
	 /**
	  * The default cache file lifetime in minutes
	  * 
	  * @var int $_lifetime
	  * @access protected
	  */
	 protected $_lifetime;
	 
	 /**
	  * Whether output should be compressed
	  * 
	  * @var boolean $_compress_ouput
	  * @access protected
	  */
	 protected $_compress_ouput;
	 
	 /**
	  * The cached file information array
	  * 
	  * This is an array of all cached files and their corresponding 
	  * information. The array is in the following form:
	  * <pre>
	  * Array (
	  * 	'en' => Array (
	  * 		'/home' => Array (
	  * 			'application/xhtml+xml' => Array (
	  * 				'filename' => the filename
	  * 				'lifetime' => lifetime in seconds
	  * 				'show_logged' => whether the file should be shown if 
	  * 								 the current user is logged in
	  * 				'charset' => 'utf-8'
	  * 				'headers' => Array (
	  * 					0 => 'Cache-Control: no-store, no-cache, must-revalidate'
	  * 				)
	  * 			)
	  * 		)
	  * 	)
	  * )
	  * </pre>
	  * 
	  * @var array $_cached_files
	  * @access protected
	  */
	 protected $_cached_files;
	 
	 /**
	  * A listing of all pages and blocks that have been started
	  * 
	  * It is an array containing an array of information about each page 
	  * or block in the following format:
	  * <pre>
	  * Array (
	  * 	0 => Array (
	  * 		'content_type' => 'application/xhtml+xml'
	  * 		'charset' => 'utf-8'
	  * 		'show_logged' => true
	  * 		'lifetime' => 3600
	  * 		'language' => 'en'
	  * 		'page' => '/home'
	  * 		'cache' => true
	  * 		'headers' => Array (
	  * 			0 => 'Cache-Control: no-store, no-cache, must-revalidate'
	  * 		)
	  * 	)
	  * )
	  * </pre>
	  * 
	  * @var array $_buffer_stack
	  * @access protected
	  */
	 protected $_buffer_stack;
	 
	 /**
	  * Whomp_Cache constructor
	  * 
	  * Initializes the cache options and also loads the cached file 
	  * information array from a stored file if it exists. This is 
	  * accomplished using unserialize. It then starts the output buffer.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the cache
	  */
	 public function __construct($options) {
		 
		 // initialize the options
		 foreach ($options as $key => $value) {
			 $this->$key = $value;
		 } // end if
		 // checked if the cached files file exists
		 if (is_file($this->_cache_dir . '/.cached_files')) {
			 // if so, get it's contents
			 $serialized = file_get_contents($this->_cache_dir . '/.cached_files');
			 // unserialize the contents, and set it to the cached files array
			 $this->_cached_files = unserialize($serialized);
		 } else {
			 // if not, set cached files to an empty array
			 $this->_cached_files = array();
		 } // end if
		 // check if compression is enable
		 if ($this->_compress_ouput) {
			 // if so, start output buffering with output compression
			 ob_start('ob_gzhandler');
		 } else {
			 // if not, start output buffering
			 ob_start();
		 } // end if
	 } // end function
	 
	 /**
	  * Whomp_Cache destructor
	  * 
	  * The destructor writes the cached file information array to a file 
	  * in serialized form.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function __destruct() {
		 
		 // serialize the cached files array
		 $serialized = serialize($this->_cached_files);
		 // write the file
		 file_put_contents($this->_cache_dir . '/.cached_files', $serialized);
	 } // end function
	 
	 /**
	  * Starts the cache
	  * 
	  * If the node is available cached, it will output the page with the 
	  * correct header information. It also makes sure that the node is 
	  * stored in the correct language and format for the user.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $requested the requested page or block
	  * @param boolean $exit whether or not we should exit if it is available
	  * @global array the accept headers
	  * @return boolean whether the node was cached or not
	  */
	 public function start($requested, $exit) {
		 global $_whomp_accept_headers;
		 
		 // check if caching is enabled
		 $return = false;
		 if ($this->_enable_caching && !array_key_exists('whomp_operation', $_REQUEST)) {
			 // if so, see if the requested language is available
			 $languages = array_keys($_whomp_accept_headers['languages']);
			 $language = $languages[0];
			 if (array_key_exists($language, $this->_cached_files)) {
				 // if so, see if the page is available
				 $page = $requested['page'];
				 if (array_key_exists($page, $this->_cached_files[$language])) {
					 // if so, see if the requested content type is available
					 $content_type = $requested['content_type'];
					 if (array_key_exists($content_type, $this->_cached_files[$language][$page])) {
						 // if so, check if the file exists
						 $filename = $this->_cache_dir . '/' . $this->_cached_files[$language][$page][$content_type]['filename'];
						 if (is_file($filename)) {
							 // if so, see if the file has expired
							 if ((time() - $this->_cached_files[$language][$page][$content_type]['lifetime']) < (filemtime($filename))) {
								 // if not, check if it should be served to a logged in user and if the user is not logged in
								 if (($this->_cached_files[$language][$page][$content_type]['show_logged']) || (!isset($_COOKIE['whomp_logout_time']))) {
									 // if so, set the status, content-type and charset
									 header('Status: 304 Not Modified');
									 header('Content-Type: ' . $content_type . '; charset=' . $this->_cached_files[$language][$page][$content_type]['charset']);
									 header('Expires: ' . gmdate('D, d M Y H:i:s', filemtime($filename) + $this->_cached_files[$language][$page][$content_type]['lifetime']) . ' GMT');
									 header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
									 header('Content-Length: ' . filesize($filename));
									 header('Cache-Control: max-age=' . $this->_cached_files[$language][$page][$content_type]['lifetime'] . ', must-revalidate');
									 // output the page
									 echo file_get_contents($filename);
									 // set return to true
									 $return = true;
									 // check if we should exit
									 if ($exit) {
										 exit();
									 } // end if
								 } // end if
							 } else {
								 // if so, delete the file
								 unlink($filename);
								 // unset this in the cached file array
								 unset($this->_cached_files[$language][$page][$content_type]);
							 } // end if
						 } else {
							 // if not, unset this in the cached file array
							 unset($this->_cached_files[$language][$page][$content_type]);
						 } // end if
					 } // end if
				 } // end if
			 } // end if
		 } // end if
		 // start the output buffer
		 ob_start();
		 // return return
		 return $return;
	 } // end function
	 
	 /**
	  * Turns off caching for the rest of the script
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  */
	 public function noCache() {
		 
		 $this->_enable_caching = false;
	 } // end function
	 
	 /**
	  * Stops the cache
	  * 
	  * If caching is enabled, it will cache the page or block and write 
	  * it to a file. Then it will stop output buffering and compress the 
	  * page if it is configured to do so. It also updates the cached files 
	  * array with the new information.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for the page or block
	  */
	 public function end($options = array()) {
		 
		 // check if caching is enabled
		 if ($this->_enable_caching && $options['cache']) {
			 // if so, create the filename
			 $filename = md5($options['page'] . '_' .
			 				 $options['language'] . '_' . 
							 $options['content_type'] . '_' . 
							 $options['charset']) . '.cache';
			 // get the file contents
			 $contents = ob_get_contents();
			 // write the file and see if it was successful
			 if (file_put_contents($this->_cache_dir . '/' . $filename, $contents) !== false) {
				 // if so, update the cached files array
				 $this->_cached_files[$options['language']]
				 					 [$options['page']]
									 [$options['content_type']] = array('filename' => $filename,
				 														'lifetime' => $options['lifetime'],
																		'show_logged' => $options['show_logged'],
																		'charset' => $options['charset'],
																		'headers' => $options['headers']);
			 } // end if
		 } // end if
		 // stop the output buffer
		 ob_end_flush();
	 } // end function
 } // end class
?>