<?php
/* $Id$ */
/**
 * /whomp/templates/whomp_template_engine.php
 * 
 * Whomp template engine file. Includes the 
 * {@link Whomp_Template_Engine Whomp_Template_Engine} class.
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
  * Require the {@link /whomp/includes/whomp_template.php Whomp_Template} class file
  */
 require_once($_whomp_storage_path . '/includes/whomp_template.php');
 
 /**
  * The Whomp template engine class
  * 
  * Template engine that expands the abstact Whomp_Template class.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Template_Engine extends Whomp_Template {
	 
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
		 
		 // currently only xhtml+xml supported
		 $this->_content_type = 'application/xhtml+xml';
		 // currently only the default layout is supported
		 $this->_template_xml = new DOMDocument('1.0', $this->_charset);
		 $this->_template_xml->load($_whomp_storage_path . '/templates/whomp_template_engine/layouts/default.xml');
		 // turn short open tags off
		 print_r(ini_get('short_open_tag'));
		 // load the xsl
		 $this->_template_xsl = new DOMDocument('1.0', $this->_charset);
		 $this->_template_xsl->loadXML(whomp_include_file_string($_whomp_storage_path . '/templates/whomp_template_engine/xsl/xhtml.xsl', array('node_xsl_path' => $node_xsl_path)));
	 } // end function
	 
 } // end class
?>