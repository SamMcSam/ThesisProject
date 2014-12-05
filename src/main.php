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

$a = "
PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
SELECT ?truc ?a ?machin
WHERE {
 ?truc ?a ?machin.
}
";

/*$a = 'PREFIX test:<http://aa.com/>

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

$a = htmlspecialchars($a);
//$a = urlencode($a);
*/
echo $a;

$query = $sesame->query($a);


echo '<pre>';
echo htmlspecialchars($query);
echo '</pre>';

/*
$bdd->query(
	"PREFIX dm: <http://learningsparql.com/ns/demo#>

	SELECT ?x ?y
	WHERE
	{
	  ?x dm:tag ?y
	}"
);
*/
/*
$bdd->query(
	'INSERT DATA
	{
	  d:x dm:tag "one" . 
	  d:x dm:tag "two" . 

	  GRAPH d:g3
	  { 
		d:x dm:tag "three" . 
		d:x dm:tag "four" . 
	  }
	}'
);
*/

?>