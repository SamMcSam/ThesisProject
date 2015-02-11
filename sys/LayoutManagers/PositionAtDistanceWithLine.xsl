<?xml version="1.0" encoding="UTF-8"?>
<!--
	PositionAtDistanceWithLine

	Author : Samuel Constantino
	Created : 10/2/2015
	Last update : 10/2/2015
	***************************

	Description : positions an object from a origin point to a distance (vector) with a line between the origin point and the object

	Parameters necessary : 
		vizu:originX = origin point
		vizu:originY
		vizu:originZ 
		vizu:distanceX = distance to the originX point
		vizu:distanceY
		vizu:distanceZ
		vizu:lineColor = (r g b)
		vizu:sceneObject = any object node with its own manager

	Returned object : 
		<Position translation="_ _ _">
		...
		</Position>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<!--Position object-->
	<xsl:template match="*[@typeLayout='PositionAtDistanceWithLine']">	

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

			<shape>
				<IndexedLineSet coordIndex='0 1'>
				<Coordinate DEF='Segment'>
					<xsl:attribute name="point">
						<xsl:value-of select="$distanceX * -1" /><xsl:text> </xsl:text>
						<xsl:value-of select="$distanceY * -1" /><xsl:text> </xsl:text>
						<xsl:value-of select="$distanceZ * -1" /><xsl:text> 0 0 0</xsl:text>
					</xsl:attribute>
				</Coordinate>
				</IndexedLineSet> 
				<appearance>
					<material>
						<xsl:attribute name="emissiveColor">
							<xsl:value-of select="./vizu:lineColor" />
						</xsl:attribute>
					</material> 
				</appearance>
			</shape>
		</position>

	</xsl:template>

	<!--copy this node as is-->
	<xsl:template match="vizu:sceneObject">
		<xsl:copy-of select="node()"/>
	</xsl:template>	

</xsl:stylesheet>