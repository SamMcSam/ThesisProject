<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 12/1/2015
* last update : 12/1/2015
*
* calls different methods necessary to get rdf from technique, apply layout managers and add it to 3d model
*/

require_once('../config/constantsPath.php');

require_once('SesameInterface.class.php');
require_once('DataInsert.class.php');
require_once('TechniqueQuery.class.php');
require_once('VisualizationResult.class.php');
require_once('ModelResult.class.php');

$output = "";

try 
{
	// Technique
	//----------------------------

	// Load repository list
	$sesame = new SesameInterface(URL_SESAME);
	$listRepo = $sesame->getListRepositories();

	//load data type list
	$jsonString = file_get_contents(PATH_DATATYPES);
	$listTypes = json_decode($jsonString, true);

	//set repository
	$repoName = "Munich_v_1_0_0.xml"; //TEST
	if (!$sesame->setRepository($repoName))
		throw new Exception("Repository not found.");

	//open technique
	//$name = "SphereWithValueAsRadius_default";
	$name = "SphereWithValueAsRadius";
	$technique = new TechniqueQuery($name);

	$technique->setModelGraph("<http://city.file/Munich_v_1_0_0.xml>");
	$technique->setDataGraph("<http://data.graph/2015-01-15_12-16-59/Munich_DataProto_type1a.txt>");

	//$technique->getParameterNames();
	$technique->loadParameterValues(["color" => "'1 0 0'"]);

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
	echo "Error : " . $e->getMessage();
}

?>

<html>
	<head> 
		<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1' /> 
		<title>My 3D enriched model</title>
		<link rel='stylesheet' type='text/css' href='<?php echo PATH_3DSYS;?>x3dom.css'></link>
		<script type='text/javascript' src='<?php echo PATH_3DSYS;?>x3dom.js'></script>
	</head>
	<body>
		<x3d width='600px' height='400px'>
			<?php echo $output; ?>
		</x3d>
	</body>
</html>