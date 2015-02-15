<?xml version="1.0" encoding="UTF-8"?>
<!--
	ElevationGrid 

	Author : Samuel Constantino
	Created : 15/2/15
	Last update : 15/2/15
	***************************

	Description : Translates the ElevationGrid with child nodes into an X3D ElevationGrid with a single attribute node
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!--Position object-->
	<xsl:template match="ElevationGrid|elevationgrid|elevationGrid ">
        <ElevationGrid>
	    	<xsl:attribute name="solid"><xsl:value-of select="@solid" /></xsl:attribute>
	    	<xsl:attribute name="creaseAngle"><xsl:value-of select="@creaseAngle" /></xsl:attribute>
	    	<xsl:attribute name="xDimension"><xsl:value-of select="@xDimension" /></xsl:attribute>
	    	<xsl:attribute name="zDimension"><xsl:value-of select="@zDimension" /></xsl:attribute>
	    	<xsl:attribute name="xSpacing"><xsl:value-of select="@xSpacing" /></xsl:attribute>
	    	<xsl:attribute name="zSpacing"><xsl:value-of select="@zSpacing" /></xsl:attribute>

	    	<xsl:attribute name="height">
	    		<xsl:for-each select="ElevationGridEntry">
	    			<xsl:sort select="x"/>
	    			<xsl:sort select="y"/>
			        <xsl:value-of select="value"/><xsl:text> </xsl:text>
			    </xsl:for-each>
	    	</xsl:attribute>
		</ElevationGrid>
    </xsl:template>	

</xsl:stylesheet> 