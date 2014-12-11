<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 10/11/2014
*
* Menu to upload 3d city
*
* ATTENTION : in php conf file, set post_max_size and upload_max_filesize to large size (~20M)
*/

ini_set('display_errors', 1);


require_once('CityRDF.class.php');
require_once('SesameInterface.class.php');

$msg = "<div id='city_message' class='error'></div>";

if (isset($_FILES["uploadcity_file"])) {
	try 
	{
		$nameFile = $_FILES["uploadcity_file"]["name"];
		$tempFile = $_FILES["uploadcity_file"]["tmp_name"];
		//echo $nameFile;
		$completeUpload = $_POST["complete_upload"];
		$removeTexture = (($_POST["remove_texture"] == "true") ? true : false);

		if ($_FILES["uploadcity_file"]["error"] > 0)
			throw new Exception("Upload error n°".$_FILES["uploadcity_file"]["error"]);

		//if ($_FILES["uploadcity_file"]["type"] != "text/xml" || $_FILES["uploadcity_file"]["type"] != ".gml")
		//	throw new Exception ("File type must be either xml or gml");§

		// GENERATE city RDFable
		$city = new CityRDF($tempFile, $completeUpload, $removeTexture);

/*
		echo "<pre>";
			echo $city->getXML();
		echo "</pre>";
*/
		// create repository
		$nameRepo = $nameFile; // MODIFICATION??/*

		$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');
		if (!$sesame->existsRepository($nameRepo)) {
			$sesame->createRepository($nameRepo);
			$sesame->setRepository($nameRepo);
 
			// upload city model as a graph
			$sesame->appendFile($city->getFile());

			//do some extra tuning?
		}
		else
			throw new Exception("A repository for this file already exists.");
		
		$msg = "<div id='city_message' class='confirmed'>A repository for the 3D model '$nameRepo' has been created!</div>";
	}
	catch (Exception $e){
		$msg = "<div id='city_message' class='error'>". $e->getMessage() ."</div>";
	}
	
}

?>

<div class='titre'>City graph</div>
<fieldset> <legend>Créer graphe depuis un fichier citygml</legend> 
	<form>
		<p>
			<input id='uploadcity_file' type='file' name='uploadcity_file' />
			<input id='complete_upload' type='hidden' name='complete_upload' value='100'><!--Completeness percentage -->
			<input id='remove_texture' type='checkbox' name='remove_texture' value='removed' checked>Remove textures 
		</p>
		<button class='champs' type="button" onclick="loadCity();">Upload</button>
	</form>
	<?php echo $msg;?>
</fieldset>