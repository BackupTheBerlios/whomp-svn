<?php
/**
 * /whomp/includes/whomp_template.php
 * 
 * <p>Whomp template file. Includes the 
 * {@link Whomp_Template Whomp_Template} class.</p>
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
  * <p>Abstract class with template methods that templates can inherit, implement, and expand.</p>
  * 
  * @package Whomp
  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
  * @version 0.0.0
  * @since 0.0.0
  * @access public
  */
 public abstract class Whomp_Template {
	 
	 /**
	  * The template XML DOMDocument
	  * 
	  * @var DOMDocument $_template_xml
	  * @access protected
	  */
	 protected $_template_xml;
	 
	 /**
	  * The template XSL DOMDocument
	  * 
	  * @var DOMDocument $_template_xsl
	  * @access protected
	  */
	 protected $_template_xsl;
	 
	 /**
	  * The transformed document
	  * 
	  * @var string $_template_transformed
	  * @access protected
	  */
	 protected $_template_transformed;
	 
	 /**
	  * The acceptable output formats
	  * 
	  * <p>This should be an array with formats as keys and content-types as 
	  * values. For example:
	  * <ul>Array (
	  * 	<li>'html' => 'text/html'</li>
	  * 	<li>'xhtml+xml' => 'application/xhtml+xml'</li>
	  * 	<li>'xhtml' => 'application/xhtml+xml'</li>
	  * )</ul>
	  * </p>
	  * 
	  * @var array $_formats
	  * @access protected
	  */
	 protected $_formats;
	 
	 /**
	  * The document's content type
	  * 
	  * @var string $_content_type
	  * @access protected
	  */
	 protected $_content_type;
	 
	 /**
	  * The document's charset
	  * 
	  * @var string $_charset
	  * @access protected
	  */
	 protected $_charset = 'utf-8';
	 
	 /**
	  * Abstract Whomp_Template constructor
	  * 
	  * <p>The constructor should take the $layout and $format as parameters 
	  * and then initialize the $_template_xml and $_template_xsl variables.
	  * It should throw an exception if the layout or ouput format does not 
	  * exist. However, if the output format is an empty string, it should 
	  * use the user's {@link $_whomp_accept_headers accept headers} to 
	  * determine the most appropriate output format. This can be made 
	  * easier using the 
	  * {@link whomp_get_content_type whomp_get_content_type} function.</p>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @throws Exception
	  * @param string $layout the layout to use
	  * @param string $format the output format
	  * @param string $node_formats the formats that the node supports
	  */
	 abstract public function __construct($layout, $format, $node_formats);
	 
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
	 public function insertNodeXml(DOMDocument $node_xml, $node_name = '') {
		 
		 // find the specified node
		 $xpath = new DOMXpath($this->_template_xml);
		 $node_list = $xpath('//node[@name = \'' . $node_name . '\']');
		 // check if the node was found
		 if (count($node_list) != 0) {
			 // if so, append the node XML
			 foreach ($node_list as $node) {
				 $node->appendChild($node_xml->documentElement);
			 } // end foreach
		 } else {
			 // if not, throw exception
			 throw new Exception('The node was not found in the XML document.');
		 } // end if
	 } // end function
	 
	 /**
	  * Inserts XSL import into the xsl file
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @param string $xsl_path the path to the XSL file
	  */
	 public function insertXslImport($xsl_path) {
		 
		 // create the XML element to append
		 $import = $this->_template_xsl->createElement('xsl:import');
		 $import->setAttribute('href', $xsl_path);
		 // append the element
		 $this->_template_xsl->documentElement->appendChild($import);
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
	 public function transform() {
		 
		 // create the XSLT processor and import the stylesheet
		 $processor = new XSLTProcessor();
		 $processor->importStyleSheet($this->_template_xsl);
		 // transform the XML file
		 $this->_template_transformed = $processor->transformToXML($this->_template_xml);
		 // check if the transformation went alright
		 if ($this->_template_transformed === false) {
			 // if not, throw exception
			 throw new Exception('Error transforming XML document with XSL.');
		 } // end if
	 } // end function
	 
	 /**
	  * Outputs the transformed XML file to the screen
	  * 
	  * <p>It also sets the correct header information.</p>
	  * 
	  * @author Schmalls / Joshua Thompson <schmalls@gmail.com>
	  * @version 0.0.0
	  * @since 0.0.0
	  * @access public
	  * @return array information about the page suitable for sending to Whomp_Cache::end()
	  */
	 public function render() {
		 
		 // set the content type and charset
		 header('Content-Type: ' . $this->_content_type . '; charset=' . $this->_charset);
		 // display the document
		 echo $this->_template_transformed;
		 // return unique identifier
		 return array('content_type' => $this->_content_type,
		 			  'charset' => $this->_charset);
	 } // end function
	 
 } // end class
?>