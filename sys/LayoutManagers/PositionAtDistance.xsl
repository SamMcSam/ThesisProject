<?xml version="1.0" encoding="UTF-8"?>
<!--
	PositionAtDistance

	Author : Samuel Constantino
	Created : 10/2/2015
	Last update : 10/2/2015
	***************************

	Description : positions an object from a origin point to a distance (vector)

	Parameters necessary : 
		vizu:originX = origin point
		vizu:originY
		vizu:originZ 
		vizu:distanceX = distance to the originX point
		vizu:distanceY
		vizu:distanceZ
		vizu:sceneObject = any object node with its own manager

	Returned object : 
		<Position translation="_ _ _">
		...
		</Position>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<!--Position object-->
	<xsl:template match="*[@typeLayout='PositionAtDistance']">	

		<!--Store in variables for simpler math (TRANSFORM THE VALUES!!)-->
		<xsl:variable name="originX" select="number(vizu:originX)" />
		<xsl:variable name="originY" select="number(vizu:originY)" />
		<xsl:variable name="originZ" select="number(vizu:originZ)" />
		<xsl:variable name="distanceX" select="number(vizu:distanceX)" />
		<xsl:variable name="distanceY" select="number(vizu:distanceY)" />
		<xsl:variable name="distanceZ" select="number(vizu:distanceZ)" />

		<position>
			<xsl:attribute name="translation">
				<xsl:value-of select="$originX + $distanceX" /><xsl:text> </xsl:text>
				<xsl:value-of select="$originY + $distanceY" /><xsl:text> </xsl:text>
				<xsl:value-of select="$originZ + $distanceZ" />
			</xsl:attribute>
			
			<xsl:apply-templates select="./vizu:sceneObject" />
		</position>

	</xsl:template>

	<!--copy this node as is-->
	<xsl:template match="vizu:sceneObject">
		<xsl:copy-of select="node()"/>
	</xsl:template>	

</xsl:stylesheet>