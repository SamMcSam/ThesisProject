<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 10/11/2014
*
* Menu to upload 3d city
*/

require_once('CityRDF.class.php');

$msg = "";

if (isset($_POST["uploadcity_name"])) {

	$nameFile = $_POST["uploadcity_name"];

	$city = new CityRDF();

	try {
		// GENERATE city name
		//$city->upload($nameFile);

		// create repository
		//$sesame->createRepository("INTERFACETEST");

		// upload city model as a graph
		//$sesame->appendFile("Munich_clean2.xml");

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
		<input type='file' name='uploadcity_name' />
		<!--<input type='radio' name='uploadcity_isCleaned' value='false' checked>Original -->
		<input class='champs' type='submit' value='Upload' onclick="loadCity();"/>
	</form>
<?php echo $msg;?>
</fieldset>