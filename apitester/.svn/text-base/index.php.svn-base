<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Call Tester</title>
<link id="ico1" rel="shortcut icon" href="favicon.png" type="image/x-icon" />
<link id="ico2" rel="icon" href="favicon.png" type="image/x-icon" />

<script type="text/javascript" src="mootools.js" ></script>
<script type="text/javascript" src="class.shortcut.js" ></script>
	
<script type="text/javascript">
// assign the apple key as control equivalent
var controlkey = Browser.Platform.mac ? 'meta' : 'control';

// assign some shortcuts
var cuts = new Shortcut();
	cuts.add(controlkey+'+shift+t', addFieldPrompt);
	cuts.add(controlkey+'+shift+g', function() { $('tester').submit(); });
	cuts.add(controlkey+'+shift+l', saveForm);
	cuts.add(controlkey+'+shift+f', addFieldPrompt);

	
// collection of fields
var f = {fields: ''};


function addFieldPrompt() {
	var name = prompt('What is the name of the parameter?');
	if (name) {
		addField(name);
	}
}

function addUploadPrompt() {
	var name = prompt('What is the name of the parameter?');
	if (name) {
		addField(name,"","",1);
	}
}

function addField(name, value, skipFieldList,upload) {
	var input = document.createElement('input');
	var tr = document.createElement('tr');
	var td1 = document.createElement('td');
	var td2 = document.createElement('td');

	tr.id = 'row-'+name;
	td1.innerHTML = name;
	
	new Element('input', {
		name:name,
		id:name,
		type:upload ? 'file' : 'text',
		value:value ? value : '',
		size:upload ? 1 : 20,
	}).inject(td2);

	var delLink = new Element('input',{
		id:'delLink'+name,
		type:'button',
		value:'x',
		events:{
			click:function() {
				delRow(this.id);
			}
		},
		styles:{
			marginLeft:'4px'
		}
	}).inject(td2);
	
	td1.inject(tr);
	td2.inject(tr);
	tr.inject($('fields'));

	if (!skipFieldList) {
		f.fields = f.fields + name + ',';
	}

	document.getElementById(name).focus();
}

function delRow(name) {
	p = document.getElementById('fields');
	cId = 'row-' + name.replace(/delLink/, '');
	c = document.getElementById(cId);
	p.removeChild(c);

	f.fields = f.fields.replace(new RegExp(name.replace(/delLink/, '')+','), '');
}

function getUrlParam(name) { 
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]"); 
	var regexS = "[\\?&]"+name+"=([^&#]*)"; 
	var regex = new RegExp(regexS); 
	var results = regex.exec(window.location.href); 
	if (results == null) {
		return false; 
	} else {
		return unescape(results[1]);
	}
}

function saveForm() {
	var formAction = document.getElementById('tester').getAttribute('action');
	var formMethod = document.getElementById('tester').getAttribute('method');
	
	addField('formAction', formAction, true);
	addField('formMethod', formMethod, true);
	addField('formFields', f.fields, true);

	document.getElementById('tester').method = 'GET';
	document.getElementById('tester').action = 'call-tester.html';
	document.getElementById('tester').target = '';
	document.getElementById('tester').submit();
}

/*
function requestId() {
	var myRequest = new Request.HTML({url:'/vk/get-token', method: 'get', onSuccess: function(responseText, responseXML) {
		$('requestId').value=responseXML.getElement('value')[0].get('text');
	}}).send("");
}
*/

document.addEvent('domready', function() {
	//addField('username');
	//addField('password', 'password');
	//requestId();
});

</script>
<style type="text/css">
	body {
		font-family: Myriad Pro;
		background: #EDF5FA url(bodybg.jpg) repeat-x;
	}
	.button { margin:5px; height:28px; width:80px;}
	h1 { font-size:16px; font-weight:bold; color:#FFF; margin-bottom:3px; }
</style>
</head>
<body>

<table width="100%" height="100%"><tr><td valign="top" width="20%">
<h1>Request</h1>
<br />
<table>
<tbody>
<tr><td>Action</td><td>
	
	<input type="text" id="formAction" onblur="document.getElementById('tester').setAttribute('action', this.value);" value="/" />
	<select style="width:5px;" name="request_select" onchange="$('formAction').value=this.value; document.getElementById('tester').setAttribute('action', this.value);">
		<option value=""></option>
	<?php
	include dirname(__FILE__)."/getfunctions.php";
	foreach($methods as $request) {
		echo "<option value=\"$request\">$request</option>\n";
	}
	?>
</select>
</td></tr>

<tr><td>Method</td><td><input type="text" id="formMethod" onblur="document.getElementById('tester').setAttribute('method', this.value);" /></td></tr>
</tbody>
</table>

<input type="hidden" id="specialFieldList" name="specialFieldList" />

<h1 style="color:#000;">Parameters</h1>


<form id="tester" method="post" action="/" target="results" >
	<table style="width:100%" width="100%">
		<tbody id="fields">
			<tr><td>requestId</td><td><input type="text" name="requestId" id="requestId" /></td></tr>
		</tbody>
	</table>
	
	<input type="button" class="button" value="Add field" title="crtl+shift+f" onclick="addFieldPrompt();" />
	<input type="button" class="button" value="Add upload" title="crtl+shift+u" onclick="addUploadPrompt();" />
	<input type="button" class="button" value="Create link" title="crtl+shift+l" onclick="saveForm();" />
	
	<p><input type="submit" value="GO" id="bu_submit" title="ctrl+shift+g" style="width:275px; height:54px;margin-bottom:none;" /></p>
</form>


</td><td valign="top">
<h1>Results</h1>
<br />

<iframe width="100%" height="625" id="results" name="results" ></iframe>
</td></tr></table>
<script type="text/javascript">

if (getUrlParam('formFields')) {
	var fields = getUrlParam('formFields').split(",");
	for(i = 0; i < fields.length; i++){
		if (fields[i] != '') {
			addField(fields[i], unescape(getUrlParam(fields[i])));
		}
	}
}

if(getUrlParam('requestId')) document.getElementById('requestId').value = getUrlParam('requestId');
if(getUrlParam('formAction') != "") document.getElementById('formAction').value = getUrlParam('formAction');
document.getElementById('formMethod').value = getUrlParam('formMethod') ? getUrlParam('formMethod') : 'post';

document.getElementById('tester').setAttribute('action', document.getElementById('formAction').value);
document.getElementById('tester').setAttribute('method', document.getElementById('formMethod').value);
</script>
</body>
</html>