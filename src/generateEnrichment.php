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

include_once('cleanSesameResults.php');

try 
{
	// Technique
	//----------------------------

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
	$technique->loadParameterValues(["color" => "'2 2 2'"]);

	$layoutNames = $technique->getLayoutNames();

	$query = $technique->getQuery();
	/*$query = 'PREFIX gml:<http://www.opengis.net/gml>
		PREFIX data:<http://master.thesis/project/data/>
		PREFIX vizu:<http://unige.ch/masterThesis/>
		PREFIX layout:<http://unige.ch/masterThesis/layoutmanagers/>

		CONSTRUCT {
		 $x a "truc".
		}
		FROM <http://data.graph/2015-01-08_10-31-31/Munich_DataProto_type1a.txt>
		FROM <http://city.file/Munich_v_1_0_0.xml>
		WHERE {
		?x a ?y.
		}';
	*/


	// Runs CONSTRUCT
	//----------------------------
	
	$reponse = $sesame->query($query , 'Accept: ' . SesameInterface::RDFXML);
	//echo "$reponse";

	// cleans the result in a more compact and efficient result

	// Apply Layout managers
	//----------------------------


}catch (Exception $e)
{
	echo "Error : " . $e->getMessage();
}


?>