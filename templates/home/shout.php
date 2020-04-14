<?php
if (!defined('PAGE_TYPE')) { exit(); }

doContentpanelHeader('Shoutbox');
doOutput('<div id="shoutInhoud"></div><br /><table width="100%">', $base_indent+6);
	if ($session->logged_in) {
		doOutput('<tr>', $base_indent+8);
			doOutput('<td align="right"><input type="text" name="shout" size="30" maxlength="255" /></td>', $base_indent+10);
			doOutput('<td align="right">'.geefButton("var txt=document.MasterForm.shout.value.replace(/&/g, '%26'); txt=txt.replace(/\+/g, '%2B'); txt=txt.replace(/#/g, '%23'); document.location='/shout/?shout_uid=".$session->uid."&shout='+txt", 'Shout', '', '', true, true, '', '', true).'</td>', $base_indent+10);
		doOutput('</tr>', $base_indent+8);
	}
	echo '<tr><td align="center" colspan="3"><a href="/shout_archief/" target="_blank" title="Shout archief">Shout archief</a></td></tr>
</table>';
doContentPanelFooter();
?>