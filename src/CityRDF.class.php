<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 8/12/2014
* last update : 8/12/2014
*
* Generates a city file in XML that can be uploaded to a triple store (from a GML file)
*/

require_once('../config/constantsPath.php');

class CityRDF {

	const FILE_CONTEXT = "http://city.file/";

	const STAT_NAME = "status";
	const STAT_PROPAGATE = "propagate";
	const STAT_AVERAGE = "average";

	const RDF_NODE = "rdf:RDF";
	const RDF_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	const GML_URI = "http://www.opengis.net/gml";

	const GEOADDED_NAME = "protogeometry";
	const GEOADDED_URI = "http://unige.ch/masterThesis/";
	
	const GEOADDED_CENTER = "center";
	const GEOADDED_HIGHEST = "highest";
	const GEOADDED_LOWEST = "lowest";
	const GEOADDED_LOC = "location";

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
		//if complete < 100, then remove % of the file
		if ($this->completeUpload < 100){
			$this->removePercent();
		}		

		//do some extra calculations here, to simplify later
		$this->calculateCenters();

		//save file in temp folder
		$this->getFile();
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
   		$newRoot = $this->xml->createElementNS(CityRDF::RDF_URI, CityRDF::RDF_NODE);
   		//$newRoot = $this->xml->createElement(CityRDF::RDF_NODE);
   		$this->xml->appendChild($newRoot);
   		$newRoot->appendChild($root);

   		//$newRoot->setAttribute('xmlns:rdf','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
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
		    	$posList = $this->xml->createElementNS("http://www.opengis.net/gml", "gml:posList");
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
		/*
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		*/
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

	//------------------------------------------------------------------------------

	private function calculateCenters()
	{
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("gml", CityRDF::GML_URI);

		// 1) add compute centers for each linear ring by averaging all of the midpoints of each ring's verticles
		$posListList = $xpath->query("//gml:posList | //gml:poslist | //gml:PosList"); 
		//$posListList = $xpath->query("//*[local-name()='posList'] | //*[local-name()='poslist'] | //*[local-name()='PosList']");  //without namespace?		

		//echo $posListList->length . "<br>";

		foreach ($posListList as $posList) {
			$posList->nodeValue = preg_replace ('/\r\n|\r|\n/', " ", $posList->nodeValue); //remove breaks!
		  	$arrayValue = explode(" ", $posList->nodeValue);

		  	//echo $posList->nodeValue . "<br>";
		  	//echo sizeof($arrayValue). "---<br>";
		  	//print_r($arrayValue);

			//storing corner values
			$corners = ["highest" => ["x" => null, "y" => null, "z" => null], "lowest" = > ["x" => null, "y" => null, "z" => null]];

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

				testHighestLowest($corners, $arrayValue[$i], "x");
				testHighestLowest($corners, $arrayValue[$i+1], "y");
				testHighestLowest($corners, $arrayValue[$i+2], "z");
				testHighestLowest($corners, $arrayValue[$i+3], "x");
				testHighestLowest($corners, $arrayValue[$i+4], "y");
				testHighestLowest($corners, $arrayValue[$i+5], "z");
		  	}

		  	// AVERAGE THE MIDPOINT
		  	$center = ["x" => array_sum($midpointsX)/count($midpointsX), "y" => array_sum($midpointsY)/count($midpointsY), "z" => array_sum($midpointsZ)/count($midpointsZ)];
		  	//echo "Center at : " . $center["x"] . ", " . $center["y"] . ", " . $center["z"];
		  	//echo "<br>";
		  	//echo "<br>";

		  	// CREATE NODES HERE FOR CENTER //('xmlns:protogeometry', 'http://unige.ch/masterThesis/')
		  	$centerNode = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_CENTER);
		  	$locationNode = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_LOC);
		  	$x = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":x", $center["x"]);
		  	$y = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":y", $center["y"]);
		  	$z = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":z", $center["z"]);

		  	//DO THE SAME FOR THE CORNERS!!

		    $locationNode->appendChild($x);
		    $locationNode->appendChild($y);
		    $locationNode->appendChild($z);
		    $centerNode->appendChild($locationNode);

			$centerNode->setAttribute(CityRDF::STAT_NAME, CityRDF::STAT_PROPAGATE); //for step 2

		    $posList->parentNode->appendChild($centerNode);
			//echo $node->childNodes->length . " ";
		}

		// 2) propagate centers and average them for each id
		//$this->propagateCenters();
		
		/*
		echo "<pre>";
		echo $this->xml->saveXML();
		echo "</pre>";
		*/
	}

	//this function propagates the center nodes to all their parents with ids - for id with several centers, it creates an average of its child centers - only average centers are propagated
	private function propagateCenters()
	{
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace(CityRDF::GEOADDED_NAME, CityRDF::GEOADDED_URI);

		$done = false;

		while (!$done)
		{
			// Step A
			// for each node with status=propagate (nodes of type center)
			$nodesToPropag = $xpath->query("//*[@".CityRDF::STAT_NAME."='".CityRDF::STAT_PROPAGATE."']"); 
			foreach ($nodesToPropag as $node)
			{
				$parent = $node->parentNode->parentNode; //starts 2 levels higher to current id

				// loop through parents while doesn't find id 
				while ($parent != null){
					if ($parent->attributes != null){
						foreach ($parent->attributes as $attr){
							//echo $attr->nodeName. "<br>";
							if ($attr->nodeName == "gml:id"){
								//echo "OHSHITITSABOUTTOGODOWN";
								break 2;
							}
								
						}
					}

					$parent = $parent->parentNode;
				}

				// remove status (if loop reached end, else will add a new one in the copy)
				$node->removeAttribute(CityRDF::STAT_NAME);
				
				// if id
				if ($parent != null) {
					$copy = $node->cloneNode(true);
					$parent->appendChild($copy);
					
					$parent->setAttribute(CityRDF::STAT_NAME, CityRDF::STAT_AVERAGE);  // adds status=average to PARENT!
				}				
			}

			// Step B
			// for each node with status=average (nodes of any type, with an id)
			$nodesToAverage = $xpath->query("//*[@".CityRDF::STAT_NAME."='".CityRDF::STAT_AVERAGE."']"); 
			//echo "node to aver total : " . $nodesToAverage->length . "<br>";

			foreach ($nodesToAverage as $node)
			{
				//$childCenters = $xpath->query("./".CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_CENTER, $node); //relative query
				$childCenters = $xpath->query("./".CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_CENTER."/*", $node); //relative query
				//echo "child per capita : " . $childCenters->length . "<br>";

				//compute average
				$sumX = 0;
				$sumY = 0;
				$sumZ = 0;
				foreach ($childCenters as $child) {
					//echo $child->nodeName . "<br>";
					foreach ($child->childNodes as $dimensions){
						//echo $dimensions->nodeName . "<br>";
						if ($dimensions->nodeName == CityRDF::GEOADDED_NAME.":x")
							$sumX += $dimensions->nodeValue;
						else if ($dimensions->nodeName == CityRDF::GEOADDED_NAME.":y")	
							$sumY += $dimensions->nodeValue;	
						else if ($dimensions->nodeName == CityRDF::GEOADDED_NAME.":z")	
							$sumZ += $dimensions->nodeValue;	
					}
				}
				$sumX /= $childCenters->length;
				$sumY /= $childCenters->length;
				$sumZ /= $childCenters->length;
				//echo $sumZ."<br>";

				//add average center node
				$average = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_CENTER);
				$locationNode = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_LOC);
			  	$x = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":x", $sumX);
			  	$y = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":y", $sumY);
			  	$z = $this->xml->createElementNS(CityRDF::GEOADDED_URI, CityRDF::GEOADDED_NAME.":z", $sumZ);
			    //$average->appendChild($x);
			    //$average->appendChild($y);
			    //$average->appendChild($z);
			    $locationNode->appendChild($x);
			    $locationNode->appendChild($y);
			    $locationNode->appendChild($z);
			    $average->appendChild($locationNode);

				//remove all geometric information in double
				$geoInfo = $xpath->query("./".CityRDF::GEOADDED_NAME.":".CityRDF::GEOADDED_CENTER, $node); 
				foreach ($geoInfo as $info) {
					$node->removeChild($info);
				}

				//add averaged center with status propagate
				$average->setAttribute(CityRDF::STAT_NAME, CityRDF::STAT_PROPAGATE);
			    $node->appendChild($average);
				
				//remove status=average
				$node->removeAttribute(CityRDF::STAT_NAME);
			}

			// if no more node with status=propagate (number infered from number of parents with average to do)
			if ($nodesToAverage->length < 1)
				$done = true;
		}
	}

	private function testHighestLowest(&$corners, $value, $axis)
	{
		if ($axis != "x" || $axis != "y" || $axis != "z")
			throw new Exception ("Axis wasn't specified when saving corner dimensions (values 'x', 'y' or 'z')");

		if ($value > $corners["highest"][$axis] || $corners["highest"][$axis] == null)
			$corners["highest"][$axis] = $value;

		if ($value < $corners["lowest"][$axis] || $corners["lowest"][$axis] == null)
			$corners["lowest"][$axis] = $value;
	}

	//------------------------------------------------------------------------------

	public function getXML()
	{
		if ($this->xml != null)
				return $this->xml->saveXML(); //saveXML in a string, save is for saving the 
		else {
			//echo "aa";
			throw new Exception ("Parsing the XML/GML file failed.");
		}
	}

	public function getFile()
	{
		if ($this->xml != null) {
				$this->filePath = PATH_TEMPFILES . "city.xml";
				$this->xml->save($this->filePath);
				return $this->filePath;
		}	
		else 
			throw new Exception ("Saving the file failed.");
	}

	//------------------------------------------------------------------------------

	//from a list of contexts, return the data contexts only
	// in optional parameter, return the same list without prefixes
	public static function getListDataContexts($listContext, &$listContextHumanReadable = null)
	{
		foreach ($listContext as $key => $uri) {
			$uriPart = explode("/", $uri, 4);
			$type = "http://" . $uriPart[2] . "/";

			if ($type === CityRDF::FILE_CONTEXT)
				unset($listContext[$key]);
			else
				$listContextHumanReadable[] = $uriPart[3];
		}
		$listContext = array_values($listContext);

		return $listContext;
	}

}
