<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/template.php
 * 
 * Whomp template file. Includes the 
 * {@link Whomp_Template Whomp_Template} interface.
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
  * The Whomp template class
  * 
  * Abstract class with template methods that templates can inherit, 
  * implement, and expand.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 interface Whomp_Template {
	 
	 /**
	  * Loads the node information into the template
	  * 
	  * The options array contains information about the node. It should be in 
	  * a format compatible with that returned from the whomp_get_node_array 
	  * function.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param array $options the node options
	  */
	 public function loadTemplate($options); // end function
	 
	 /**
	  * Inserts the node XML into the correct location(s)
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param DOMDocument $node_xml the node XML to be inserted
	  * @param string $layout the layout to use
	  * @param string $node_name the name of the node that needs to be inserted
	  */
	 public function insertNodeXml(DOMDocument $node_xml, $layout, $node_name = ''); // end function
	 
	 /**
	  * Inserts XSL import into the xsl file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xsl_path the path to the XSL file
	  * @param string $template the template to use
	  * @param string $format the format to use
	  */
	 public function insertNodeXsl($xsl_path, $template, $format); // end function
	 
	 /**
	  * Transforms the XML document with XSL
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  */
	 public function transform(); // end function
	 
	 /**
	  * Outputs the transformed XML file to the screen
	  * 
	  * It also sets the correct header information.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
	  */
	 public function render(); // end function
	 
 } // end class
?>