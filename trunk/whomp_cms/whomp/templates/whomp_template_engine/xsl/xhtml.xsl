<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output omit-xml-declaration="no" method="xml" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" indent="no" encoding="utf-8"/>
	<xsl:template match="/layout">
		<html>
			<head>
				<title>Whomp CMS</title>
			</head>
			<body>
				<div name="header" class="header">
					<xsl:apply-templates select="header" />
				</div>
				<div name="content" class="content">
					<xsl:apply-templates select="content" />
				</div>
				<div name="footer" class="footer">
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
</xsl:stylesheet>
