<?php
/* $Id: whomp_template_stylesheet.php 47 2005-07-12 05:22:02Z schmalls $ */
/**
 * /whomp/extensions/whomp/template/stylesheet.php
 * 
 * Whomp template engine file. Includes the 
 * {@link Whomp_Template_Stylesheet Whomp_Template_Stylesheet} class.
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
  * The Whomp template stylesheet class
  * 
  * Template engine that expands the abstact Whomp_Template class. Instead of using the 
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Template_Stylesheet extends Whomp_Template {
	 
	 /**
	  * The path to the nodes xsl stylesheet
	  * 
	  * @var string $_node_xsl_path
	  * @access protected
	  */
	 protected $_node_xsl_path;
	 
	 /**
	  * Whomp_Template_Engine constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param string $layout the layout to use
	  * @param string $content_type the output content type
	  * @param array $node_formats the formats that the node supports
	  * @param string $node_xsl_path the path to the node xsl file
	  * @todo implement other output formats
	  */
	 public function __construct($layout, $content_type, $node_formats, $node_xsl_path) {
		 global $_whomp_storage_path;
		 
		 // all output is xml
		 $this->_content_type = 'application/xml';
		 // currently only the default layout is supported
		 $this->_template_xml = new DOMDocument('1.0', $this->_charset);
		 $this->_template_xml->load($_whomp_storage_path . '/layouts/' . $layout . '.xml');
		 // set the node xsl path
		 $this->_node_xsl_path = $node_xsl_path;
	 } // end function
	 
	 /**
	  * Transforms the XML document with XSL
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  */
	 public function transform($xsl_path) {
		 global $_whomp_storage_url;
		 
		 // append the template xsl stylesheet	 
		 $this->_template_xml->appendChild($this->_template_xml->createProcessingInstruction('xml-stylesheet', 'href="' . $_whomp_storage_url . '/templates/whomp_template_stylesheet/xsl/xhtml.xsl" type="text/xsl"'));
		 // append the node xsl stylesheet
		 $this->_template_xml->appendChild($this->_template_xml->createProcessingInstruction('xml-stylesheet', 'href="' . $this->_node_xsl_path . '" type="text/xsl"'));
		 // create the transformed xml
		 $this->_template_transformed = $this->_template_xml->saveXML();
	 } // end function
	 
 } // end class
?>