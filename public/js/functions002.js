'use strict';

function checkEnter(event, button_id) {
	// Deze functie controleert of de ingedrukte toets de ENTER is en simuleert dan een klik op de aangegeven button
	var key = document.layers ? event.which : document.all ? event.keyCode : event.keyCode;
	if (key == 13) {
		document.getElementById(button_id).click();
		return false;
	}
	return true;
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires="; expires="+date.toGMTString();
	} else {
		var expires='';
	}
	document.cookie = name + "=" + value + expires + "; path=/";
}

function eraseCookie(name) {
	createCookie(name, '', -1);
}

function getDynamicContent(site_url) {
	if(document.getElementById('spotsInhoud'))     { makeRequest(site_url + '/blocks/ajax/spots.php','spotsInhoud', '<br />De recente spots worden opgehaald.<br />Even geduld...'); }
	if(document.getElementById('shoutInhoud'))     { makeRequest(site_url + '/blocks/ajax/shout.php','shoutInhoud', '<br />De actuele shouts/tweets worden opgehaald.<br />Even geduld...'); }
	if(document.getElementById('storingenInhoud')) { makeRequest(site_url + '/blocks/ajax/storingen.php','storingenInhoud', '<br />De actuele verstoringen worden opgehaald.<br />Even geduld...'); }
}

function getElementTop(element_id) {
	if(document.layers) {
		var element = getObjNN4(document,element_id);
		return element.pageY;
	} else {
		var element = document.getElementById(element_id);
		var yPos    = element.offsetTop;
		var tempEl  = element.offsetParent;
		while(tempEl!=null) {
			yPos += tempEl.offsetTop;
			tempEl = tempEl.offsetParent;
		}
		return yPos;
	}
}

function insertAtCursor(textfield, text) {
	var sel;
	if (document.selection) {
		textfield.focus();
		sel = document.selection.createRange();
		sel.text = text;
	} else {
		if (textfield.selectionStart || textfield.selectionStart == '0') {
			var sel_start   = textfield.selectionStart;
			var sel_end     = textfield.selectionEnd;
			textfield.value = textfield.value.substring(0, sel_start) + text + textfield.value.substring(sel_end, textfield.value.length);
			textfield.selectionStart += text.length;
		} else {
			textfield.value += text;
			textfield.selectionStart = text.length;
		}
	}
	textfield.focus();
}

function makeRequest(url, elementId, waitText) {
	if (elementId != undefined && elementId.length > 0 && waitText != undefined && waitText.length > 0) {
		document.getElementById(elementId).innerHTML = waitText;
	}

	if (elementId != undefined && elementId.length>0) {
		$.ajax({ url:url, cache:false }).done(function(html) { document.getElementById(elementId).innerHTML = html; });
	} else {
		$.ajax({ url:url, cache:false });
	}
}

function plaatsKeyUp(site_url, plaats, field_id, previous_field_id) {
	if (plaats=='') {
		document.getElementById(field_id+'Div').style.visibility='hidden';
		document.getElementById(field_id+'Volledig').innerHTML='';
	} else {
		document.getElementById(field_id+'Div').style.visibility='visible';

		var previous_field_top = getElementTop(previous_field_id);
		document.getElementById(field_id+'Div').style.top = previous_field_top+'px';
		document.getElementById(field_id+'Div').style.visibility="visible";
		makeRequest(site_url+'/blocks/ajax/verk.php?plaats='+plaats+'&element='+field_id, field_id+'Div', plaats);
	}
}

function plaatsKlaar(_1e,_1f,_20){if(_1f!==""){document.getElementById(_20+"Input").value=_1f;document.getElementById(_20+"Div").style.visibility="hidden";makeRequest(_1e+"/blocks/ajax/verk.php?done=1&plaats="+_1f+"&element="+_20,_20+"Volledig",_1f);}}

function readCookie(name){var nameEQ=name+"=";var ca=document.cookie.split(';');for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' ')c=c.substring(1,c.length);if(c.indexOf(nameEQ)==0)return c.substring(nameEQ.length,c.length);} return null;}

function showImgSelected(_21,_22,_23){var _24,selectDom;_24=document.getElementById(_21);selectDom=document.getElementById(_22);_24.src=_23+"/"+selectDom.options[selectDom.selectedIndex].value;}

function sluitmaterieelidPopup(){var _25=0;var _26=0;document.getElementById("materieelidDiv").style.visibility="hidden";for(var i=0;i<document.MasterForm.elements.length;i++){if(document.MasterForm.elements[i].type=="checkbox"&&document.MasterForm.elements[i].name.indexOf("materieelid")>-1){_26++;if(document.MasterForm.elements[i].checked){_25++;}}}if(_25==_26){var _28="Alles geselecteerd</a>";}else{if(_25==1){var _29="1 soort geselecteerd</a> -- <a href=\"#\" onclick=\"matSelecteerAlles(); sluitmaterieelidPopup(); return false;\">Selecteer alles</a>";}else{if(_25>1){var _2a=_25+" soorten geselecteerd</a> -- <a href=\"#\" onclick=\"matSelecteerAlles(); sluitmaterieelidPopup(); return false;\">Selecteer alles</a>";}else{var _2b="Selecteer tenminste 1 soort door hier te klikken</a> of <a href=\"#\" onclick=\"matSelecteerAlles(); sluitmaterieelidPopup(); return false;\">Selecteer alles</a>";}}}document.getElementById("materieelidKop").innerHTML="<a href=\"#\" onclick=\"document.getElementById('materieelidDiv').style.visibility='visible'; return false;\">"+_2b;}

function toonPopup(url, titelText, breedte, e) {
	if (e.screenX) {
		xPos = e.screenX;
		yPos = e.screenY;
	} else {
		xPos = e.pageX;
		yPos = e.pageY;
	}
	document.getElementById('trein').style.left = xPos+'px';
	document.getElementById('trein').style.top = (yPos+10)+'px';
	document.getElementById('trein').style.width = breedte+'px';
	document.getElementById('treinTitel').innerHTML = titelText;
	document.getElementById('trein').style.visibility = 'visible';
	makeRequest(url, 'treinInhoud', 'Even geduld...');
}

function trim(value){return value.replace(/^\s+|\s+$/,'');}

// UIC
function multiply(_1,_2){if(_2===1){return (_1);}else{if(_1<5){return (2*_1);}else{return ((2*_1)-9);}}}function divide(_3,_4){if(_4===1){return (_3);}else{if(_3%2===0){return (_3/2);}else{return ((_3+9)/2);}}}function calculate(_5){var _6=_5.uic.value;var _7=_6.length;var _8=-1;var _9=0;var _a=1;var _b=0;_5.result.value="";_5.result1.value="";for(var i=_7;i>=1;i--){var c=_6.substring(i-1,i);if((c>="0")&&(c<="9")){_b=_b+parseInt(multiply(c,_a),10);_a=3-_a;}else{if(c==="?"){if(_8!=-1){alert("Onjuiste invoer: meer dan 1 vraagteken ingevoerd!");return 0;}else{_8=i;_9=_a;_a=3-_a;}}}}if(_8===-1){alert("Onjuiste invoer: geen vraagteken ingevoerd!");return 0;}else{var _e=10-(_b%10);_e=(_e%10);_e=divide(_e,_9);var _f=_6.substring(0,_8-1)+_e+_6.substring(_8,_7);_5.result.value=_f;_5.result1.value=_e;return 1;}}
