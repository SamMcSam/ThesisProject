<?xml version="1.0" encoding="UTF-8"?>
<!--
	Author : Samuel Constantino
	Created : 13/1/15
	Last update : 13/1/15
	***************************
	
	Document transform
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
>

	<!--Include layout translater here-->
	<!--<xsl:include href="../Position.xsl"/>-->

	<!--removes remnants of rdf-->
	<xsl:template match="rdf:*">
	</xsl:template>


	<xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
        </xsl:copy>
    </xsl:template>

</xsl:stylesheet> 