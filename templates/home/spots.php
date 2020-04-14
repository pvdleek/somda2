<?php
if (!defined('PAGE_TYPE')) { exit(); }

doContentpanelHeader(CONTENTPANELS_SPOTS, '/feed/rss.php?rss_type=spots');
doOutput("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">", $base_indent+6);
	$query = "select count(*) from ".DB_PREFIX."_spots where datum='".date("Y-m-d")."'";
	$dbset_aantal = $db->query($query);
	list($aantal_spots) = $db->fetchRow($dbset_aantal);
	doOutput("<tr valign=\"baseline\"><td class=\"contentpanel\" align=\"center\" colspan=\"3\"><strong>", $base_indent+8);
		switch($aantal_spots) {
			case 0:
				doOutput(CP_SPOTS_TODAY_0, $base_indent+10);
				break;
			case 1:
				doOutput(CP_SPOTS_TODAY_1, $base_indent+10);
				break;
			default:
				doOutput(sprintf(CP_SPOTS_TODAY_2, $aantal_spots), $base_indent+10);
				break;
		}
	doOutput("</strong></td></tr>", $base_indent+8);
doOutput('</table><div id="spotsInhoud"></div><br /><table align="center"><tr>', $base_indent+6);
	doOutput('<td>'.geefButton("location.href='".SITE_URL."/invoer/'", 'Spots invoeren', '', 'I', true, true, '', '', true).'</td>', $base_indent+6);
	doOutput('<td>'.geefButton("location.href='".SITE_URL."/spots/'", 'Meer spots', '', 'S', true, true, '', '', true).'</td>', $base_indent+6);
doOutput('</tr></table>', $base_indent+6);
doContentPanelFooter();
?>