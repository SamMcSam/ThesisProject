<?xml version="1.0" encoding="UTF-8"?>
<!--
	PositionBetweenTwoPointsWithLine

	Author : Samuel Constantino
	Created : 28/1/15
	Last update : 14/2/15
	***************************

	TODO either use a vizu:appearance OR figure out if lineweight works!!

	Description : defines an a line between 2 points
				Type 3 data - represents a relation, so it doesn't need a layout manager!!!

	Parameters necessary : 
		vizu:originX = origin point
		vizu:originY
		vizu:originZ
		vizu:endX = end point
		vizu:endY
		vizu:endZ
		vizu:appearanceLine = an Appearance node for the line
		vizu:sceneObject = any object node with its own manager

	Returned object : 
		....
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='PositionBetweenTwoPointsWithLine']">
		<xsl:variable name="originX" select="number(vizu:originX)" />
		<xsl:variable name="originY" select="number(vizu:originY)" />
		<xsl:variable name="originZ" select="number(vizu:originZ)" />
		<xsl:variable name="endX" select="number(vizu:endX)" />
		<xsl:variable name="endY" select="number(vizu:endY)" />
		<xsl:variable name="endZ" select="number(vizu:endZ)" />

		<Shape>
			<IndexedLineSet coordIndex='0 1'>
			<Coordinate DEF='Segment'>
				<xsl:attribute name="point">
					<xsl:value-of select="$originX" /><xsl:text> </xsl:text>
					<xsl:value-of select="$originY" /><xsl:text> </xsl:text>
					<xsl:value-of select="$originZ" /><xsl:text> </xsl:text>
					<xsl:value-of select="$endX" /><xsl:text> </xsl:text>
					<xsl:value-of select="$endY" /><xsl:text> </xsl:text>
					<xsl:value-of select="$endZ" /><xsl:text> </xsl:text>
				</xsl:attribute>
			</Coordinate>
			</IndexedLineSet>  

			<Appearance>
				<xsl:apply-templates select="./vizu:appearanceLine" />	
			</Appearance>	
			<!--
			<Appearance>
				<LineProperties DEF='TestLineProperties' linetype='1' linewidthScaleFactor='10' applied='true' containerField='lineProperties'>
					<MetadataString name='test LineProperties metadata child' containerField='metadata'/>
				</LineProperties>

				<Material>
					<xsl:attribute name="emissiveColor">
						<xsl:value-of select="./vizu:lineColor" />
					</xsl:attribute>
				</Material> 
			</Appearance>
			-->
			
		</Shape>

		<Position>
			<xsl:attribute name="translation">
				<xsl:value-of select="($originX + $endX) div 2" /><xsl:text> </xsl:text>
				<xsl:value-of select="($originY + $endY) div 2" /><xsl:text> </xsl:text>
				<xsl:value-of select="($originZ + $endZ) div 2" />
			</xsl:attribute>
			<xsl:text> </xsl:text>
			<xsl:apply-templates select="./vizu:sceneObject" />
		</Position>


	</xsl:template>	

</xsl:stylesheet>