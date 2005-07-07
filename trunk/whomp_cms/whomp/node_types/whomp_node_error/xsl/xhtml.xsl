<?xml version="1.0" encoding="utf-8" ?>
<!-- $Id$ -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output omit-xml-declaration="yes" />
	<xsl:template match="whomp_node_error">
		<div class="whomp_node_error">
			<h3><xsl:value-of select="title" /></h3>
			<p><xsl:value-of select="content" /></p>
		</div>
	</xsl:template>
</xsl:stylesheet>