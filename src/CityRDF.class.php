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

		if ($this->removeTexture){
			$this->removeTextures();
		}

		//if complete < 100, then remove % of the file

		//do some extra calculations here, to simplify later
		$this->calculateCenters();
	}

	private function generateXML()
	{
		try {
			$this->xml = new DOMDocument($this->fileName);
			$this->xml->load($this->fileName); //LOAD para filename, LOADXML para string :/
		}
		catch (E_STRICT  $e) {
			exit("Erreur : document impossible to parse!");
		}
	}

	private function cleaning()
	{
		//add prefix and a rdf:RDF node as the root
		//----------
		$root = $this->xml->documentElement;
   		$newRoot = $this->xml->createElement("rdf:RDF");
   		$this->xml->appendChild($newRoot);
   		$newRoot->appendChild($root);

   		$newRoot->setAttribute('xmlns:rdf','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
   		$newRoot->setAttribute('xmlns:failsafe','http://escape.nodes/without/namespaces#');
   		$newRoot->setAttribute('xmlns:core', 'http://www.opengis.net/citygml/1.0');

		//transform multiple pos INTO a single posList (old gml doc)
		//----------
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("gml", "http://www.opengis.net/gml");
		$linearRingList = $xpath->query('//gml:LinearRing');
		foreach ($linearRingList as $ring) {
		    $listOfPos = $xpath->query('gml:pos', $ring);
		    if ($listOfPos->length > 0){
		    	$posList = $this->xml->createElement("gml:poslist");
			    foreach ($listOfPos as $pos) {
		    		$posList->nodeValue .= $pos->nodeValue." ";
		    		$pos->parentNode->removeChild($pos);
			    }
			    $ring->appendChild($posList);
			}
		}		

		//enlever bldg:address
		//----------
		$xpath->registerNamespace("bldg", "http://www.opengis.net/citygml/building/1.0");
		$bldAddressList = $xpath->query('//bldg:address');
		foreach ($bldAddressList as $address) {
			$address->parentNode->removeChild($address);
		}

		//enlever core:externalReference
		//----------
		$xpath->registerNamespace("core", "http://www.opengis.net/citygml/1.0");
		$externalRefList = $xpath->query('//core:externalReference');
		foreach ($externalRefList as $ref) {
			$ref->parentNode->removeChild($ref);
		}		


		/*
		//verify if no unprefixed nodes (no easy way to change names!!)
		//----------
		$allElements = $xpath->query('//*');
		foreach($allElements as $node) {
			if (!preg_match("#^.*:.*$#", $node->nodeName)){
				$newNode = $this->xml->createElement("failsafe:" . $node->nodeName);

				//copy children
				$childList = $xpath->query('child::*', $node);
				foreach($childList as $child) {
					$newNode->appendChild($child);
				}

				//copy attributes
				foreach($node->attributes as $attr) {
					$newNode->setAttributeNodeNS($attr);
				}
				$node->parentNode->replaceChild ($newNode, $node);
				
				//echo $node->nodeName . "<br>";
			}
		}
		*/

		//verify if no unprefixed attributes (same)
		//----------
		$allAttributes = $xpath->query('//@*');
		foreach($allAttributes as $attributeNode) {
			if (!preg_match("#^.*:.*$#", $attributeNode->nodeName)){
				//echo $attributeNode->nodeName . "<br>"; 
				$newAttribute = $this->xml->createAttribute("failsafe:" . $attributeNode->nodeName);

				$attributeNode->ownerElement->setAttributeNode ($newAttribute);
				$attributeNode->ownerElement->removeAttributeNode($attributeNode);
			}
		}

		//TODO
		/*
		//remove all nodes without content & children
		//----------
		$allElements = $xpath->query('//*');
		foreach($allElements as $node) {
			//echo $node->nodeName . " <br>";
			if ($node->childNodes->length <= 0){
				$node->parentNode->removeChild($node);
				echo "a ";
			}
		}
		*/

		//debug
		//echo "<pre>";
		//echo $this->xml->saveXML();
		//echo "</pre>";
	}

	private function removeTextures()
	{
		//remove all <app:appearance>
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("app", "http://www.opengis.net/citygml/appearance/1.0");
		$appearanceList = $xpath->query('//app:Appearance');
		foreach ($appearanceList as $appearance) {
			$appearance->parentNode->removeChild($appearance);
		}
		//in case was written in lower
		$appearanceList = $xpath->query('//app:appearance');
		foreach ($appearanceList as $appearance) {
			$appearance->parentNode->removeChild($appearance);
		}

		/*
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		*/	
	}

	private function calculateCenters()
	{

	}

	//------------------------------------------------------------------------------

	public function getXML()
	{
		if ($this->xml != null)
				return $this->xml->saveXML(); //saveXML in a string, save is for saving the 
		else {
			echo "aa";
			throw new Exception ("Parsing the XML/GML file failed.");
		}
	}

	public function getFile()
	{
		if ($this->xml != null) {
				$this->filePath = "tempFiles/city.xml";
				$this->xml->save($this->filePath);
				return $this->filePath;
		}	
		else 
			throw new Exception ("Saving the file failed.");
	}

}
