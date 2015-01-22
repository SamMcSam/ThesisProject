<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectCone

	Author : Samuel Constantino
	Created : 22/1/15
	Last update : 22/1/15
	***************************

	Description : defines a 3d cone object

	Parameters necessary : 
		vizu:bottomRadius = radius of the base
		vizu:height = height at the tip
		vizu:appearance = an Appearance node

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
		
		<position>
			<xsl:attribute name="rotation">1 0 0 1.5708</xsl:attribute>
			<xsl:attribute name="translation">0<xsl:text> </xsl:text>0<xsl:text> </xsl:text><xsl:value-of select="./vizu:height div 2" /></xsl:attribute>

			<shape>

				<xsl:apply-templates select="./vizu:sceneObject" />
			
				<cone>
					<xsl:attribute name="bottomRadius"><xsl:value-of select="./vizu:bottomRadius"/></xsl:attribute>

					<xsl:attribute name="height"><xsl:value-of select="./vizu:height"/></xsl:attribute>

					<xsl:text> </xsl:text> <!--REALLY IMPORTANT!! - without it, php will create an empty tag (not accepted in x3d) -->
				</cone>

				<appearance>
					<xsl:apply-templates select="./vizu:appearance" />	
				</appearance>

			</shape>

	</position>

	</xsl:template>	

</xsl:stylesheet>