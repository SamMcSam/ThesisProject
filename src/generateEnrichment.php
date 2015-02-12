<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 12/1/2015
* last update : 12/2/2015
*
* calls different methods necessary to get rdf from technique, apply layout managers and add it to 3d model
*
* if action is display, calls displayEnrich after
* if action is save, calls downloadEnrich after
*/

require_once('../config/constantsPath.php');

require_once('SesameInterface.class.php');
require_once('CityRDF.class.php');
require_once('DataInsert.class.php');
require_once('TechniqueQuery.class.php');
require_once('VisualizationResult.class.php');
require_once('ModelResult.class.php');

$output = "";
$errorMessage = "";

try 
{
	if (!isset($_POST["enrichment_repoName"]) || !isset($_POST["enrichment_dataName"]) || !isset($_POST["enrichment_techName"]))
		throw new Exception("Couldn't retrieve information from request.");

	$repoName = htmlspecialchars($_POST["enrichment_repoName"]);
	$dataName = htmlspecialchars($_POST["enrichment_dataName"]);
	$techName = htmlspecialchars($_POST["enrichment_techName"]);

	//parameters
	if (isset($_POST["parameters"])) 
		$parameterList = $_POST["parameters"];
	else
		$parameterList = array();

	// Technique
	//----------------------------

	$sesame = new SesameInterface(URL_SESAME);

	//set repository
	if (!$sesame->setRepository($repoName))
		throw new Exception("Repository not found.");

	//open technique
	$technique = new TechniqueQuery($techName);

	//$technique->setModelGraph("<". CityRDF::FILE_CONTEXT . $repoName .">"); //OLD, without chunks
	$listData = $sesame->getListContexts();
	$listData = CityRDF::getListCityContexts($listData);
	$technique->setModelGraph($listData);

	$technique->setDataGraph($dataName);

	//$technique->getParameterNames();
	$technique->loadParameterValues($parameterList);

	$layoutNames = $technique->getLayoutNames();

	$query = $technique->getQuery();

	// Runs CONSTRUCT
	//----------------------------
	
	//gets constructed graph
	//$reponse = $sesame->query($query , 'Accept: ' . SesameInterface::RDFXML);
	//echo $reponse;
	$visualization = new VisualizationResult($sesame, $query);

	//applies layout managers
	$visualization->appliesLayouts($layoutNames);

	//transforms in X3D
	$languageOutput = "X3D";
	$visualization->translateLanguage($languageOutput);

	// Enriched Model
	//----------------------------

	//creates X3D model
	$model = new ModelResult($sesame, $repoName, false);

	//adds visualization objects to X3D
	// output contains enriched model
	$output = $model->addVisualization($visualization);

}catch (Exception $e)
{
	$errorMessage = "ERROR : " . $e->getMessage();
}


//Either displays in in HTML loader
//Or save as
if (isset($_POST["display_button"]))
	include("displayEnrichment.php");
else if (isset($_POST["save_button"]))
	include("downloadEnrichment.php");

?>