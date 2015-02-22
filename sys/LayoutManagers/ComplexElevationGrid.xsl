<?xml version="1.0" encoding="UTF-8"?>
<!--
	ComplexElevationGrid

	Author : Samuel Constantino
	Created : 15/2/15
	Last update : 15/2/15
	***************************

	Description : an elevation grid to simulate an isosurface

	Parameters necessary : 
		vizu:x
		vizu:y 
		vizu:z
		vizu:offsetZ
		vizu:val
		vizu:proportion = use 1 or higher
		vizu:color = "r g b"
		vizu:creaseAngle = angle (between 0,1)

	Returned object : 
		....
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ComplexElevationGrid']">
		<!--Parameters-->
		<xsl:variable name="x" select="number(vizu:x)" />
		<xsl:variable name="y" select="number(vizu:y)" />
		<xsl:variable name="z" select="number(vizu:z)" />
		<xsl:variable name="val" select="number(vizu:val)" />
		<xsl:variable name="proportion" select="number(vizu:proportion)" />

		<ElevationGridEntry>
			<x><xsl:value-of select="$x"/></x>
			<y><xsl:value-of select="$y"/></y>
			<z><xsl:value-of select="$z"/></z>
			<value><xsl:value-of select="$val * $proportion"/></value>
		</ElevationGridEntry>
	</xsl:template>	

	<xsl:key name="xvalues" match="*[@typeLayout='ComplexElevationGrid']" use="vizu:x" />
	<xsl:key name="yvalues" match="*[@typeLayout='ComplexElevationGrid']" use="vizu:y" />

	<xsl:template match="/">
		<xsl:if test="count(//*[@typeLayout='ComplexElevationGrid']) &gt; 0">

			<!--PARAMETERS-->
			<xsl:variable name="z" select="//*[@typeLayout='ComplexElevationGrid']/vizu:z[1]" />
			<xsl:variable name="offsetZ" select="//*[@typeLayout='ComplexElevationGrid']/vizu:offsetZ[1]" />
			<xsl:variable name="color" select="//*[@typeLayout='ComplexElevationGrid']/vizu:color[1]" />
			<xsl:variable name="creaseAngle" select="//*[@typeLayout='ComplexElevationGrid']/vizu:creaseAngle[1]" />

			<xsl:variable name="nbrx" select="count(//*[@typeLayout='ComplexElevationGrid'] [ generate-id() = generate-id(key('xvalues', vizu:x))])"/>	
			<xsl:variable name="nbry" select="count(//*[@typeLayout='ComplexElevationGrid'] [ generate-id() = generate-id(key('yvalues', vizu:y))])"/>	

			<xsl:variable name="smallestx">
				<xsl:for-each select="//*[@typeLayout='ComplexElevationGrid']/vizu:x">
	         		<xsl:sort select="vizu:x"/>	
	         		<xsl:if test="position() = 1"><xsl:value-of select="."/></xsl:if>
	      		</xsl:for-each>		
      		</xsl:variable>
			<xsl:variable name="smallesty">
				<xsl:for-each select="//*[@typeLayout='ComplexElevationGrid']/vizu:y">
	         		<xsl:sort select="vizu:y"/>	
	         		<xsl:if test="position() = 1"><xsl:value-of select="."/></xsl:if>
	      		</xsl:for-each>		
      		</xsl:variable>

			<xsl:variable name="highx">
				<xsl:for-each select="//*[@typeLayout='ComplexElevationGrid']/vizu:x">
	         		<xsl:sort select="vizu:x"/>	
	         		<xsl:if test="position() = last()"><xsl:value-of select="."/></xsl:if>
	      		</xsl:for-each>		
      		</xsl:variable>
			<xsl:variable name="highy">
				<xsl:for-each select="//*[@typeLayout='ComplexElevationGrid']/vizu:y">
	         		<xsl:sort select="vizu:y"/>	
	         		<xsl:if test="position() = last()"><xsl:value-of select="."/></xsl:if>
	      		</xsl:for-each>		
      		</xsl:variable>

      		<!--  ...  -->

      		<visualizations>  <!--DEBUG!!! TODO find a better way without extra visu tags!-->
      		<visualization> <!--ie, put this code NOT in the match="/" template!!!!-->
      		<Position rotation='1 0 0 1.57'>

      			<xsl:attribute name="translation">
					<xsl:value-of select="$smallestx" /><xsl:text> </xsl:text>
					<xsl:value-of select="$highy" /><xsl:text> </xsl:text>
					<xsl:value-of select="number($z) + number($offsetZ)" />
				</xsl:attribute>

			    <Shape>
			    <Appearance>
					<Material>
						<xsl:attribute name="diffuseColor">
							<xsl:value-of select="$color" />
						</xsl:attribute>
					</Material>
			    </Appearance> 
			    <ElevationGrid solid='false'>
			    	<xsl:attribute name="creaseAngle"><xsl:value-of select="$creaseAngle" /></xsl:attribute>
			    	<xsl:attribute name="xDimension"><xsl:value-of select="$nbrx" /></xsl:attribute>
			    	<xsl:attribute name="zDimension"><xsl:value-of select="$nbry" /></xsl:attribute>
			    	<xsl:attribute name="xSpacing">
			    		<xsl:value-of select="($highx - $smallestx) div ($nbrx - 1)" />
			    	</xsl:attribute>
			    	<xsl:attribute name="zSpacing">
			    		<xsl:value-of select="($highy - $smallesty) div ($nbry - 1)" />
			    	</xsl:attribute>
				
			    	<xsl:apply-templates select="//*[@typeLayout='ComplexElevationGrid']"/>
				</ElevationGrid>
			    </Shape>

			</Position>
			</visualization>
			</visualizations>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>