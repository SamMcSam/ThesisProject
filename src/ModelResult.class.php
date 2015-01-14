<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 14/1/2015
* last update : 14/1/2015
*
* queries the repository with a CONSTRUCT query (got from TechniqueQuery object) -> gets abstract objects
* applies layout managers -> gets concrete generic objects
* translates output into a defined language -> gets concrete objects
* 
*/

require_once('SesameInterface.class.php');


class ModelResult 
{
	//const RDF_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	//const XSL_URI = "http://www.w3.org/1999/XSL/Transform";

	//const LANG_X3D = "X3D";

	//const GENERIC_FILE = "_/overall";
	//const EXTENSION = ".xsl";

	private $reponse;
	private $xml;

	//---------------------------------------------------------------------------------------------

	function __construct(SesameInterface $sesame, $query, $exportTextures=false)
	{
		$query = $this->generateQuery();
		$this->reponse = $sesame->query($query , 'Accept: ' . SesameInterface::RDFXML);
		$this->cleanResult();
	}

	//Kludge method for self-including blank nodes in rdf/xml sesame output
	private function generateQuery()
	{

	}