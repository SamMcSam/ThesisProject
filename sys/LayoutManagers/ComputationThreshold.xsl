<?xml version="1.0" encoding="UTF-8"?>
<!--
	ComputationThreshold

	Author : Samuel Constantino
	Created : 21/1/15
	Last update : 14/2/15
	***************************

	Description : returns either one of two objects if a value is in a threshold, the other if not

	Parameters necessary : 
		vizu:object1 = any node
		vizu:object2 = any node
		vizu:value = the data value that will decide which object to pick from object1 or object2
		vizu:theshold = the threshold to make the choice 

	(object1 when val < threshold, object2 when val >= threshold)

	Returned object : 
		[a node]
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:vizu="http://unige.ch/masterThesis/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" >

	<!--shape object-->
	<xsl:template match="*[@typeLayout='ComputationThreshold']">

		<xsl:choose>
			<xsl:when test="./vizu:value &lt; ./vizu:theshold">
				<xsl:apply-templates select="./vizu:object1" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="./vizu:object2" />
			</xsl:otherwise>
       </xsl:choose>
			
	</xsl:template>	

</xsl:stylesheet>