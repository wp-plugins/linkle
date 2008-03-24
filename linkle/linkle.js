window.onload = initForm;

function initForm() {

	showSearch();	
	var myDiv;
	var i = 0;
	while(myDiv = document.getElementById("linkle_entry_"+i)) {
		newDiv = document.createElement('div');
		newDiv.setAttribute("id", "linkle_compress_"+i);
		newDiv.style.marginLeft = "50px";
		newDiv.style.width = "20em";
		myDiv.style.width = "42em";
		myDiv.style.marginLeft = "20px";
		myDiv.style.border = "1px solid #b2b2b2";
		document.getElementById("linkle_custom_options").insertBefore(newDiv, myDiv);
		hideDiv(i);
		i++;
	}
}

function editLink(index) {
	var type_element = document.getElementsByName("linkle_handler_"+index+"_type")[0];
	var description_element = document.getElementsByName("linkle_handler_"+index+"_description")[0];
	return type_element.value+' <a href="#" onclick="return showDiv('+index+');"><small>edit</small></a>';
}

function hideLink(index) {
	return '<b>'+document.getElementsByName("linkle_handler_"+index+"_type")[0].value+'</b> <a href="#" onclick="return hideDiv('+index+');"><small>hide</small></a>';
}
function addLink(index) {
	return '<a href="#" onclick="return showDiv('+index+');"><small>add new</small></a>';
}
function hideDiv(index) {
	if(!(document.getElementsByName("linkle_handler_"+index+"_type")[0].value == '')){
		document.getElementById("linkle_compress_"+index).innerHTML = editLink(index);
	} else {
		document.getElementById("linkle_compress_"+index).innerHTML = addLink(index);
	}	
	hDiv = document.getElementById("linkle_compress_"+index);
	hDiv.style.padding = "0px";
	hDiv.style.background = "#fff";
	hDiv.style.borderWidth = "0px";
	hDiv.style.marginLeft = "50px";
	hDiv.style.marginTop = "0px";
	document.getElementById("linkle_entry_"+index).style.display = 'none';
	return false;
}
function showDiv(index) {
	hDiv = document.getElementById("linkle_compress_"+index);
	hDiv.style.padding = "3px";
	hDiv.style.marginLeft = "40px";
	hDiv.style.marginTop = "5px";
	hDiv.style.background = "#e5f3ff";
	hDiv.style.border = "1px solid #b2b2b2";
	hDiv.style.borderBottom = "0px";
	hDiv.innerHTML = hideLink(index);

	bDiv = document.getElementById("linkle_entry_"+index);
	bDiv.style.padding = "10px 10px 10px 15px";
	bDiv.style.display = 'block';
	return false;
}
function showSearch() {
	sDiv = document.getElementById("linkle_search");
	sDiv.style.margin = "0px 0px 10px 20px";
	sDiv.innerHTML = '<small style="display:block; text-indent:5px;"> Search <span style="text-align:right"><a href="#" onclick="return clearSearch()">clear</a></span></small><input id="linkle_search_input" onKeyUp="searchLinks(this.value);" onKeyPress="return disableEnterKey(event);" type="text" />';
}	
function searchLinks(query) {
	var search = new RegExp('^'+query, "i");
	i = 0;
	while(myDiv = document.getElementById("linkle_compress_"+i)) {
		if(search.exec(document.getElementsByName("linkle_handler_"+i+"_type")[0].value)) {
			document.getElementById("linkle_compress_"+i).style.display = 'block';
		} else {
			document.getElementById("linkle_compress_"+i).style.display = 'none';
			hideDiv(i);
		}
	i++;
	}
}
function clearSearch() {
	document.getElementById("linkle_search_input").value = "";
	searchLinks("");
	return false;
}
function disableEnterKey(e) {
	var key;     
	if(window.event)
		key = window.event.keyCode; //IE
	else
		key = e.which; //firefox     
	return (key != 13);
}
