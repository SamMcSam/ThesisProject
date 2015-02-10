<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 20/1/2015
*
* Menu to upload visualization techniques
* analyses varaibles and layout managers to see if correct
*/

require_once('../config/constantsPath.php');

require_once('SesameInterface.class.php');
require_once('TechniqueQuery.class.php');

$GENERIC_NAME = "test";

$analyse = "<div id='technique_analyse' class='analyse'>";
$msg = "<div id='technique_message' class='error'></div>";

$layoutsSupported = TechniqueQuery::getLayoutsSupported();

//use lang as test, because might not have file set up
//TODO - text upload
if (isset($_POST["uploadtechnique_lang"])) {
	try 
	{
		$nameTechnique = $_FILES["uploadtechnique_file"]["name"];
		$nameTechnique = explode(".", $nameTechnique)[0];
		$tempFile = $_FILES["uploadtechnique_file"]["tmp_name"];
		$targetTemp = PATH_TEMPFILES . $GENERIC_NAME . TechniqueQuery::TECHNIQUE_EXT;
		$targetFile = PATH_TECHNIQUES . $nameTechnique . TechniqueQuery::TECHNIQUE_EXT;

		$lang_technique = $_POST["uploadtechnique_lang"];

		if ($_FILES["uploadtechnique_file"]["error"] > 0)
			throw new Exception("Upload error n°".$_FILES["uploadtechnique_file"]["error"]);

		//verify if technique doesn't exist already
		if (in_array($nameTechnique, TechniqueQuery::getTechniquesSupported()))
			throw new Exception("Technique already in use");

		//move to temp directory
		if (!move_uploaded_file($tempFile, $targetTemp)) 
			throw new Exception("There was an error while uploading your file - can't access temporary directory");

		//loads the techniques, as if to use it, to get info on it
		$technique = new TechniqueQuery($GENERIC_NAME, PATH_TEMPFILES);

		//log an analyse of the technique

		$analyse .= "####################<br>";
		//echo parameters used
		$analyse .= "Technique parameters : <br><ul>";
		foreach ($technique->getParameterNames() as $parameter) {
			$analyse .= "<li>$parameter</li>";
		}
		$analyse .= "</ul>";

		//get layout managers used, and compare to existing ones
		$listLayout = TechniqueQuery::getLayoutsSupported();
		$analyse .= "Layout managers : <ul>";
		foreach ($technique->getLayoutNames() as $layout) {
			if (!in_array($layout, $listLayout))
				$analyse .= "<li class='error'>$layout - Layout manager is not present in the system</li>";
			else
				$analyse .= "<li>$layout</li>";
		}
		$analyse .= "</ul>";
		$analyse .= "####################<br><br>";

		// if invalid layout managers
		if (!$technique->isValidTechnique())
			throw new Exception("Couldn't upload technique because of unknown managers.");

		//everything ok
		if (!rename($targetTemp, $targetFile)) 
			throw new Exception("There was an error while uploading your file here - can't access technique directory");

		$msg = "<div id='technique_message' class='confirmed'>The new technique '$nameTechnique' has been uploaded!</div>";
	}
	catch (Exception $e){
		$msg = "<div id='technique_message' class='error'>". $e->getMessage() ."</div>";
	}

}

?>

<a name="technique"></a>
<fieldset> <legend>Upload visualization techniques</legend> 
	<form>

		<?php
			// if
			// menu confirmation
			echo "
				<p>
					Query language : 
					<input id='uploadtechnique_lang' type='radio' name='uploadtechnique_lang' value='sparql1.0' readonly checked/> SPARQL 1.0
				</p>
				<p>
					Upload by : 
					<input id='uploadtechnique_typeupload_file' type='radio' name='uploadtechnique_typeupload' value='file' checked onclick='switchTechniqueUploadType();'/> File
				</p>
				<p>
					<input id='uploadtechnique_file' type='file' name='uploadtechnique_file'/>
				</p>
			";
			//else
			//menu avec parametres
		?>	

		<button class='champs' type="button" onclick="loadTechnique(false);">Upload</button>
	</form>
	<p>
		<a href='../doc/howToVisu/help.html' target="_blank">Read me more on technique definition format</a>
	</p>
	<?php 
		echo $analyse . "</div>";
		echo $msg;
	?>
</fieldset>