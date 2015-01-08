<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 6/12/2014
*
* main menu composed of all different menu
* each menu is called dynamically called with AJAX queries
*/
?>

<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr' lang='fr'>
	<head>
		<title>PROTOTYPE</title>
		<meta charset='UTF-8'>
		<link rel="stylesheet" href="style.css">
	</head>

	<body>
		<header>
			<div id='headerText'>
				Prototyping visualization techniques in 3DCM
				<p>How to use</p>
			</div>
		</header>

		<div id='mainBlock'>

			<!-- MENU TO UPLOAD CITY -->
			<div class='titre'>City graph</div>
			<div id='menuCity'>
				<?php include("menuCity.php");?>
			</div>
			<div id='menuCity_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>

			<!-- add 'delete cities' menu here-->

			<!-- MENU TO UPLOAD DATA -->
			<div class='titre'>Data graph</div>
			<div id='menuData'>
				<?php include("menuData.php");?>
			</div>
			<div id='menuData_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
			
			<!-- add 'delete data graphs' menu here-->
				
			<!-- MENU TO CREATE TECHNIQUE -->
			<div class='titre'>Abstract visualization techniques</div>
			<div id='menuTechnique'>
				<?php include("menuTechnique.php");?>
			</div>
			<div id='menuTechnique_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>

			<!-- MENU TO GENERATE ENRICHED MODEL -->
			<div class='titre'>Enriched city model</div>
			<div id='menuEnrichment'>
				<?php include("menuEnrichment.php");?>
			</div>
			<div id='menuEnrichment_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
			
		</div>
	</body>

</html>

<script type="text/javascript" src="main.js"></script>