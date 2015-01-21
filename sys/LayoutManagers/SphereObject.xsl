<?xml version="1.0" encoding="UTF-8"?>
<!--
	SphereObject

	Author : Samuel Constantino
	Created : 9/1/15
	Last update : 16/1/15
	***************************

	Description : defines a 3d sphere object

	Parameters necessary : 
		vizu:radius = the size of the radius
		vizu:color = 3 float from 0-1 defined as 'r g b'

	Returned object : 
		<Shape>
			<Sphere radius='_'/>
			<Appearance>
				<Material diffuseColor='_ _ _'>
			</Appearance>
		<Shape>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 

    xmlns:vizu="http://unige.ch/masterThesis/" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
>

	<!--shape object-->
	<xsl:template match="*[@typeLayout='SphereObject']">

		<shape>
			<sphere>
				<xsl:attribute name="radius"><xsl:value-of select="./vizu:radius"/></xsl:attribute>
				<xsl:text> </xsl:text> <!--REALLY IMPORTANT!! - without it, php will create an empty tag (not accepted in x3d) -->
			</sphere>
			
			<appearance>
				<material>
				<xsl:attribute name="diffuseColor"><xsl:value-of select="./vizu:color"/></xsl:attribute>
				</material>
			</appearance>
		</shape>

	</xsl:template>	

</xsl:stylesheet>