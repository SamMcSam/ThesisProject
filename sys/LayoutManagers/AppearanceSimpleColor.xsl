<?xml version="1.0" encoding="UTF-8"?>
<!--
	AppearanceSimpleColor

	Author : Samuel Constantino
	Created : 21/1/15
	Last update : 14/2/15
	***************************

	Description : defines a material with a simple vextex coloring

	Parameters optional : 
		vizu:diffuseColor = 3 float from 0-1 defined as 'r g b'
		vizu:emissiveColor = 3 float from 0-1 defined as 'r g b'

	Returned object : 
		<Material diffuseColor='_ _ _' emissiveColor='_ _ _'>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" >

	<!--shape object-->
	<xsl:template match="*[@typeLayout='AppearanceSimpleColor']">
		<Material>


			<xsl:if test="./vizu:diffuseColor">
				<xsl:attribute name="diffuseColor">
					<xsl:value-of select="./vizu:diffuseColor"/>
				</xsl:attribute>
			</xsl:if>

			<xsl:if test="./vizu:emissiveColor">
				<xsl:attribute name="emissiveColor">
					<xsl:value-of select="./vizu:emissiveColor"/>
				</xsl:attribute>
			</xsl:if>

		</Material>
	</xsl:template>	

</xsl:stylesheet>