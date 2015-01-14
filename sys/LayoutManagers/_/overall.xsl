<?xml version="1.0" encoding="UTF-8"?>
<!--
	Author : Samuel Constantino
	Created : 9/1/15
	Last update : 9/1/15
	***************************
	
	Document transform
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 

    xmlns:vizu="http://unige.ch/masterThesis/" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"     
>

	<!--Include layout managers here-->
	<!--<xsl:include href="../SimplePositionOnCoordinate.xsl"/>-->
	<!--<xsl:include href="../SphereObject.xsl"/>-->

	<xsl:template match="rdf:Description">
		<visualization>
			<xsl:apply-templates />
		</visualization>
    </xsl:template>


	<xsl:template match="rdf:RDF">
		<visualizations>
			<xsl:apply-templates />
		</visualizations>
    </xsl:template>
	
	<xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
        </xsl:copy>
    </xsl:template>
	

</xsl:stylesheet> 