<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 10/11/2014
*
* calls everything
*/

require_once('SesameInterface.class.php');

//echo "a";

$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame', "INTERFACETEST");

//$sesame->createRepository("INTERFACETEST");

//echo $sesame->existsRepository("FRANKFURT");

//$sesame->appendFile("Munich_clean2.xml");


//exemple of a SELECT query
/*
$a = '
PREFIX :<http://www.opengis.net/citygml/1.0>
PREFIX app:<http://www.opengis.net/citygml/appearance/1.0>
PREFIX ex:<http://example.org/stuff/1.0/>
PREFIX xlink:<http://www.w3.org/1999/xlink>
PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX gml:<http://www.opengis.net/gml>
PREFIX dem:<http://www.opengis.net/citygml/relief/1.0>
PREFIX bldg:<http://www.opengis.net/citygml/building/1.0>
PREFIX xsi:<http://www.w3.org/2001/XMLSchema-instance>

SELECT ?truc ?type
WHERE {
 ?truc rdf:type ?type.
}
';
*/
//$query = $sesame->query($a, array('Accept: ' . SesameInterface::SPARQL_XML));



//exemple of a CONSTRUCT query
/*
$b = 'PREFIX test:<http://aa.com/>
CONSTRUCT {
 _:1 a test:Truc.
 _:1 test:a ?truc.
 _:1 test:b ?a.
 _:1 test:c ?machin.
}
FROM <file://fakepath/Munich_clean2.xml>
WHERE {
 ?truc ?a ?machin.
}';
*/
//$query = $sesame->query($b, array('Accept: ' . SesameInterface::RDFXML));


//echo '<pre>';
//echo htmlspecialchars($query);
//echo '</pre>';

//$xmlDoc = new DOMDocument();
//$xmlDoc->loadXML($query);
//var_dump($xmlDoc);


//exemple of INSERT query
$c = '
PREFIX data:<http://test.com/>
INSERT DATA
	{
	  GRAPH <http://graphData>
	  { 
		data:x data:tag "three" . 
		data:y data:tag "four" . 
	  }
	}
';

$query = $sesame->append($c, 'null', '')
//$query = $sesame->query($c, array());
//$query = $sesame->query($c, array('Accept: ' . SesameInterface::RDFXML));

?>