<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectBox

	Author : Samuel Constantino
	Created : 22/1/15
	Last update : 22/1/15
	***************************

	Description : defines a 3d box object

	Parameters necessary : 
		vizu:sizeX = size
		vizu:sizeY = size
		vizu:sizeZ = size
		vizu:appearance = an Appearance node

	Returned object : 
		<shape>
		<box size='#sizeX #sizeY #sizeZ'></box> 
		<appearance>
			[...]
		</appearance>
	</shape>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ObjectBox']">

		<shape>
			<box>
				<xsl:attribute name="size">
					<xsl:value-of select="./vizu:sizeX"/><xsl:text> </xsl:text><xsl:value-of select="./vizu:sizeY"/><xsl:text> </xsl:text><xsl:value-of select="./vizu:sizeZ"/> 
				</xsl:attribute>
				<xsl:text> </xsl:text> <!--REALLY IMPORTANT!! - without it, php will create an empty tag (not accepted in x3d) -->
			</box> 
			<appearance>
				<xsl:apply-templates select="./vizu:appearance" />	
			</appearance>
		</shape>

	</xsl:template>	

</xsl:stylesheet>