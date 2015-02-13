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

	<xsl:template match="rdf:literal">
		<Shape>
			<IndexedFaceSet solid="false" coordIndex="0 1 2 3 -1" creaseAngle='1'>
				<xsl:element name="Coordinate">
					<xsl:attribute name="point">
						<xsl:value-of select="." />
					</xsl:attribute>
				</xsl:element>
			</IndexedFaceSet>
			<Appearance>
				<Material diffuseColor='0.8 0.8 0.8'>
				</Material>
			</Appearance>
		</Shape>		
    </xsl:template>

	<xsl:template match="/">
		<X3D width='600px' height='400px'>
			<Scene dopickpass="true" pickmode="idBuf" bboxsize="-1,-1,-1" bboxcenter="0,0,0" render="true">
			
				<Transform translation="0 0 0">
					<xsl:apply-templates />
				</Transform>
				
			</Scene>
		</X3D>
    </xsl:template>


</xsl:stylesheet> 