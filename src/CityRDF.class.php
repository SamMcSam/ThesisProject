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
	private $filePath;

	function __construct($fileName, $completeUpload = 100, $removeTexture = true) 
	{
		$this->fileName = $fileName;
		$this->completeUpload = $completeUpload;
		$this->removeTexture = $removeTexture;
		$this->xml = null;
		$this->filePath = null;

		//loads the file in a DOM object
		$this->generateXML();

		//cleans it and makes it valid for a transfer in a triple store
		$this->cleaning();

		if ($this->removeTexture)
			removeTextures();
	}

	private function generateXML()
	{
		try {
			$this->xml = new DOMDocument($this->fileName);
			$this->xml->load($this->fileName); //LOAD para filename, LOADXML para string :/

			//echo "<pre>";
			//print_r($this->xml);
			//echo "</pre>";
		}
		catch (E_STRICT  $e) {
			exit("Erreur : document impossible to parse!");
		}
	}

	private function cleaning()
	{
		//add prefix

		//verify if no unprefixed tags
		//(surtout uri)

		//transform multiple pos INTO posList

		//extra
		//---

		//enlever bounded by
		//enlever address
		//enlever exteernalReference
	}

	private function removeTextures()
	{
		//this is what the cleaning.py script did
		//remove all <app:appearance>
	}

	public function getXML()
	{
		if ($this->xml != null)
				return $this->xml;
		else 
			throw new Exception ("Parsing the XML/GML file failed.");
	}

	public function getFile()
	{
		if ($this->xml != null) {
				$this->filePath = "city.xml";
				$this->xml->save("city.xml");
				return $this->filePath;
		}	
		else 
			throw new Exception ("Saving the file failed.");
	}

}
