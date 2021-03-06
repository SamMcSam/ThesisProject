<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectStar

	Author : Samuel Constantino
	Created : 28/1/15
	Last update : 17/2/15
	***************************

	Description : defines an example of a more complex 3d object

	Parameters necessary : 
		vizu:scale = size of the object
		vizu:appearance = an Appearance node
	Optional parameters:
		vizu:proportion = applied to the scale

	Returned object : 
		<position> (so that the base is at Z position)
			<shape>
				[a complex shape]
				<appearance>
					[...]
				</appearance>
			</shape>
		<position>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ObjectStar']">

		<!--optional parameters-->
		<xsl:variable name="proportion">
			<xsl:choose>
				<xsl:when test="vizu:proportion"><xsl:value-of select="number(vizu:proportion)" /></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>				
			</xsl:choose>
		</xsl:variable>

		<!--required parameters-->
		<xsl:variable name="scale" select="number(vizu:scale) * $proportion" />
		
		<Position>
			<xsl:attribute name="scale"><xsl:value-of select="$scale" /><xsl:text> </xsl:text><xsl:value-of select="$scale" /><xsl:text> </xsl:text><xsl:value-of select="$scale" /></xsl:attribute>
			
			<Group  DEF="horiz" >
				<Shape  DEF="spike" >
			       <Appearance>
						<xsl:apply-templates select="./vizu:appearance" />	
					</Appearance>

			       	<Cylinder  radius="0.5" height="5.0"/>
			    </Shape>
			    <Position  rotation="1 0 0 1.5708">
			        <Shape  USE="spike"><xsl:text> </xsl:text></Shape>
			    </Position>

			</Group>
			
			<Position rotation="0 1 0 1.5708">
					<Shape USE="horiz"><xsl:text> </xsl:text></Shape>
			</Position>
		</Position>

	</xsl:template>	

</xsl:stylesheet>