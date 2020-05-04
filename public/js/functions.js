'use strict';

/**
 * @param event
 * @param {string} button_id
 * @returns {boolean}
 */
function checkEnter(event, button_id)
{
	// This function checks if the pressed key is the ENTER key and will than simulate a click for the given button
	const key = document.layers ? event.which : document.all ? event.keyCode : event.keyCode;
	if (key === 13) {
		document.getElementById(button_id).click();
		return false;
	}
	return true;
}

// UIC
function multiply(_1,_2){if(_2===1){return (_1);}else{if(_1<5){return (2*_1);}else{return ((2*_1)-9);}}}function divide(_3,_4){if(_4===1){return (_3);}else{if(_3%2===0){return (_3/2);}else{return ((_3+9)/2);}}}function calculate(_5){var _6=_5.uic.value;var _7=_6.length;var _8=-1;var _9=0;var _a=1;var _b=0;_5.result.value="";_5.result1.value="";for(var i=_7;i>=1;i--){var c=_6.substring(i-1,i);if((c>="0")&&(c<="9")){_b=_b+parseInt(multiply(c,_a),10);_a=3-_a;}else{if(c==="?"){if(_8!=-1){alert("Onjuiste invoer: meer dan 1 vraagteken ingevoerd!");return 0;}else{_8=i;_9=_a;_a=3-_a;}}}}if(_8===-1){alert("Onjuiste invoer: geen vraagteken ingevoerd!");return 0;}else{var _e=10-(_b%10);_e=(_e%10);_e=divide(_e,_9);var _f=_6.substring(0,_8-1)+_e+_6.substring(_8,_7);_5.result.value=_f;_5.result1.value=_e;return 1;}}
