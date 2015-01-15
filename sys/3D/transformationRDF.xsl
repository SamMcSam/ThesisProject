<?xml version="1.0" encoding="UTF-8"?>
<!--
/* 
* Thesis project
* @author Samuel Constantin
* created : 15/1/2015
* last update : 15/1/2015
*
* Transforms the Sesame output into X3D of polygons
*/
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:rdf='http://www.w3.org/2005/sparql-results#' >

	<xsl:output method="xml" indent="yes"/>

	<xsl:template match="rdf:literal">
		<Shape>
			<IndexedFaceSet solid="false" coordIndex="0 1 2 3  ">
				<xsl:element name="Coordinate">
					<xsl:attribute name="point">
						<xsl:value-of select="." />
					</xsl:attribute>
				</xsl:element>
			</IndexedFaceSet>
			<Appearance>
				<Material diffuseColor='0 1 0' />
			</Appearance>
		</Shape>		
    </xsl:template>

	<xsl:template match="/">
		<scene dopickpass="true" pickmode="idBuf" bboxsize="-1,-1,-1" bboxcenter="0,0,0" render="true">
		
			<transform translation="0 0 0">
				<xsl:apply-templates />
			</transform>
			
		</scene>
    </xsl:template>


</xsl:stylesheet> 