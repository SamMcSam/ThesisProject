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

	const TEMP_PATH = "../tmpFiles/";
	const FILE_CONTEXT = "http://city.file/";

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

		//remove appearances tags
		if ($this->removeTexture){
			$this->removeTextures();
		}

		//remove part of the object tags (in percent)
		if ($this->completeUpload < 100){
			$this->removePercent();
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
			$this->xml->preserveWhiteSpace = false;
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
   		$newRoot->setAttribute('xmlns:protogeometry', 'http://unige.ch/masterThesis/'); //for adding attributes

		//transform multiple pos into a single posList (old gml doc)
		//----------
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("gml", "http://www.opengis.net/gml");

		$linearRingList = $xpath->query('//gml:LinearRing');
		foreach ($linearRingList as $ring) {
		    $listOfPos = $xpath->query('gml:pos', $ring);
		    if ($listOfPos->length > 0){
		    	$posList = $this->xml->createElementNS("http://www.opengis.net/gml", "gml:poslist");
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

		//verify if no unprefixed attributes
		//(not necessary for elements, only attributes need to be prefixed)
		//see commits until 53a427963f23710aae094094851d8bbb48d1c661 for prefixation of elements
		//----------
		$allAttributes = $xpath->query('//@*');
		foreach($allAttributes as $attributeNode) {
			if (!preg_match("#^.*:.*$#", $attributeNode->nodeName)){
				//echo $attributeNode->nodeName . "<br>"; 
				$newAttribute = $this->xml->createAttribute("failsafe:" . $attributeNode->nodeName);
				$newAttribute->value = $attributeNode->value;

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
			}
		}
		*/

		//remove white spaces
		$this->xml->normalize();

		//debug
		//echo "<pre>";
		//echo $this->xml->saveXML();
		//echo "</pre>";
	}

	private function removeTextures()
	{
		//remove all <app:appearance> and/or <app:Appearance> 
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("app", "http://www.opengis.net/citygml/appearance/1.0");
		$appearanceList = $xpath->query('//app:Appearance | //app:appearance');
		foreach ($appearanceList as $appearance) {
			$appearance->parentNode->removeChild($appearance);
		}
		/*
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		*/	
	}

	//TODO : add support of lowercase tag
	private function removePercent()
	{
		$xpath = new DOMXPath($this->xml);
		$cityObjectList = $xpath->query("//*[local-name() = 'cityObjectMember']"); 

		//remove last % given
		$nbrLeft = $cityObjectList->length / 100 * $this->completeUpload;
		for ($i = $cityObjectList->length - 1; $i >= $nbrLeft ; $i--) {
			$cityObjectList->item($i)->parentNode->removeChild($cityObjectList->item($i));
		}
	}

	//TODO : corners!!
	//for each node in the list
	//update lowest x
	//update lowest y
	//update lowest z
	//update highest x
	//update highest y
	//update highest z
	private function calculateCenters()
	{
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("gml", "http://www.opengis.net/gml");

		// 1) add compute centers for each linear ring by averaging all of the midpoints of each ring's verticles
		$posListList = $xpath->query("//gml:posList | //gml:poslist | //gml:PosList"); 
		//$posListList = $xpath->query("//*[local-name()='posList'] | //*[local-name()='poslist'] | //*[local-name()='PosList']");  //without namespace?		

		//echo $posListList->length . "<br>";

		foreach ($posListList as $posList) {
		  	$arrayValue = explode(" ", $posList->nodeValue);

		  	//compute midpoints
		  	$midpointsX = array();
		  	$midpointsY = array();
		  	$midpointsZ = array();
		  	for ($i=0 ; $i<sizeof($arrayValue)-4;$i+=3){ //(3 by 3 because values are in order x1 y1 z1 x2 y2 z2 etc.)
		  		 $midx = ($arrayValue[$i] + $arrayValue[$i+3]) / 2;
		  		 $midy = ($arrayValue[$i+1] + $arrayValue[$i+1+3]) / 2;
		  		 $midz = ($arrayValue[$i+2] + $arrayValue[$i+2+3]) / 2;

				$midpointsX[] = $midx;
				$midpointsY[] = $midy;
				$midpointsZ[] = $midz;
				//echo "Midpoint : " . $midx . ", " . $midy . ", " . $midz;
    			//echo "<br>";
		  	}

		  	// AVERAGE THE MIDPOINT
		  	$center = ["x" => array_sum($midpointsX)/count($midpointsX), "y" => array_sum($midpointsY)/count($midpointsY), "z" => array_sum($midpointsZ)/count($midpointsZ)];
		  	//echo "Center at : " . $center["x"] . ", " . $center["y"] . ", " . $center["z"];
		  	//echo "<br>";
		  	//echo "<br>";

		  	// CREATE NODES HERE FOR CENTER //('xmlns:protogeometry', 'http://unige.ch/masterThesis/')
		  	$centerNode = $this->xml->createElementNS("http://unige.ch/masterThesis/", "protogeometry:center");
		  	$x = $this->xml->createElementNS("http://unige.ch/masterThesis/", "protogeometry:x", $center["x"]);
		  	$y = $this->xml->createElementNS("http://unige.ch/masterThesis/", "protogeometry:y", $center["y"]);
		  	$z = $this->xml->createElementNS("http://unige.ch/masterThesis/", "protogeometry:z", $center["z"]);
	    	
		    $centerNode->appendChild($x);
		    $centerNode->appendChild($y);
		    $centerNode->appendChild($z);
		    $posList->parentNode->appendChild($centerNode);

			//echo $node->childNodes->length . " ";
		}

		// 2) propagate centers and average them for each id
		$this->propagateCenters();
		
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		
	}

	private function propagateCenters()
	{
		// WHILE not done
			// for each node with status=propagate
				// loop through parents while doesn't find id 
					// if id
						// remove status of center
						// copy center in id  
						// add status=average to copy
				// if loop reached root - remove status
			// for each node with status=average
				//compute average
				//remove all centers
				//add average center with status propagate
			// if no more node with status=propagate 
				//done
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
				$this->filePath = CityRDF::TEMP_PATH . "city.xml";
				$this->xml->save($this->filePath);
				return $this->filePath;
		}	
		else 
			throw new Exception ("Saving the file failed.");
	}

}
