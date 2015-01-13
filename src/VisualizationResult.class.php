<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 13/1/2015
* last update : 13/1/2015
*
* queries the repository with a CONSTRUCT query (got from TechniqueQuery object) -> gets abstract objects
* applies layout managers -> gets concrete generic objects
* translates output into a defined language -> gets concrete objects
* 
*/

require_once('SesameInterface.class.php');


class VisualizationResult 
{
	const RDF_URI = "<http://www.w3.org/1999/02/22-rdf-syntax-ns#>";
	const XSL_URI = "<http://www.w3.org/1999/XSL/Transform>";

	const LANG_X3D = "X3D";

	const PATH_TEMPLATES = "../sys/LayoutManagers/";
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
		$xslt = new XSLTProcessor();
		$xslSheet = new DOMDocument();

		//generic style for the document
		$xslSheet->load(VisualizationResult::PATH_TEMPLATES . "_/" . "overall" . VisualizationResult::EXTENSION);

		$xpath = new DOMXPath($xslSheet);
		$xpath->registerNamespace("xsl",VisualizationResult::XSL_URI);

		//for each layout manager, adds the appropriate call in xslt
		foreach ($arrayLayout as $nameLayout) 
		{
			$include = $xslSheet->createElementNS(VisualizationResult::XSL_URI,"xsl:include");
			$include->setAttribute("href", "../" . $nameLayout . VisualizationResult::EXTENSION);

			$xpath->query('//xsl:stylesheet')->item(0)->appendChild($include);
		}

		//apply style sheet
		$xslt->importStylesheet($xslSheet);
		$this->xml = $xslt->transformToDoc($this->xml);

		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
	}

	public function translateLanguage($languageName)
	{
		if ($languageName == VisualizationResult::LANG_X3D)
		{

		}
		//other languages here
		else
		{
			throw new Exception("Can't translate output into '$languageName' language.");
		}
	}

}
