<?php
	/*
		Test applying layout managers
	*/

	//header('Content-type: text/xml');

	$RDF_NAME = "rdf:type";
	$RDF_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";

	$PATH_TEMPLATES = "";
	$EXTENSION = ".xsl";

	$NS_XSL = "http://www.w3.org/1999/XSL/Transform";

	//-------------------------------

	$fileName = "cleanedOutput2.xml";
	$xml = new DOMDocument();
	$xml->load($fileName);

	$xpath = new DOMXPath($xml);
	$xpath->registerNamespace("rdf", $RDF_URI);

	// First get name of all layout managers used
	$listTypetag = $xpath->query('//'.$RDF_NAME . '[string-length(text()) > 0]');

	$layoutNames = array();

	foreach ($listTypetag as $typeTag){

		$layout = $typeTag->nodeValue;

		//echo "$layout<br>"; 

		if(!in_array($layout, $layoutNames)){
        	$layoutNames[]=$layout;
        }
	}
	//print_r($layoutNames);
	//echo "<br>";

	// LAYOUT MANAGERS
	//-------------------------------------------------------------

	$xslt = new XSLTProcessor();
	$xslSheet = new DOMDocument();


	//generic style for document
	$xslSheet->load($PATH_TEMPLATES . "overall" . $EXTENSION);


	$xpath = new DOMXPath($xslSheet);
	$xpath->registerNamespace("xsl",$NS_XSL);

	//for each layout manager, adds the appropriate call in xslt
	foreach ($layoutNames as $name) 
	{
		$include = $xslSheet->createElementNS($NS_XSL,"xsl:include");
		//$attr = $xslSheet->createAttribute("href");
		//$attr->value = $PATH_TEMPLATES . $name . $EXTENSION;
		//$include->appendChild($attr); 
		$include->setAttribute("href", $PATH_TEMPLATES . $name . $EXTENSION);

		//$xslSheet->firstChild->appendChild($include);

		$xpath->query('//xsl:stylesheet')->item(0)->appendChild($include);
	}

	//apply style sheet
	$xslt->importStylesheet($xslSheet);
	$xml = $xslt->transformToDoc($xml);

/*
	//for each layout manager, call the appropriate xsl stylesheet
	foreach ($layoutNames as $name) 
	{
		// load
		//echo "loading file : " . $PATH_TEMPLATES . $name . $EXTENSION . "<br>";
		$xslSheet->load($PATH_TEMPLATES . $name . $EXTENSION);
		$xslt->importStylesheet($xslSheet);

		// apply templates
		$xml = $xslt->transformToDoc($xml);
	}
*/
	//echo "<pre>";
	echo $xml->saveXML();
	//echo "</pre>";
	
?>