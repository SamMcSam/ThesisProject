<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 12/2/2015
* last update : 12/2/2015
*
* display the enrichment in an HTML page
*/
?>

<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr' lang='fr'>
	<head> 
		<meta charset='UTF-8'>
		<title>My 3D enriched model</title>
		<script type='text/javascript' src='<?php echo PATH_3DSYS;?>x3dom.js'></script>
		<link rel='stylesheet' type='text/css' href='<?php echo PATH_3DSYS;?>x3dom.css'></link>
		<link rel="stylesheet" href="style.css">
	</head>

	<body>
		<div id='loading'>
			<div>***LOADING***</div> 
			<div id='loading0'>Analysing specification...</div>
			<div id='loading1'>Generating request...</div>
			<div id='loading2' style='display:none;'>Querying triple store...</div>
			<div id='loading3' style='display:none;'>Analysing results...</div>
			<div id='loading4' style='display:none;'>Constructing abstract objects...</div>
			<div id='loading5' style='display:none;'>Using parameters : 
				<?php 
					print "<pre>";
					print_r($parameterList);
					print "</pre>";
				?>
			</div>
			<div id='loading6' style='display:none;'>Applying layout managers...</div>
			<div id='loading7' style='display:none;'>Generating concrete objects...</div>
			<div id='loading8' style='display:none;'>Enriching the scene...</div>
		</div>
		<div id='mainBlock' style='display:none;'>
			<?php 
				if (empty($errorMessage))
					echo "<p id='OK' style='height:400px'><x3d width='600px' height='400px'>" . $output . "</x3d></p>";
			?>

			<p>
				<?php echo $errorMessage; ?>
			</p>
		</div>

	</body>
</html>

<script type="text/javascript">
	var i = 2;

	document.onload = function () {
		var x3d = document.getElementById('OK');

		if (x3d == null){
			document.getElementById('loading').style.display = 'none';
	   	 	document.getElementById('mainBlock').style.display = '';
		}
		else
			printLog();
	};

	//TODO: display according to status AJAX, instead of faked timer
	function printLog(){
		var max = 800;
		var min = 300;
		var wait = Math.floor(Math.random()*(max-min+1)+min);

		if (i < 9){
			setTimeout(function() {
				document.getElementById('loading' + i).style.display = '';
				i++;
				printLog();
			}, wait);
		}
		else{
			setTimeout(function() {
				document.getElementById('loading').style.display = 'none';
		   	 	document.getElementById('mainBlock').style.display = '';
			}, 2000);
		}
	}

</script>