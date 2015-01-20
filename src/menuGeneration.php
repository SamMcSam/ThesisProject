<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 20/1/2015
*
* Menu to  generate an enriched 3DCM, part 2 - specifying the parameters
*, calls generateEnrichment.php script and returns a page with embedded x3d
*/

require_once('../config/constantsPath.php');

require_once('TechniqueQuery.class.php');

//goes back if information missing (in case of error)
if (!isset($_POST["repoName"]) || !isset($_POST["dataName"]) || !isset($_POST["techName"]))
{
	header('Location: ' . 'menuEnrichment.php', true, 303);
	die();
}

$repoName = htmlspecialchars($_POST["repoName"]);
$dataName = htmlspecialchars($_POST["dataName"]);
$techName = htmlspecialchars($_POST["techName"]);

//get all parameters for this technique
$technique = new TechniqueQuery($techName);
$listParameters = $technique->getParameterNames();

?>

<a name="enrichment"></a>
<fieldset> <legend>Generate an enriched 3DCM - specify parameters</legend> 
	<form method='post' action='generateEnrichment.php' >
		Use the city : <input type='text' name='repoName' value='<?php echo $repoName; ?>' readonly/>
		Use the data : <input type='text' name='dataName' value='<?php echo $dataName; ?>' readonly/>
		Use the technique : <input type='text' name='techName' value='<?php echo $techName; ?>' readonly/>

		Parameters :
		<ul>
			<?php
				foreach ($listParameters as $parameter => $value) {
					echo "<li>$parameter : <input type='text' name='parameter_1' value='$value' /></li>";   // ????????????
				}
			?>
		</ul>
		
		<button class='champs' type='button' onclick="loadEnrichment(false);">Cancel</button>
		<input type='submit' value='Generate enriched model'/>
	</form>
</fieldset>