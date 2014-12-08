<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 8/12/2014
* last update : 8/12/2014
*
* Generates a city file in XML that can be uploaded to a triple store (from a GML file)
*/

class CityRDF {

	private $fileName;
	private $completeUpload;
	private $removeTexture;
	private $xml;

	function __construct($fileName, $completeUpload = 100, $removeTexture = true) 
	{
		$this->fileName = $fileName;
		$this->completeUpload = $completeUpload;
		$this->removeTexture = $removeTexture;

		//loads the file in a DOM object
		generateXML();

		if ($this->removeTexture)
			removeTextures();
	}

	private function generateXML()
	{

	}

	private function removeTextures()
	{

	}

	public function getXML()
	{
		if ($this->xml != null)
				return $this->xml;
		else 
			throw new Exception ("Parsing the XML/GML file failed.");
	}

}
