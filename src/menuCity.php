<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 10/11/2014
*
* Menu to upload 3d city
*/

require_once('SesameInterface.class.php');

//if isset($_POST["uploadcity_name"]);
//if isset($_POST["uploadcity_isCleaned"]);

// GENERATE city name

$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');

$msg = "";

//if ($sesame->existsRepository(__le_nom du rep__))
	$msg = "<div class='error'>A repository for this 3D model has already been created!</div>";
//else {

	// create repository
	//$sesame->createRepository("INTERFACETEST");

	// upload city model as a graph
	//$sesame->appendFile("Munich_clean2.xml");

	$msg = "<div class='confirmed'>A repository for the 3D model '' has been created!</div>";
//}
?>

<div class='titre'>City graph</div>
<fieldset> <legend>Créer graphe depuis un fichier citygml</legend> 
	<form>
		<input type='file' name='uploadcity_name' />
		<input type='radio' name='uploadcity_isCleaned' value='false' checked>Original
		<input class='champs' type='submit' value='Upload' onclick="loadCity();"/>
	</form>
<?php echo $msg;?>
</fieldset>