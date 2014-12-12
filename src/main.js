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
	document.getElementById(div).style.display = 'none';


	var xhr = null;
	var xhr = getXhr();
	
	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4 && xhr.status == 200) {
			document.getElementById(div).innerHTML = xhr.responseText;
			document.getElementById(div).style.display = '';
			document.getElementById(div + "_loading").style.display = 'none';
			refreshSection(div);
		}
	}
	
	xhr.open("POST",url,true);
	//xhr.setRequestHeader("Content-type", false); //DOES NOT WORK IF USE A FORMDATA
	xhr.send(data);
}

//--------------------------------------------------
//Fonctions pour chaque bouton
//--------------------------------------------------

function loadCity(refresh)
{
	//get all the data from this form
	var uploadcity_file = document.getElementById("uploadcity_file").files;
	//var type_file
	var uploadcity_complete = document.getElementById("uploadcity_complete").value;
	var uploadcity_texture = document.getElementById("uploadcity_texture").checked;
	var formData = null;

	if (!refresh) {
		if (uploadcity_file.length < 1){
			afficheMessage("city", "Please select a file to upload.");
			return;
		}

		formData = new FormData();
    	formData.append('uploadcity_file', uploadcity_file[0], uploadcity_file[0].name);
    	formData.append('uploadcity_complete', uploadcity_complete);
    	formData.append('uploadcity_texture', uploadcity_texture);
    }
	
    xhrHTML("menuCity", "menuCity.php", formData);
}

function loadData(refresh)
{
	//get all the data from this form
	var uploaddata_file = document.getElementById("uploaddata_file").files;
	var selectRepo = document.getElementById("repo_name");
	var repoNumber = selectRepo.options[selectRepo.selectedIndex].value;
	var selectType = document.getElementById("data_type");
	var dataType = selectType.options[selectType.selectedIndex].value;
	var formData = null;

	if (!refresh){
		if (repoNumber < 0){
			afficheMessage("data", "Please select a repository.");
			return;
		}

		if (uploaddata_file.length < 1){
			afficheMessage("data", "Please select a file to upload.");
			return;
		}

		formData = new FormData();
    	formData.append('uploaddata_file', uploaddata_file[0], uploaddata_file[0].name);
    	formData.append('repo_name', repoNumber);
    	formData.append('data_type', dataType);
	}

    xhrHTML("menuData", "menuData.php", formData);
}

function loadEnrichment(refresh)
{
	var formData = null;

	//get all the data from this form
	if (!refresh){
		formData = new FormData();
    	//formData.append('uploaddata_file', uploaddata_file[0], uploaddata_file[0].name);
    	//formData.append('repo_name', repoNumber);
	}

	xhrHTML("menuEnrichment", "menuEnrichment.php", formData);
}

//--------------------------------------------------

function afficheMessage(div, message)
{
	document.getElementById(div + "_message").className = "error";
		document.getElementById(div + "_message").innerHTML = message;
}

//NOT FINISHED
function refreshSection(div)
{
	switch (div) {
	    case "menuCity":
	        //loadCityDelete(true);
			loadData(true);
			//loadDataDelete(true);
	        break;
	    //ETC!
    }	
	
}

// -----------------------------------------------

function verifyPercent(element)
{
	var content = parseInt(element.value);

	if (isNaN(content))
		element.value = 100;
	else 
	{
    	if (content > 100)
    		element.value = 100;
    	else if (content < 0)
    		element.value = 0;
	  }	
}