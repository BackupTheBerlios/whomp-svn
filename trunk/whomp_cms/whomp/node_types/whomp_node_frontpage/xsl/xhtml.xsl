<?xml version="1.0" encoding="utf-8" ?>
<!-- $Id: xhtml.xsl 16 2005-06-23 16:04:44Z schmalls $ -->
<xsl:stylesheet>
	<xsl:output omit-xml-declaration="yes" />
	<xsl:template match="whomp_test_node">
		<div class="whomp_test_node">
			<h3><xsl:value-of select="title" /></h3>
			<p><xsl:value-of select="content" /></p>
		</div>
	</xsl:template>
</xsl:stylesheet>