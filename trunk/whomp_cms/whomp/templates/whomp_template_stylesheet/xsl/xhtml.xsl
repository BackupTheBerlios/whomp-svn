<?xml version="1.0" encoding="utf-8" ?>
<!-- $Id$ -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output version="1.0" omit-xml-declaration="no" method="xml" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" indent="yes" encoding="utf-8" media-type="application/xhtml+xml" />
	<xsl:template match="/layout">
		<html>
			<head>
				<title>Whomp CMS</title>
			</head>
			<body>
				<div id="header" class="header">
					<xsl:apply-templates select="header" />
				</div>
				<div id="content" class="content">
					<xsl:apply-templates select="content" />
				</div>
				<div id="footer" class="footer">
					<xsl:apply-templates select="footer" />
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template match="header">
		<h1><xsl:value-of select="title" /></h1>
		<h2><xsl:value-of select="description" /></h2>
	</xsl:template>
	<xsl:template match="content">
		<xsl:apply-templates />
	</xsl:template>
	<xsl:template match="footer">
		<p><xsl:value-of select="copyright" /></p>
	</xsl:template>
	<xsl:template match="whomp_node_frontpage">
		<div class="whomp_node_frontpage">
			<h3><xsl:value-of select="title" />: Overridden.</h3>
			<p><xsl:value-of select="content" /></p>
		</div>
	</xsl:template>
</xsl:stylesheet>