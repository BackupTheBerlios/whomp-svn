<?xml version="1.0" encoding="utf-8" ?>
<!-- $Id$ http://localhost/whomp/whomp/node_types/whomp_node_frontpage/xsl/xhtml.xsl-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output omit-xml-declaration="no" method="xml" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" indent="yes" encoding="utf-8"/>
	<xsl:template match="/layout">
		<html>
			<head>
				<xsl:copy-of select="$whomp_head" />
				<link rel="stylesheet" href="{$_whomp_storage_url}/templates/default/files/blueheaven.css" type="text/css" />
			</head>
			<body onload="{$whomp_onload}">
				<h1 class="header" style="height:76px;">LL Schmalls</h1>
				<div style="float:left;">
					<xsl:apply-templates select="header" />
					<xsl:apply-templates select="content" />
					<xsl:apply-templates select="footer" />
				</div>
				<div id="navcontainer">
					<b>navigation</b>
					<br/><br/>
					<ul id="navlinks">
						<li><strong>home</strong></li>
						<li><a href="http://whomp.berlios.de">whomp cms</a></li>
						<li><a href="http://mamboforge.net/projects/mamml/">mamml</a></li>
						<li><a href="aboutme.html">about me</a></li>
					</ul>     
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template match="header">
		<h2><xsl:value-of select="title" /></h2>
		<h3><xsl:value-of select="description" /></h3>
	</xsl:template>
	<xsl:template match="content">
		<xsl:if test="$whomp_edit">
			<div id="{$whomp_editid}">
				<xsl:apply-templates />
			</div>
		</xsl:if>
		<xsl:if test="not($whomp_edit)">
			<xsl:apply-templates />
		</xsl:if>
	</xsl:template>
	<xsl:template match="footer">
		<p id="copyright"><xsl:value-of select="copyright" /></p>
	</xsl:template>
	<xsl:template match="whomp_node_frontpage">
		<div class="whomp_node_frontpage">
			<h4><xsl:value-of select="title" />: Overridden.</h4>
			<p><xsl:value-of select="content" /></p>
		</div>
	</xsl:template>
</xsl:stylesheet>