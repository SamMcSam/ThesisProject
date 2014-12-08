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
		<div id='mainBlock'>

			<!-- MENU TO UPLOAD CITY -->
			<div id='menuCity'>
				<?php include("menuCity.php");?>
			</div>
			<div id='menuCity_loading' >
				<img src="../img/loading.gif" style="display:none;">
			</div>
			
			<!-- MENU TO UPLOAD DATA -->
			<div id='menuData'>
				<?php include("menuData.php");?>
			</div>
			<div id='menuData_loading' style="display:none;">
				<img src="../img/loading.gif">
			</div>
				
			<!-- MENU TO CREATE TECHNIQUE -->
			<div id='menuTechnique'>
				<?php include("menuTechnique.php");?>
			</div>
			<div id='menuTechnique_loading' style="display:none;">
				<img src="../img/loading.gif">
			</div>

			<!-- MENU TO GENERATE ENRICHED MODEL -->
			<div id='menuEnrichment'>
				<?php include("menuEnrichment.php");?>
			</div>
			<div id='menuEnrichment_loading' style="display:none;">
				<img src="../img/loading.gif">
			</div>
			
		</div>
	</body>

</html>

<script type="text/javascript">
	

    //--------------------------------------------------
    //Fonctions Ajax
    //--------------------------------------------------
    
	function getXhr() 
	{
		var xhr = null;

		if (window.XMLHttpRequest){
			xhr = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject('Msxml2.XMLHTTP');
			} catch (e) {
				xhr = new ActiveXObject('Microsoft.XMLHTTP');
			}       
		}
		else {
			alert('Votre navigateur ne supporte pas les objets XMLHTTPRequest...'); 
			xhr = false; 
		}
		return xhr;
	}
    
    function xhrHTML(div, url, data) 
	{
		document.getElementById(div + "_loading").style.display = '';
	
		var xhr = null;
		var xhr = getXhr();
		
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) {
				document.getElementById(div).innerHTML = xhr.responseText;
				document.getElementById(div + "_loading").style.display = 'none';
			}
		}
		
		//xhr.open('GET',url,true);
		//xhr.send();
		xhr.open("POST",url,true);
		xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xhr.send(data);
	}
    
    //--------------------------------------------------

    function loadCity()
    {
    	//get all the data from this form
    	var nameCityFile = document.getElementById(uploadcity_name);

    	var data = "variable1=truc&variable2=bidule";    	
    	xhrHTML("menuCity", "menuCity.php", data);
    }

    function loadData()
    {
    	var data = "";

    	//get all the data from this form

    	xhrHTML("menuData", "menuData.php", data);
    }

    function loadEnrichment()
    {
    	var data = "";

    	//get all the data from this form

    	xhrHTML("menuEnrichment", "menuEnrichment.php", data);
    }

</script>