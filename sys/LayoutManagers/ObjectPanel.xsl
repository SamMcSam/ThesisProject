<?xml version="1.0" encoding="UTF-8"?>
<!--
	ObjectPanel

	Author : Samuel Constantino
	Created : 27/1/15
	Last update : 27/1/15
	***************************

	Description : defines a 3d panel object, which presents text in front of a colored flat box

	Parameters necessary : 
		vizu:content = the textual content (TODO, support for pictures)
		vizu:fontSize = size of the textual content
		vizu:appearanceText = an Appearance node for the textual content
		vizu:appearancePanel = an Appearance node for the panel behind it
		vizu:orientation = orientation of the panel - x y z angle(radian) (for default use "0 0 0 0") (for facing one direction "0 1 0 1.57079633")
		vizu:border = size of the label around the text

	Returned object : 
		<Position orientation='$orientation'>
			<Position translation='0 0 -1'>
				<Shape>
					<Box size='#computed #computed 1'/>
					<Appearance>
						[...]
					</Appearance>
				</Shape>
			</Position>
			<Shape>
				<Text string='$content'/>
					<FontStyle size='$fontSize'>
				</Text>
				<Appearance>
					[...]
				</Appearance>
			</Shape>
		</Position>
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 

    xmlns:vizu="http://unige.ch/masterThesis/" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
>
	<!--shape object-->
	<xsl:template match="*[@typeLayout='ObjectPanel']">

		<xsl:variable name="length"><xsl:value-of select="string-length(./vizu:content)" /></xsl:variable>
		<!--TODO: add computation for multiline here?-->
		<xsl:variable name="height"><xsl:value-of select="./vizu:fontSize" /></xsl:variable>

		<!--overall orientation-->
		<Position>
			<xsl:attribute name="rotation">
				<xsl:value-of select="./vizu:orientation"/>
			</xsl:attribute>

			<!--box behind-->
			<Position translation="0 0 -1">
				<Shape>
					<Appearance>
						<xsl:apply-templates select="./vizu:appearancePanel" />	
					</Appearance>	

					<Box>
						<xsl:attribute name="size">
							<xsl:value-of select="$length * 3 + ./vizu:border"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="$height + ./vizu:border"/>
							<xsl:text> </xsl:text>1</xsl:attribute>
						<xsl:text> </xsl:text>
					</Box>
				</Shape>
			</Position>

			<!--text-->
			<Shape>
				<Appearance>
					<xsl:apply-templates select="./vizu:appearanceText" />	
				</Appearance>	

				<Text>
					<xsl:attribute name="string"><xsl:value-of select="./vizu:content"/></xsl:attribute>
					<FontStyle>
						<xsl:attribute name="size"><xsl:value-of select="./vizu:fontSize"/></xsl:attribute>
						<xsl:text> </xsl:text>
					</FontStyle>
				</Text>
			</Shape>

		</Position>

	</xsl:template>	
	
</xsl:stylesheet>