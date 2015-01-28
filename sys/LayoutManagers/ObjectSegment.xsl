<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectSegment

	Author : Samuel Constantino
	Created : 28/1/15
	Last update : 28/1/15
	***************************

	Description : defines an a line between 2 points

	Parameters necessary : 
		vizu:originX = origin point
		vizu:originY
		vizu:originZ

	Parameters optional :
		vizu:endX = end point
		vizu:endY
		vizu:endZ

	Returned object : 
		<shape>
			[a complex shape]
			<appearance>
				[...]
			</appearance>
		</shape>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ObjectSegment']">
			
		<xsl:choose>
			<xsl:when test="./vizu:endX">
				<xsl:variable name="endX"><xsl:value-of select="./vizu:endX" /></xsl:variable>
			</xsl:when>
			<xsl:otherwise>
			    <xsl:variable name="endX"><xsl:value-of select="./vizu:originX" /></xsl:variable>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="./vizu:endY">
				<xsl:variable name="endY"><xsl:value-of select="./vizu:endY" /></xsl:variable>
			</xsl:when>
			<xsl:otherwise>
			    <xsl:variable name="endY"><xsl:value-of select="./vizu:originY" /></xsl:variable>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="./vizu:endZ">
				<xsl:variable name="endZ"><xsl:value-of select="./vizu:endZ" /></xsl:variable>
			</xsl:when>
			<xsl:otherwise>
			    <xsl:variable name="endZ"><xsl:value-of select="./vizu:originZ" /></xsl:variable>
			</xsl:otherwise>
		</xsl:choose>

		<shape>
			<IndexedLineSet coordIndex='0 1'>
				<Coordinate>
					<xsl:attribute name="point"><xsl:value-of select="./vizu:originX" /><xsl:text> </xsl:text><xsl:value-of select="./vizu:originY" /><xsl:text> </xsl:text><xsl:value-of select="./vizu:originZ" /><xsl:text> </xsl:text><xsl:value-of select="$endX" /><xsl:text> </xsl:text><xsl:value-of select="$endY" /><xsl:text> </xsl:text><xsl:value-of select="$endZ" /><xsl:text> </xsl:text></xsl:attribute>
				</Coordinate>
			</IndexedLineSet> 
		 	<appearance>
				<material diffuseColor="0 0 0"/>
			</appearance>
		</shape> 

	</xsl:template>	

</xsl:stylesheet>