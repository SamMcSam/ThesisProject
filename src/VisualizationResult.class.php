<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 13/1/2015
* last update : 14/1/2015
*
* queries the repository with a CONSTRUCT query (got from TechniqueQuery object) -> gets abstract objects
* applies layout managers -> gets concrete generic objects
* translates output into a defined language -> gets concrete objects
* 
*/

require_once('SesameInterface.class.php');


class VisualizationResult 
{
	const RDF_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	const XSL_URI = "http://www.w3.org/1999/XSL/Transform";

	const LANG_X3D = "X3D";

	const GENERIC_FILE = "_/overall";
	const EXTENSION = ".xsl";

	private $reponse;
	private $xml;

	//---------------------------------------------------------------------------------------------

	function __construct(SesameInterface $sesame, $query)
	{
		$this->reponse = $sesame->query($query , 'Accept: ' . SesameInterface::RDFXML);
		$this->cleanResult();
	}

	//Kludge method for self-including blank nodes in rdf/xml sesame output
	private function cleanResult()
	{
		$this->xml = new DOMDocument();
		$this->xml->loadXML($this->reponse);

		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("rdf", VisualizationResult::RDF_URI);

		do 
		{
			$done = true;

			$listDescriptions = $xpath->query('//rdf:Description | //rdf:description');

			// parcours en arrière
			for ($i = $listDescriptions->length - 1 ; $i >= 0 ; $i--)
			{	
				$node = $listDescriptions->item($i);

				//the id of the current node
				$id = $xpath->query('@rdf:nodeID | @rdf:nodeid', $node)->item(0)->nodeValue;
				//echo $id . "<br>";

				//find where this node was referenced
				$foundRef = false;
				$listReferences = $xpath->query("//*[@rdf:nodeID='".$id."'] | //*[@rdf:nodeid='".$id."']");
				foreach ($listReferences as $reference){
					//if not the same won't do a thing (obvs)
					if (!$reference->isSameNode($node))
					{
						//copy all child nodes inside reference
						foreach ($node->childNodes as $child)
				        {
				            $reference->appendChild($child->cloneNode(true));
				        }

				        //adds simpler way to find the type of layout
				        $resourceLayout = $xpath->query("./rdf:type/@rdf:resource", $reference)->item(0)->nodeValue;
						$arrayResource = explode("/", $resourceLayout);
						$nameLayout = $arrayResource[count($arrayResource)-1];
						$reference->setAttribute("typeLayout", $nameLayout);

						$foundRef = true;
					}
				}

				//remove $node if has been found
				if ($foundRef){
					$node->parentNode->removeChild($node);
					$done = false; //will do another loop to verify if still things to do
				}
			}

		}
		while (!$done);

		//echo $this->xml->saveXML();
	}

	//---------------------------------------------------------------------------------------------

	//Transforms abstract objects into concrete (generic) objects, with the help of layout managers
	public function appliesLayouts($arrayLayout)
	{
		$xslSheet = new DOMDocument();

		//generic style for the document
		$xslSheet->load(PATH_LAYOUTMANAGERS . VisualizationResult::GENERIC_FILE . VisualizationResult::EXTENSION);
		//$xslSheet->load(PATH_LAYOUTMANAGERS .  "overall" . VisualizationResult::EXTENSION);

		$xpath = new DOMXPath($xslSheet);
		$xpath->registerNamespace("xsl", VisualizationResult::XSL_URI);

		//for each layout manager, adds the appropriate call in xslt
		foreach ($arrayLayout as $nameLayout) 
		{
			$include = $xslSheet->createElementNS(VisualizationResult::XSL_URI, "xsl:include");
			$include->setAttribute("href", "../" . $nameLayout . VisualizationResult::EXTENSION);
			//$include->setAttribute("href", $nameLayout . VisualizationResult::EXTENSION);

			$xpath->query('//xsl:stylesheet')->item(0)->appendChild($include);
		}
		//apply style sheet
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xslSheet);
		$this->xml = $xslt->transformToDoc($this->xml);


		/*
		echo "<pre>";
		//echo $res;
		//echo $xslSheet->saveXML();
		echo $this->xml->saveXML();
		echo "</pre>";
		*/

	}


	// A refaire!! (pour accepter autres languages)
	public function translateLanguage($languageName)
	{
		//NO SUPPORT FOR OTHER LANGUAGES YET!!!
		if ($languageName != VisualizationResult::LANG_X3D)
			throw new Exception("Can't translate output into '$languageName' language.");

		//---------------------------------------

		$xslSheet = new DOMDocument();

		//generic file (same as previous function)
		$xslSheet->load(PATH_TRANSLATORS . VisualizationResult::LANG_X3D . "/" . VisualizationResult::GENERIC_FILE . VisualizationResult::EXTENSION);

		$xpath = new DOMXPath($xslSheet);
		$xpath->registerNamespace("xsl", VisualizationResult::XSL_URI);

		//here, not a selective input, but add ALL files
		//for all files in the translator language
		foreach (glob(PATH_TRANSLATORS . VisualizationResult::LANG_X3D . "/*.*") as $nameTranslator) 
		{
			$arrayResource = explode("/", $nameTranslator);
			$nameTranslator = $arrayResource[count($arrayResource)-1];

			//echo $nameTranslator . " <br>";
			
			$include = $xslSheet->createElementNS(VisualizationResult::XSL_URI, "xsl:include");
			$include->setAttribute("href", "../" . $nameTranslator);
			//$include->setAttribute("href", $nameLayout . VisualizationResult::EXTENSION);

			$xpath->query('//xsl:stylesheet')->item(0)->appendChild($include);
			
		}
		
		//apply style sheet
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xslSheet);
		$this->xml = $xslt->transformToDoc($this->xml);

		/*
		echo "<pre>";
		//echo $res;
		//echo $xslSheet->saveXML();
		echo $this->xml->saveXML();
		echo "</pre>";
		*/
	}

}
