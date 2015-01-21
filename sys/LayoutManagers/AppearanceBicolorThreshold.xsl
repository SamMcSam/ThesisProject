<?xml version="1.0" encoding="UTF-8"?>
<!--
	AppearanceBicolorThreshold

	Author : Samuel Constantino
	Created : 21/1/15
	Last update : 21/1/15
	***************************

	Description : defines a material one of two simple color textures according to a value

	Parameters necessary : 
		vizu:color1 = 3 float from 0-1 defined as 'r g b'
		vizu:color2 = 3 float from 0-1 defined as 'r g b'
		vizu:value = the data value that will decide which color to pick from color1 or color2
		vizu:theshold = the threshold to make the choice (color 1 is val<threshold, color 2 is val>=threshold)

	Returned object : 
		<Material diffuseColor='_ _ _'>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" >

	<!--shape object-->
	<xsl:template match="*[@typeLayout='AppearanceBicolorThreshold']">
		<material>

			<xsl:choose>
				<xsl:when test="./vizu:value &lt; ./vizu:theshold">
					<xsl:attribute name="diffuseColor"><xsl:value-of select="./vizu:color1"/></xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="diffuseColor"><xsl:value-of select="./vizu:color2"/></xsl:attribute>
				</xsl:otherwise>
	       </xsl:choose>
			
		</material>
	</xsl:template>	

</xsl:stylesheet>