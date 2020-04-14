<?php
if (!defined('PAGE_TYPE')) { exit(); }

doContentpanelHeader(CONTENTPANELS_DOORKOMST);
doOutput('<table cellspacing="0" cellpadding="0" border="0" align="center">', $base_indent+6);
	doOutput('<tr>', $base_indent+8);
		doOutput('<td class="contentpanel">', $base_indent+10);
			$from = date('H:i', mktime(date('H'), date('i')-5, 0, date('m'), date('d'), date('Y')));
			$to = date('H:i', mktime(date('H'), date('i')+30, 0, date('m'), date('d'), date('Y')));
			doOutput(CP_DOORKOMST_LOCATIE.'<input type="text" name="station" maxlength="10" value="'.ucfirst(strtolower($params['default_spot_place'])).'" onKeyDown="javascript:return checkEnter(event, \'doorkomst\');" tabindex="'.giveTabIndex().'" />&nbsp;&nbsp;', $base_indent+12);
		doOutput('</td><td class="contentpanel" rowspan="2" valign="middle">', $base_indent+10);
			$query = 'select tdr_nr from '.TDR_DB_PREFIX.'_tdr_drgl where now() between start_datum and eind_datum';
			$dbset_tdr_now = $db->query($query);
			list($tdr_nr_now) = $db->fetchRow($dbset_tdr_now);
			$dagnr = date('w'); if ($dagnr<1) { $dagnr += 7; }
			doOutput(geefButton("document.location='".SITE_URL."/doorkomststaat/".$tdr_nr_now."/'+document.MasterForm.station.value+'/".$dagnr."/'+document.MasterForm.time_start.value+'/'+document.MasterForm.time_end.value+'/g/';", 'Vraag op', '', 'D', true, true, 'doorkomst', '', true), $base_indent+12);
		doOutput('</td>', $base_indent+10);
	doOutput('</tr><tr>', $base_indent+8);
		doOutput('<td class="contentpanel">', $base_indent+10);
			doOutput(CP_DOORKOMST_VAN."<input type=\"text\" name=\"time_start\" size=\"5\" maxlength=\"5\" value=\"".$from."\" onKeyDown=\"javascript:return checkEnter(event, 'doorkomst');\" tabindex=\"".giveTabIndex()."\" />", $base_indent+12);
			doOutput(CP_DOORKOMST_TOT."<input type=\"text\" name=\"time_end\" size=\"5\" maxlength=\"5\" value=\"".$to."\" onKeyDown=\"javascript:return checkEnter(event, 'doorkomst');\" tabindex=\"".giveTabIndex()."\" />", $base_indent+12);
		doOutput('</td>', $base_indent+10);
	doOutput('</tr>', $base_indent+8);
doOutput('</table>', $base_indent+6);
doContentPanelFooter();
?>