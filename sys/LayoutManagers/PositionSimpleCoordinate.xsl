<?xml version="1.0" encoding="UTF-8"?>
<!--
	PositionSimpleCoordinate

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
	<xsl:template match="*[@typeLayout='PositionSimpleCoordinate']">	

		<position>
			<xsl:attribute name="translation">
				<xsl:value-of select="vizu:x" /><xsl:text> </xsl:text>
				<xsl:value-of select="vizu:y" /><xsl:text> </xsl:text>
				<xsl:value-of select="vizu:z" />
			</xsl:attribute>
			
			<xsl:apply-templates select="./vizu:sceneObject" />
		</position>

	</xsl:template>

	<!--copy this node as is-->
	<xsl:template match="vizu:sceneObject">
		<xsl:copy-of select="node()"/>
	</xsl:template>	
	

</xsl:stylesheet> 