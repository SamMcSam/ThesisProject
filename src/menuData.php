<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 6/12/2014
*
* Menu to upload data
*/

require_once('SesameInterface.class.php');

//if isset($_POST["uploadcity_name"]);
//if isset($_POST["uploadcity_isCleaned"]);

// GENERATE city name

$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame', $repository);

$c = '
PREFIX data:<http://test.com/>
INSERT DATA
	{
	  GRAPH <http://graphData>
	  { 
		data:x data:tag "three" . 
		data:y data:tag "four" . 
	  }
	}
';

//$query = $sesame->update($c);

$msg = "";

//if ?
	$msg = "<div class='error'>Data couldn't be uploaded. </div>";
//else {
	$msg = "<div class='confirmed'>The data '' has been uploaded to the '' repository!</div>";
//}
?>

<div class='titre'>Data graph</div>
<fieldset> <legend>Charger les données dans un graphe</legend> 
	<form method='post' action='_______.php' >
		<input type='file' name='nom' />
		<input class='champs' type='submit' value='Upload' onclick="loadData();"/>
	</form>
<?php echo $msg;?>
</fieldset>