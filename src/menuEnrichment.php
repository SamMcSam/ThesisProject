<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 6/12/2014
*
* Menu to  generate a enriched 3DCM
*/



//require_once('SesameInterface.class.php');

//if isset($_POST["uploadcity_name"]);
//if isset($_POST["uploadcity_isCleaned"]);




//$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame', "INTERFACETEST");


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




/*
$msg = "";

//if ?
	$msg = "<div class='error'>Data couldn't be uploaded. </div>";
//else {
	$msg = "<div class='confirmed'>The data '' has been uploaded to the '' repository!</div>";
//}
*/
?>

<fieldset> <legend>Créer la visualisation des données dans le 3DCM</legend> 
	<form method='post' action='_______.php' >
		Utiliser la ville : <select class='champs' name='grapheCity'>
			<option>City 1</option>
			<option>City 2</option>
		</select>
		Utiliser les données : <select class='champs' name='grapheData'>
			<option>Data 1</option>
		</select>
		
		<br>
		... PLEIN de choses sur la technique 
		<br><br>
		
		<button class='champs' type='button' onclick="loadEnrichment(false);">Générer 3DCM</button>
	</form>
</fieldset>