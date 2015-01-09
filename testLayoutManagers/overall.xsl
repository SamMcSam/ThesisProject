<?xml version="1.0" encoding="UTF-8"?>
<!--
	Author : Samuel Constantino
	Created : 9/1/15
	Last update : 9/1/15
	***************************
	
	Document transform
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns="http://www.w3.org/1999/xhtml"

    xmlns:vizu="http://unige.ch/masterThesis/" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
>

	<!--Include layout managers here-->

	<xsl:template match="rdf:Description">
		<Visualization>
			<xsl:apply-templates />
		</Visualization>
    </xsl:template>


	<xsl:template match="/">
		<Visualizations>
			<xsl:apply-templates />
		</Visualizations>
    </xsl:template>
	

</xsl:stylesheet> 