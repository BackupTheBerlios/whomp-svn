<?php
/* $Id$ */
/**
 * /whomp/extensions/whomp/template.php
 * 
 * Whomp template file. Includes the 
 * {@link Whomp_Template Whomp_Template} class.
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
	  * Abstract Whomp_Template constructor
	  * 
	  * The constructor should take the $layout and $format as parameters 
	  * and then initialize the $_template_xml and $_template_xsl variables.
	  * It should throw an exception if the layout or ouput format does not 
	  * exist. However, if the output format is an empty string, it should 
	  * use the user's {@link $_whomp_accept_headers accept headers} to 
	  * determine the most appropriate output format. This can be made 
	  * easier using the 
	  * {@link whomp_get_content_type whomp_get_content_type} function.
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param string $layout the layout to use
	  * @param string $content_type the output content type
	  * @param array $node_formats the formats that the node supports
	  */
	 public function __construct($layout, $content_type, $node_formats);
	 
	 /**
	  * Inserts the node XML into the correct location(s)
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param DOMDocument $node_xml the node XML to be inserted
	  * @param string $node_name the name of the node that needs to be inserted
	  */
	 public function insertNodeXml(DOMDocument $node_xml, $node_name = ''); // end function
	 
	 /**
	  * Inserts XSL import into the xsl file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xsl_path the path to the XSL file
	  * @deprecated
	  */
	 public function insertXslImport($xsl_path); // end function
	 
	 /**
	  * Transforms the XML document with XSL
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  */
	 public function transform($xsl_path); // end function
	 
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