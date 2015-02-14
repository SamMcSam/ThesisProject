<?xml version="1.0" encoding="UTF-8"?>
<!--
	PositionAtCoordinate

	Author : Samuel Constantino
	Created : 9/1/15
	Last update : 21/1/15
	***************************

	Description : most basic positionning for given three-dimensional coordinates of an object

	Parameters necessary : 
		vizu:x = x-axis coordinate
		vizu:y = y-axis coordinate
		vizu:z = z-axis coordinate
		vizu:sceneObject = any object node with its own manager

	Parameters optional :
		vizu:offsetX = an offset to the x coordinate (if none, is 0)
		vizu:offsetY = " " " "
		vizu:offsetZ = " " " "

	Returned object : 
		<Position translation="_ _ _">
		...
		</Position>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 

    xmlns:vizu="http://unige.ch/masterThesis/" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
>

	<!--Position object-->
	<xsl:template match="*[@typeLayout='PositionAtCoordinate']">

		<xsl:variable name="x" select="number(vizu:x)" />
		<xsl:variable name="y" select="number(vizu:y)" />
		<xsl:variable name="z" select="number(vizu:z)" />

		<xsl:variable name="offsetX">
			<xsl:choose>
				<xsl:when test="vizu:offsetX"><xsl:value-of select="vizu:offsetX" /></xsl:when>
				<xsl:otherwise>0</xsl:otherwise>				
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="offsetY">
			<xsl:choose>
				<xsl:when test="vizu:offsetY"><xsl:value-of select="vizu:offsetY" /></xsl:when>
				<xsl:otherwise>0</xsl:otherwise>				
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="offsetZ">
			<xsl:choose>
				<xsl:when test="vizu:offsetZ"><xsl:value-of select="vizu:offsetZ" /></xsl:when>
				<xsl:otherwise>0</xsl:otherwise>				
			</xsl:choose>
		</xsl:variable>

		<Position>
			<xsl:attribute name="translation">
				<xsl:value-of select="$x + number($offsetX)" /><xsl:text> </xsl:text>
				<xsl:value-of select="$y + number($offsetY)" /><xsl:text> </xsl:text>
				<xsl:value-of select="$z + number($offsetZ)" />
			</xsl:attribute>
			
			<xsl:apply-templates select="./vizu:sceneObject" />
		</Position>

	</xsl:template>

	<!--copy this node as is-->
	<xsl:template match="vizu:sceneObject">
		<xsl:copy-of select="node()"/>
	</xsl:template>	
	

</xsl:stylesheet> 