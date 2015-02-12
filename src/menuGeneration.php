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

require_once('DataInsert.class.php');
require_once('TechniqueQuery.class.php');

//goes back if information missing (in case of error)
if (!isset($_POST["enrichment_repoName"]) || !isset($_POST["enrichment_dataName"]) || !isset($_POST["enrichment_techName"]))
{
	header('Location: ' . 'menuEnrichment.php', true, 303);
	die();
}

$repoId = htmlspecialchars($_POST["enrichment_repoId"]);
$repoName = htmlspecialchars($_POST["enrichment_repoName"]);
$dataName = htmlspecialchars($_POST["enrichment_dataName"]);
$techName = htmlspecialchars($_POST["enrichment_techName"]);

//get all parameters for this technique
$technique = new TechniqueQuery($techName);
$listParameters = $technique->getParameterWithValues();

?>

<a name="enrichment"></a>
<fieldset> <legend>Generate an enriched 3DCM - specify parameters</legend> 
	<form method='post' action='generateEnrichment.php' target="_blank">
		<p>
			3D model : <?php echo $repoName; ?>
			<input type='hidden' name='enrichment_repoName' id='enrichment_repoName' value='<?php echo $repoName; ?>' readonly/>
			<input type='hidden' name='enrichment_repoId' id='enrichment_repoId' value='<?php echo $repoId; ?>' readonly/>
		</p>

		<p>
			Data set : <?php echo str_replace(DataInsert::DATA_URI, "", $dataName); ?>
			<input type='hidden' name='enrichment_dataName' id='enrichment_dataName' value='<?php echo $dataName; ?>' readonly/>
		</p>

		<p>
			Visualization technique : <?php echo $techName; ?>
			<input type='hidden' name='enrichment_techName' id='enrichment_techName' value='<?php echo $techName; ?>' readonly/>
		</p>

		<p>
			Parameters :
			<ul style='margin-left:20px;'>
				<?php
					foreach ($listParameters as $parameter => $value) {
						echo "<li>$parameter : <input type='text' name='parameters[$parameter]' value=". '"' . htmlspecialchars($value) . '"' . " /></li>";   // ????????????
					}
				?>
			</ul>
		</p>

		<table>
			<tr>
				<td> <button class='champs' type='button' onclick="goBackEnrichment();">Cancel</button> </td>
				<td> <input type='submit'  name="display_button" value='Visualize enriched model'/> </td>
				<td> <input type='submit'  name="save_button" value='Save locally'/> </td>
			</tr>
		</table>

	</form>
</fieldset>