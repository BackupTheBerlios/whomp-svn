<?xml version="1.0" encoding="utf-8" ?>
<!-- $Id$ -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output omit-xml-declaration="no" method="xml" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" indent="yes" encoding="utf-8"/>
	<xsl:template match="/layout">
		<html>
			<head>
				<xsl:copy-of select="$whomp_head" />
				<link rel="stylesheet" href="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/files/frontpage.css" type="text/css" />
			</head>
			<body onload="{$whomp_onload}">
				<div id="bodycenter">
					<div id="header">
						<h1>XA Tulsa</h1>
					</div>
					<div id="navigation">
						<div id="navigationcenter">
							<div class="col">
								<div class="row-one">
									<a href="{$_whomp_base_url}/about" onmouseover="imgover('about');" onmouseout="imgout('about');">
										<div class="txt-over" id="txt-about">About XA</div>
										<img class="img" id="img-about" src="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/images/about-out.png" alt="About XA" />
									</a>
								</div>
								<div class="row-two">
									<a href="{$_whomp_base_url}/prayer" onmouseover="imgover('prayer');" onmouseout="imgout('prayer');">
										<div class="txt-over" id="txt-prayer">Peak of the Week</div>
										<img class="img" id="img-prayer" src="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/images/prayer-out.png" alt="Peak of the Week" />
									</a>
								</div>
							</div>
							<div class="col">
								<div class="row-one">
									<a href="{$_whomp_base_url}/service" onmouseover="imgover('service');" onmouseout="imgout('service');">
										<div class="txt-over" id="txt-service">Rockin' Sockin' Tuesdays</div>
										<img class="img" id="img-service" src="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/images/service-out.png" alt="Rockin' Sockin' Tuesdays" />
									</a>
								</div>
								<div class="row-two">
									<a href="{$_whomp_base_url}/outreach" onmouseover="imgover('outreach');" onmouseout="imgout('outreach');">
										<div class="txt-over" id="txt-outreach">Reaching Out</div>
										<img class="img" id="img-outreach" src="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/images/outreach-out.png" alt="Reaching Out" />
									</a>
								</div>
							</div>
							<div class="col">
								<div class="row-one">
									<a href="{$_whomp_base_url}/bible" onmouseover="imgover('bible');" onmouseout="imgout('bible');">
										<div class="txt-over" id="txt-bible">Get Yo' Word On</div>
										<img class="img" id="img-bible" src="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/images/bible-out.png" alt="Get Yo' Word On" />
									</a>
								</div>
								<div class="row-two">
									<a href="{$_whomp_base_url}/more" onmouseover="imgover('more');" onmouseout="imgout('more');">
										<div class="txt-over" id="txt-more">More Info</div>
										<img class="img" id="img-more" src="{$_whomp_storage_url}/templates/xatulsa.com-frontpage/images/more-out.png" alt="More Info" />
									</a>
								</div>
							</div>
						</div>
					</div>
					<div id="footer">
						
					</div>
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
</xsl:stylesheet>