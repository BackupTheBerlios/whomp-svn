<?php
/* $Id$ */
/**
 * /whomp/includes/whomp_ajax.php
 * 
 * Whomp ajax file. Includes the {@link Whomp_Ajax Whomp_Ajax} class.
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
  * The Whomp Ajax class
  * 
  * Implements {@link http://en.wikipedia.org/wiki/AJAX AJAX} abilities in Whomp.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  * @todo get this working
  */
 class Whomp_Ajax {
	 
	 /**
	  * The callback url
	  * 
	  * @var string $_callback
	  * @access protected
	  */
	 protected $_callback;
	 
	 /**
	  * The method type ('get' or 'post' currently)
	  * 
	  * @var string $_method
	  * @access protected
	  */
	 protected $_method = 'post';
	 
	 /**
	  * The return type to expect from the request
	  * 
	  * @var string $_return_type
	  * @access protected
	  */
	 protected $_return_type = 'text';
	 
	 /**
	  * Whether async should be used or not
	  * 
	  * @var strin $_async
	  * @access protected
	  */
	 protected $_async = 'true';
	 
	 /**
	  * Classes and methods that have been registered
	  * 
	  * @var array $_classes
	  * @access protected
	  */
	 protected $_classes = array();
	 
	 /**
	  * Functions that have been registered
	  * 
	  * @var array $_functions
	  * @access protected
	  */
	 protected $_functions = array();
	 
	 /**
	  * Static variable to keep track of whether the base functions have 
	  * been included or not
	  * 
	  * @var boolean $_included
	  * @access protected
	  */
	 protected static $_included = false;
	 
	 /**
	  * Whomp_Ajax constructor
	  * 
	  * Initializes the Ajax options. The options array should be formatted 
	  * in the following way:
	  * <pre>
	  * Array (
	  * 	'_callback' => the url to the request page (usually the current page url)
	  * 	'_method' => 'GET' or 'POST'
	  * 	'_return_type' => 'text' or 'xml'
	  * 	'_async' => either 'true' or 'false' (as a string)
	  * )
	  * </pre>
	  * '_callback' is the only required option because the others have defaults.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options options for Ajax
	  */
	 public function __construct($options = array()) {
		 
		 // initialize the options
		 foreach ($options as $key => $value) {
			 $this->$key = $value;
		 } // end foreach
	 } // end function
	 
	 /**
	  * Registers a function for callback
	  * 
	  * The function can be sent as a simple string for callback, or it can 
	  * be sent as an array containing the class name as the first element 
	  * and the method as the second element. The function needs to be 
	  * static to function correctly.
	  * 
	  * Examples of calling functions in javascript:
	  * 
	  * <samp>
	  * function do_something() {
	  * 	my_var = document.getElementById('my_var').value;
	  * 	My_Class_myMethod(my_var);
	  * }
	  * 
	  * // callback function
	  * function My_Class_myMethod_callback(result) {
	  * 	document.getElementById('example').innerHtml = result;
	  * }	
	  * </samp>
	  * In the previous example a function has been defined that should be 
	  * called on an event (onclick, etc.). The function gets some 
	  * information and stores it in 'my_var' to be sent to a php function. 
	  * In this case we are calling a class and method: 'My_Class' and 
	  * 'myMethod' respectively. They are a php class/method and javascript 
	  * function has been created automatically to call it. The function is 
	  * named with the class name followed by an underscore and then the 
	  * method name. The only difference from using this function from within
	  * php is that the result is not returned when it is called. Instead the
	  * result is sent to a callback function name 
	  * 'My_Class_myMethod_callback' which is simply the same name as the 
	  * function we called but with the '_callback' appended to the name. 
	  * This callback function should be implemented in javascript and 
	  * should take the result as its only argument. The result will either 
	  * be the response text or the dom xml object.
	  * 
	  * <samp>
	  * function do_something_else() {
	  * 	my_var1 = document.getElementById('my_var1').value;
	  * 	my_var2 = document.getElementById('my_var2').value;
	  * 	result = my_function(my_var1, my_var2);
	  * }
	  * 
	  * // callback function 
	  * my_function_callback(result) {
	  * 	document.getElementById('example').innerHtml = result;
	  * }
	  * </samp>
	  * The previous example shows an event function that uses a function 
	  * instead of a class/method combination.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param mixed $function either a string as the function name or an array as the classname function combination
	  */
	 public function registerFunction($function) {
		 
		 // check if this is an array
		 if (is_array($function)) {
			 // if so, add the class name and method name
			 $this->_classes[$function[0]][] = $function[1];
		 } else {
			 // if not, add the function
			 $this->_functions[] = $function;
		 } // end if
	 } // end function
	 
	 /**
	  * Inserts the javascript into the document
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access protected
	  */
	 protected function insertJavascript() {
		 
		 // get the javascript
		 ob_start();
?>
<script language="javascript"  type="text/javascript">
	var whomp_ajax_url = "<?php echo $this->_callback; ?>";
	var whomp_ajax_callback;
	var whomp_ajax_working = false;
	var whomp_ajax_http;
	// gets the http object
	function whomp_ajax_get_http_object() {
		var xmlhttp;
		/*@cc_on
			@if (@_jscript_version >= 5)
				try {
					xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try {
						xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					} catch (E) {
						xmlhttp = false;
					}
				}
			@else
				xmlhttp = false;
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
			try {
				xmlhttp = new XMLHttpRequest();
				<?php if ($this->_return_type == 'xml') { echo 'xmlhttp.overrideMimeType("text/xml");'; } ?>
			} catch (e) {
				xmlhttp = false;
			}
		}
		return xmlhttp;
	}

	function whomp_ajax_call_function(function_string, args, callback) {
		var post_data;
		var url = whomp_ajax_url;

		whomp_ajax_callback = callback;
<?php
		 // check if this is a get request
		 if ($this->_method == 'GET') {
			 // if so, output get javascript
?>
		if (url.indexOf("?") == -1) {
			url = url + "?" + function_string;
		} else {
			url = url + "&" + function_string;
		}
		for (i = 0; i < args.length; i++) {
			url = url + "&args[]=" + escape(args[i]);
		}
		post_data = null;
<?php
		 } else {
			 // if not, output post javascript
?>
		post_data = function_string;
		for (i = 0; i < args.length; i++) { 
			post_data = post_data + "&args[]=" + escape(args[i]);
		}
<?php
		 } // end if
?>
		whomp_ajax_http = whomp_ajax_get_http_object();
		whomp_ajax_http.open("<?php echo $this->_method; ?>", url, <?php echo $this->_async; ?>);
<?php
		 // check if this is a post request
		 if ($this->_method == 'POST') {
			 // if so, output post javascript
?>
		whomp_ajax_http.setRequestHeader("Method", "POST" + url + " HTTP/1.1");
		whomp_ajax_http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
<?php
		 } // end if
?>
		whomp_ajax_http.onreadystatechange = whomp_ajax_handle_response;
		whomp_ajax_http.send(post_data);
	}
	
	function whomp_ajax_handle_response() {
		var whomp_ajax_result;

		if (whomp_ajax_http.readyState == 4) {
			if (whomp_ajax_http.responseText.indexOf('invalid') == -1) {
<?php
		 // check if this is text or xml
		 if ($this->_return_type == 'xml') {
			 // if xml, return dom result
?>
				whomp_ajax_result = whomp_ajax_http.responseXML;
<?php
		 } else if ($this->_return_type == 'text') {
			 // if text, return response text
?>
				whomp_ajax_result = whomp_ajax_http.responseText;
<?php
		 } else {
			 // if neither, throw an exception
			 throw new Exception('Unknown return type: ' . $this->_return_type);
		 } // end if
?>
				whomp_ajax_callback(whomp_ajax_result);
			}
		}
	}
<?php
		 // insert the class/method functions
		 foreach ($this->_classes as $class => $value) {
			 foreach ($value as $method) {
?>
	function <?php echo $class . '_' . $method; ?>() {
		whomp_ajax_call_function("function[]=<?php echo $class; ?>&function[]=<?php echo $method; ?>", <?php echo $class . '_' . $method; ?>.arguments, <?php echo $class . '_' . $method; ?>_callback);
	}
<?php
			 } // end foreach
		 } // end foreach
		 // insert the function function
		 foreach ($this->_functions as $function) {
?>
	function <?php echo $function; ?>() {
		whomp_ajax_call_function("function=<?php echo $function; ?>", <?php echo $function; ?>.arguments);
	}
<?php
		 } // end foreach
?>
	</script>
<?php
		 return ob_get_clean();
	 } // end function
	 
	 /**
	  * Checks the request to see if we should call a function and exit
	  * 
	  * This method should be called after the object has been initialized 
	  * to minimize unneeded code before this function. However, make sure 
	  * that all files which contain functions or classes/methods that are 
	  * going to be registered have been included.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception if there is an unknown request method or return type
	  */
	 public function checkRequest() {
		 
		 // make sure we don't print unwanted information
		 while (@ob_end_clean());
		 // check if this is a post or get request
		 if ($this->_method == 'POST') {
			 // if post, get the post superglobal
			 $request = $_POST;
		 } else if ($this->_method == 'GET') {
			 // if get, get the get superglobal
			 $request = $_GET;
		 } else {
			 // if neither, throw an exception
			 throw new Exception('Unknown request method: ' . $this->_method);
		 } // end if
		 // check if the function or class/method was provided
		 if (array_key_exists('function', $request)) {
			 // if so, check if this is a get request
			 if ($this->_method = 'GET') {
				 // if so, fix get caching problems
				 header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');    // Date in the past
				 header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . " GMT");
				 header ('Cache-Control: no-cache, must-revalidate');  // HTTP/1.1
				 header ('Pragma: no-cache');                          // HTTP/1.0
			 } // end if
			 // check if arguments were supplied
			 if (empty($request['args'])) {
				 // if not, create empty array
				 $request['args'] = array();
			 } // end if
			 // check if this is xml or text
			 if ($this->_return_type == 'xml') {
				 // if xml, set header to xml
				 header('Content-Type: text/xml');
			 } else if ($this->_return_type == 'text') {
				 // if text, set header to text
				 header('Content-Type: text/plain');
			 } else {
				 // if neither, throw exception
				 throw new Exception('Unknown return type: ' . $this->_return_type);
			 } // end if
			 call_user_func_array($request['function'], $request['args']);
			 // exit
			 exit;		 
		 } else {
			 // if not, return
			 return;
		 } // end if
	 } // end function
	 
	 /**
	  * Initializes the necessary information for this instance
	  * 
	  * It adds the required javascript to the document head
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @global array access to the head data
	  */
	 public function initialize() {
		 global $_whomp_head_data;
		 
		 // add the javascript
		 $_whomp_head_data['script'][] = $this->insertJavascript();
	 } // end function
	 
 } // end class
?>