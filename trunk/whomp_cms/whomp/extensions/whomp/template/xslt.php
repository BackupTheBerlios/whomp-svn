<?php
/* $Id: whomp_template_engine.php 53 2005-07-12 18:23:54Z schmalls $ */
/**
 * /whomp/extensions/whomp/template/xslt.php
 * 
 * Whomp template engine file. Includes the 
 * {@link Whomp_Template_Xslt Whomp_Template_Xslt} class.
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
  * The Whomp template xslt class
  * 
  * Template engine that expands the abstact Whomp_Template class with php xslt.
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 class Whomp_Template_Xslt extends Whomp_Template {
	 
	 /**
	  * Whomp_Template_Engine constructor
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param array $layout the layout information
	  * @param string $content_type the output content type
	  * @param array $node_formats the formats that the node supports
	  * @param string $node_xsl_path the path to the node xsl file
	  * @global string the whomp storage path
	  * @global string the whomp storage url
	  * @todo implement other output formats
	  */
	 public function __construct($layout, $content_type, $node_formats, $node_xsl_path) {
		 global $_whomp_storage_path, $_whomp_storage_url;
		 
		 // set the content type
		 if ($content_type == '*/*') {
			 $content_type = 'text/html';
		 } // end if
		 $this->_content_type = $content_type;
		 // currently only the default layout is supported
		 $this->_template_xml = new DOMDocument('1.0', $this->_charset);
		 $this->_template_xml->load($_whomp_storage_path . '/layouts/' . $layout['layout'] . '.xml');
		 // load the xsl
		 $this->_template_xsl = new DOMDocument('1.0', $this->_charset);
		 $xsl = <<<XSL
<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="{$node_xsl_path}" />
	<xsl:import href="{$_whomp_storage_url}/templates/{$layout['template']}/{$layout['format']}.xsl" />
	<xsl:variable name="_whomp_storage_url">{$_whomp_storage_url}</xsl:variable>
</xsl:stylesheet>
XSL;
		 $this->_template_xsl->loadXML($xsl);
	 } // end function
	 
 } // end class
?>