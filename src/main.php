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

$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');

//$sesame->createRepository("working");

$a = $sesame->existsRepository("SYSTEM");




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