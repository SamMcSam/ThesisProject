<?php
	/*
		Kludge method for self-including blank nodes in rdf/xml sesame output
		author : Samuel Constantino
	*/

	header('Content-type: text/xml');

	$RDF_NAME = "rdf:Description";
	$RDF_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";

	//$fileName = "simpleSphereOnPosition.rdf";
	//$fileName = "correctOutput.rdf";
	$fileName = "correctOutput2.rdf";
	$xml = new DOMDocument();
	$xml->load($fileName);

	$xpath = new DOMXPath($xml);
	$xpath->registerNamespace("rdf", $RDF_URI);

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

	echo $xml->saveXML();

?>