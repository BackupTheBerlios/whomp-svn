<?xml version="1.0" encoding="utf-8"?>
<!-- $Id: transformxsl.xsl 1162 2005-02-14 18:07:34Z chregu $ -->
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:xslt="output.xsl">
    <xsl:output method="xml" encoding="utf-8"/>
    <xsl:namespace-alias stylesheet-prefix="xslt" result-prefix="xsl"/>

    <xsl:template match="/">

        <xsl:apply-templates/>

    </xsl:template>

 <xsl:template match="xsl:stylesheet">
        <xsl:copy>
        <xsl:for-each select="@*">
                <xsl:copy/>
            </xsl:for-each>
            
           <!-- <xsl:copy-of select="document('elementpath.xsl')/root/*"/>-->
        
            <xsl:apply-templates/>
            </xsl:copy>
       
    </xsl:template>

<!--
<xsl:template match="xsl:apply-templates">
    <xhtml:div id="{generate-id()}" bxe_xpath="{@select}"></xhtml:div>
</xsl:template>-->

    <xsl:template match="*">
        <xsl:copy>

            <xsl:for-each select="@*">
                <xsl:copy/>
            </xsl:for-each>
            
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="xsl:include">
        <xsl:apply-templates select="document(@href)/xsl:stylesheet/xsl:template"/>
    </xsl:template>

    <xsl:template match="xsl:value-of[not (ancestor::xsl:attribute) and not(contains(@select,'('))]|xsl:copy-of">
    <span> <xsl:element name="attribute" namespace="http://www.w3.org/1999/XSL/Transform">
                <xsl:attribute name="name">__bxe_id</xsl:attribute>
                    <xsl:element name="value-of" namespace="http://www.w3.org/1999/XSL/Transform">
                        
                      <xsl:choose>
                            <xsl:when test="contains(@select, '@')"><xsl:attribute name="select"><xsl:value-of select="@select"/>/parent::*/@__bxe_id</xsl:attribute></xsl:when>
                            <xsl:when test="@select"><xsl:attribute name="select"><xsl:value-of select="@select"/>/@__bxe_id</xsl:attribute></xsl:when>
                            <xsl:otherwise><xsl:attribute name="select">@__bxe_id</xsl:attribute></xsl:otherwise>
                      </xsl:choose>
                    </xsl:element>
                </xsl:element>
                <!-- attribute nodes... -->
                <xsl:if test="contains(@select, '@')">
                <xsl:element name="attribute" namespace="http://www.w3.org/1999/XSL/Transform"><xsl:attribute name="name">__bxe_attribute</xsl:attribute><xsl:value-of select="substring-after(@select,'@')"/></xsl:element>
                </xsl:if>
                <xsl:element name="copy-of" namespace="http://www.w3.org/1999/XSL/Transform"><xsl:attribute name="select"><xsl:choose>
                            <xsl:when test="@select"><xsl:value-of select="@select"/>/@__bxe_defaultcontent</xsl:when>
                            <xsl:otherwise>@__bxe_defaultcontent</xsl:otherwise>
                      </xsl:choose></xsl:attribute></xsl:element>
                
                

        <xsl:copy>
            <xsl:for-each select="@*">
                <xsl:copy/>
            </xsl:for-each>
            

            <xsl:apply-templates/>
        </xsl:copy>

</span>
    </xsl:template>

   <xsl:template match="*[(namespace-uri() = '' and xsl:apply-templates) or 
                          (namespace-uri()='http://www.w3.org/1999/XSL/Transform' and local-name()='element')]">
  
      <xsl:copy><xsl:for-each select="@*">
                <xsl:copy/>
            </xsl:for-each><xsl:element name="attribute" namespace="http://www.w3.org/1999/XSL/Transform">
                <xsl:attribute name="name">__bxe_id</xsl:attribute>
                    <xsl:element name="value-of" namespace="http://www.w3.org/1999/XSL/Transform">
                      <xsl:choose>
                            <xsl:when test="@select"><xsl:attribute name="select"><xsl:value-of select="@select"/>/@__bxe_id</xsl:attribute></xsl:when>
                            <xsl:otherwise><xsl:attribute name="select">@__bxe_id</xsl:attribute></xsl:otherwise>
                      </xsl:choose>
                    </xsl:element>
                </xsl:element>
                 <xsl:element name="copy-of" namespace="http://www.w3.org/1999/XSL/Transform"><xsl:attribute name="select">@__bxe_defaultcontent</xsl:attribute></xsl:element>
                
            <xsl:apply-templates/></xsl:copy>
       
    </xsl:template>
    
    <xsl:template match="*[namespace-uri() = '' and ancestor::xsl:template and not(descendant::xsl:value-of[not(contains(@select,'@') and not (contains(@select,'[')) and not( contains(substring-before(@select,'['),'@')))]) and not (descendant::xsl:apply-templates)]">

      <xsl:copy><xsl:call-template name="descendantOfTemplate"/></xsl:copy>
       
    </xsl:template>
    
    <xsl:template name="descendantOfTemplate">
    <xsl:for-each select="@*">
                <xsl:copy/>
            </xsl:for-each><xsl:element name="attribute" namespace="http://www.w3.org/1999/XSL/Transform">
                <xsl:attribute name="name">__bxe_id</xsl:attribute>
                    <xsl:element name="value-of" namespace="http://www.w3.org/1999/XSL/Transform">
                      <xsl:choose>
                            <xsl:when test="@select"><xsl:attribute name="select"><xsl:value-of select="@select"/>/@__bxe_id</xsl:attribute></xsl:when>
                            <xsl:otherwise><xsl:attribute name="select">@__bxe_id</xsl:attribute></xsl:otherwise>
                      </xsl:choose>
                    </xsl:element>
                </xsl:element>
                <xsl:element name="copy-of" namespace="http://www.w3.org/1999/XSL/Transform"><xsl:attribute name="select">@__bxe_defaultcontent</xsl:attribute></xsl:element>
                
                
            <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="xsl:value-of[starts-with(@select,'py:')]">
        <xsl:value-of select="@select"/>
    </xsl:template>
  
  
  
  
<!-- rewrite a tags -->
<xsl:template match="a|xhtml:a|script|xhtml:script|link|xhtml:link">

    <span class="{local-name()}" __bxe_ns="{namespace-uri()}">
    <xsl:for-each select="@*">
                <xsl:copy/>
            </xsl:for-each>
            <xsl:apply-templates/>
          </span>
          </xsl:template>


</xsl:stylesheet>

