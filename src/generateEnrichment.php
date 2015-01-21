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
	$parameterList = $_POST["parameters"];

	// Technique
	//----------------------------

	$sesame = new SesameInterface(URL_SESAME);

	//set repository
	if (!$sesame->setRepository($repoName))
		throw new Exception("Repository not found.");

	//open technique
	$technique = new TechniqueQuery($techName);

	$technique->setModelGraph("<". CityRDF::FILE_CONTEXT . $repoName .">");
	$technique->setDataGraph("<". $dataName .">");

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

?>

<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr' lang='fr'>
	<head> 
		<meta charset='UTF-8'>
		<title>My 3D enriched model</title>
		<script type='text/javascript' src='<?php echo PATH_3DSYS;?>x3dom.js'></script>
		<link rel='stylesheet' type='text/css' href='<?php echo PATH_3DSYS;?>x3dom.css'></link>
		<link rel="stylesheet" href="style.css">
	</head>

	<body>
		<div id='mainBlock'>
			<?php 
				if (empty($errorMessage))
					echo "<p style='height:400px'><x3d width='600px' height='400px'>" . $output . "</x3d></p>";
			?>

			<p>
				<?php echo $errorMessage; ?>
			</p>

			<!--<p>
				<a href='javascript:history.back()'>Go back</a>
			</p>-->	
		</div>

	</body>
</html>