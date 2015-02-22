<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 14/1/2015
* last update : 14/1/2015
*
* queries the repository to retrieve the 3D scene
* adds the visualization objects to create an enriched city model
* 
* note that this model only supports X3D as output for the mb_decode_numericentity(str, convmap, encoding)
*/

require_once('../config/constantsPath.php');

require_once('SesameInterface.class.php');

require_once('VisualizationResult.class.php');


class ModelResult 
{
	//const RDF_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	//const XSL_URI = "http://www.w3.org/1999/XSL/Transform";

	//const LANG_X3D = "X3D";

	//const GENERIC_FILE = "_/overall";
	const CLEANING_STYLESHEET = "transformationRDF.xsl";

	private $sesame;
	private $exportTextures;
	private $xml;

	//---------------------------------------------------------------------------------------------

	function __construct(SesameInterface $sesame, $query, $exportTextures=false)
	{
		$this->sesame = $sesame;
		$this->exportTextures = $exportTextures;
		$this->reponse = null;
		$this->xml = null;

		$query = $this->generateQuery();
		$reponse = $sesame->query($query , 'Accept: ' . SesameInterface::SPARQL_XML);

		$this->xml = new DOMDocument();
		$this->xml->loadXML($reponse);

		$this->cleanResult();
	}

	// TODO EXPORTING TEXTURES!!
	private function generateQuery()
	{
		$query = "
			PREFIX gml:<http://www.opengis.net/gml>

			SELECT DISTINCT ?val
		";

		$query .= "	
			WHERE {
			 ?x a gml:LinearRing.
			 ?x gml:posList ?val.
		";

		if ($this->exportTextures){
			//$query .= "SOMETHING ELSE?";
		}

		$query .= "  
			}	
		";

		return $query;
	}

	//TODO export textures
	private function cleanResult()
	{
		//style sheet to transform results
		$xslSheet = new DOMDocument();
		$xslSheet->load(PATH_3DSYS . ModelResult::CLEANING_STYLESHEET);

		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xslSheet);
		$this->xml = $xslt->transformToDoc($this->xml);

		//textures
		if ($this->exportTextures){
			// something something textures whatnot
		}

		//centering model
		$this->centering();

		/*
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		*/
	}

	//
	private function centering()
	{
		$xpath = new DOMXPath($this->xml);

		//find first point
		$coord = $xpath->query('//*/@point[1]')->item(0)->nodeValue;
		$coordVal = explode(" ", $coord);

		//adds it to main transform
		$position = $xpath->query('//*/@translation')->item(0);
		$position->nodeValue = (-1 * (int)$coordVal[0]) . " " .(-1 * (int)$coordVal[1]). " " .(-1 * (int)$coordVal[2] - 150) ;
	}

	//---------------------------------------------------------------------------------------------

	public function addVisualization(VisualizationResult $visualization)
	{
		$xpathMain = new DOMXPath($this->xml);
		$xpathObjects = new DOMXPath($visualization->getXML());

		// each object is added to the main transform tag
		$mainTransform = $xpathMain->query('//transform[1]|//Transform[1]')->item(0);

		//import all visualization objects
		$listObject = $xpathObjects->query('//visualization/*');
		foreach ($listObject as $visuObject)
		{
			$node = $this->xml->importNode($visuObject, true);
			$mainTransform->appendChild($node);
		}

		/*
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		*/

		return $this->xml->saveXML();
	}

}