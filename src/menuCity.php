<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 12/12/2014
*
* Menu to upload 3d city
*
* ATTENTION : in php conf file, set post_max_size and upload_max_filesize to large size (~20M)

TODO : 
- removing some percent of file might not work if tags are in LOWERCASE!!
*/

ini_set('display_errors', 1);


require_once('CityRDF.class.php');
require_once('SesameInterface.class.php');

$nameRepo = "";
$msg = "<div id='city_message' class='error'></div>";

if (isset($_FILES["uploadcity_file"])) {
	try 
	{
		$nameFile = $_FILES["uploadcity_file"]["name"];
		$tempFile = $_FILES["uploadcity_file"]["tmp_name"];
		//echo $nameFile;
		$completeUpload = $_POST["uploadcity_complete"];
		$removeTexture = (($_POST["uploadcity_texture"] == "true") ? true : false);

		if ($_FILES["uploadcity_file"]["error"] > 0)
			throw new Exception("Upload error n°".$_FILES["uploadcity_file"]["error"]);

		//if ($_FILES["uploadcity_file"]["type"] != "text/xml" || $_FILES["uploadcity_file"]["type"] != ".gml")
		//	throw new Exception ("File type must be either xml or gml");§

		// GENERATE city RDFable
		$city = new CityRDF($tempFile, $completeUpload, $removeTexture);
/*
		// create repository
		$nameRepo = $str=preg_replace('/\s+/', '', $nameFile); // removes spaces
		$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');
		if (!$sesame->existsRepository($nameRepo)) {
			$description = "Repository for the city '" . $nameRepo . "' created on " . date("Y-m-d H:i:s");
			$sesame->createRepository($nameRepo, $description);
			$sesame->setRepository($nameRepo);
 
			// upload city model as a graph
			$context = "<" . CityRDF::FILE_CONTEXT . $nameRepo . ">";
			$sesame->appendFile($city->getFile(), $context);
		}
		else{
			$msg = "<div id='city_message' class='error'>A repository for this file already exists.</div>";
			//throw new Exception("A repository for this file already exists.");
			//NOT AN ERROR HERE, or will erase the repo!!!
		}
*/		
		$msg = "<div id='city_message' class='confirmed'>A repository for the 3D model '$nameRepo' has been created!</div>";
	}
	catch (Exception $e){
		//delete repo
		//$sesame->deleteRepository($nameRepo);

		$msg = "<div id='city_message' class='error'>". $e->getMessage() ."</div>";
	}
	
}

?>

<fieldset> <legend>Create a graph from a CityGML file</legend> 
	<form>
		<p>
			<input id='uploadcity_file' type='file' name='uploadcity_file' />
			File type : 
			<input id='uploadcity_type' type='radio' name='uploadcity_type' readonly checked/> GML
		</p>
		<p>		
			Remove textures : <input id='uploadcity_texture' type='checkbox' name='uploadcity_texture' value='removed' checked>
		</p>
		<p>		
			Upload completeness : <input id='uploadcity_complete' type='number' name='uploadcity_complete' value='50' style="width:50px" onchange="verifyPercent(this);"> %
		</p>
		<button class='champs' type="button" onclick="loadCity(false);">Upload</button>
	</form>
	<?php echo $msg;?>
</fieldset>