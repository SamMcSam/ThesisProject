<?xml version="1.0" encoding="UTF-8"?>
<!--
	Position

	Author : Samuel Constantino
	Created : 13/1/15
	Last update : 13/1/15
	***************************

	Description : Translates the generic Position into X3D transform tag
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:vizu="http://unige.ch/masterThesis/" >

	<!--Position object-->
	<xsl:template match="vizu:Position">
        <transform>
        	<xsl:apply-templates />
        </transform>
    </xsl:template>	

</xsl:stylesheet> 