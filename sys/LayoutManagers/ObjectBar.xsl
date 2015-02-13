<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectBar

	Author : Samuel Constantino
	Created : 13/2/15
	Last update : 13/2/15
	***************************

	Description : defines a bar that starts at a point and is of the size defined

	Parameters necessary : 
		vizu:weight
		vizu:height 
		vizu:proportion = use 1 or higher
		vizu:appearance = an Appearance node

	Returned object : 
		<shape>
		<IndexedFaceSet></IndexedFaceSet> 
		<appearance>
			[...]
		</appearance>
	</shape>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ObjectBar']">

		<xsl:variable name="weight" select="number(./vizu:weight)" />
		<xsl:variable name="proportion" select="number(./vizu:proportion)" />
		<xsl:variable name="height" select="number(./vizu:height) * $proportion" />

		<Shape>
			<IndexedFaceSet creaseAngle='1' solid='false' coordIndex='0 1 2 3 -1'>
				<Coordinate>
					<xsl:attribute name="point">

						<xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text>, </xsl:text>

						<xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0 + $height"/><xsl:text>, </xsl:text>

						<xsl:value-of select="0 + $weight"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0 + $height"/><xsl:text>, </xsl:text>

						<xsl:value-of select="0 + $weight"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text>, </xsl:text>

						<xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text> </xsl:text><xsl:value-of select="0"/><xsl:text>, </xsl:text>

					</xsl:attribute>
				</Coordinate>
			</IndexedFaceSet>

			<Appearance>
				<xsl:apply-templates select="./vizu:appearance" />	
			</Appearance>

		</Shape>

	</xsl:template>	

</xsl:stylesheet>