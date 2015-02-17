<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectCone

	Author : Samuel Constantino
	Created : 22/1/15
	Last update : 17/2/15
	***************************

	Description : defines a 3d cone object

	Parameters necessary : 
		vizu:bottomRadius = radius of the base
		vizu:height = height at the tip
		vizu:appearance = an Appearance node

	Optional parameters :
		vizu:proportionRadius = modifier for the bottomRadius, default is 1
		vizu:proportionHeight = modifier for the height, default is 1

	Returned object : 
		<position> (so that the base is at Z position)
		<shape>
			<cone bottomRadius=#bottomRadius height=#height></cone> 
			<appearance>
				[...]
			</appearance>
		</shape>
	<position>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ObjectCone']">
		
		<!--optional parameters-->
		<xsl:variable name="proportionRadius">
			<xsl:choose>
				<xsl:when test="vizu:proportionRadius"><xsl:value-of select="number(vizu:proportionRadius)" /></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>				
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="proportionHeight">
			<xsl:choose>
				<xsl:when test="vizu:proportionHeight"><xsl:value-of select="number(vizu:proportionHeight)" /></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>				
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="bottomRadius" select="number(vizu:bottomRadius) * $proportionRadius" />
		<xsl:variable name="height" select="number(vizu:height) * $proportionHeight" />
		
		<Position>
			<xsl:attribute name="rotation">1 0 0 1.5708</xsl:attribute>
			<xsl:attribute name="translation">0<xsl:text> </xsl:text>0<xsl:text> </xsl:text><xsl:value-of select="$height div 2" /></xsl:attribute>

			<Shape>

				<xsl:apply-templates select="./vizu:sceneObject" />
			
				<Cone>
					<xsl:attribute name="bottomRadius"><xsl:value-of select="$bottomRadius"/></xsl:attribute>

					<xsl:attribute name="height"><xsl:value-of select="$height"/></xsl:attribute>

					<xsl:text> </xsl:text> <!--REALLY IMPORTANT!! - without it, php will create an empty tag (not accepted in x3d) -->
				</Cone>

				<Appearance>
					<xsl:apply-templates select="./vizu:appearance" />	
				</Appearance>

			</Shape>

	</Position>

	</xsl:template>	

</xsl:stylesheet>