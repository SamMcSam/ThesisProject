<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 10/11/2014
*
* Menu to upload 3d city
*/

ini_set('display_errors', 1);


require_once('CityRDF.class.php');
require_once('SesameInterface.class.php');

$msg = "";

if (isset($_POST["ok"]))
	echo $_POST['ok'];
if (isset($_GET["WHAT"]))
	echo $_GET['WHAT'];


if (isset($_FILES["uploadcity_file"])) {
	try 
	{
		$nameFile = $_FILES["uploadcity_file"]["name"];
		$typeFile = $_FILES["uploadcity_file"]["type"];

		echo $nameFile;

		$completeUpload = 20;//100; //$_POST["complete_upload"];
		$removeTexture = true; //$_POST["remove_texture"]["removed"];

		// GENERATE city RDFable
		//$city = new CityRDF($nameFile, $completeUpload, $removeTexture);

		// create repository
		//$nameRepo;
		//if ($sesame->existsRepository($nameRepo)) {
			//$sesame->createRepository($nameRepo);

			// upload city model as a graph
			//$sesame->appendFile($city->getXML());

			//do some extra tuning?
		//}
		//else
			//throw new Exception("A repository for this file already exists.");
		
		$msg = "<div class='confirmed'>A repository for the 3D model '' has been created!</div>";
	}
	catch (Exception $e){
		$msg = "<div class='error'>". $e->getMessage() ."</div>";
	}
}

?>

<div class='titre'>City graph</div>
<fieldset> <legend>Créer graphe depuis un fichier citygml</legend> 
	<form>
		<input id='uploadcity_file' type='file' name='uploadcity_file' />
		<input id='complete_upload' type='hidden' name='complete_upload' value='100'><!--Completeness percentage -->
		<input id='remove_texture' type='checkbox' name='remove_texture' value='removed' checked>Remove textures 
		<button class='champs' type="button" onclick="loadCity();">Upload</button>
	</form>
<?php echo $msg;?>
</fieldset>