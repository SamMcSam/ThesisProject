<?xml version="1.0" encoding="UTF-8"?>
<!--
	AppearanceSimpleColor

	Author : Samuel Constantino
	Created : 21/1/15
	Last update : 21/1/15
	***************************

	Description : defines a material with a simple color texture

	Parameters necessary : 
		vizu:diffuseColor = 3 float from 0-1 defined as 'r g b'

	Returned object : 
		<Material diffuseColor='_ _ _'>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" >

	<!--shape object-->
	<xsl:template match="*[@typeLayout='AppearanceSimpleColor']">
		<material>
			<xsl:attribute name="diffuseColor"><xsl:value-of select="./vizu:diffuseColor"/></xsl:attribute>
		</material>
	</xsl:template>	

</xsl:stylesheet>