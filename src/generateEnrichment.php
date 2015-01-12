<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 12/1/2015
* last update : 12/1/2015
*
* calls different methods necessary to get rdf from technique, apply layout managers and add it to 3d model
*/

require_once('SesameInterface.class.php');
require_once('DataInsert.class.php');
require_once('TechniqueQuery.class.php');

try 
{

	// Load repository list
	$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');
	$listRepo = $sesame->getListRepositories();

	//load data type list
	$jsonString = file_get_contents("../config/dataTypes.json");
	$listTypes = json_decode($jsonString, true);

	//set repository
	$repoName = "Munich_v_1_0_0.xml"; //TEST
	if (!$sesame->setRepository($repoName))
		throw new Exception("Repository not found.");

	//open technique
	//$name = "SphereWithValueAsRadius_default";
	$name = "SphereWithValueAsRadius";
	$technique = new TechniqueQuery($name);
	$technique->setModelGraph("<http://data.graph/2015-01-08_10-31-31/Munich_DataProto_type1a.txt>");
	$technique->setDataGraph("<http://city.file/Munich_v_1_0_0.xml>");

	//$technique->getParameterNames();
	//$technique->loadParameterValues(["color" => "'2 2 2'"]);

	//$technique->generateQuery();
	$technique->getQuery();



}catch (Exception $e)
{
	echo "Error : " . $e->getMessage();
}


?>