<?php
if (!defined('PAGE_TYPE')) { exit(); }

doContentpanelHeader(CONTENTPANELS_WERKZAAMHEDEN, '/feed/rss.php?rss_type=drgl&amp;drgl_type=2');
doOutput("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">", $base_indent+6);
	$query = "select drglid, datum, einddatum, title, text
			from ".DB_PREFIX."_drgl
			where public='1'
				and ((datum>='".date("Y-m-d")."' and einddatum IS NULL)
					or (einddatum>='".date("Y-m-d")."'))
				and werkzaamheden='1'
			order by datum asc
			limit ".$params["max_drgls"];
	$dbset_drgls = $db->query($query);
	if($db->numRows($dbset_drgls)>0) {
		// Controleer of er meer DRGLs zijn dan er weergegeven kunnen worden
		$query = "select count(*)
				from ".DB_PREFIX."_drgl
				where public='1'
					and ((datum>='".date("Y-m-d")."' and einddatum IS NULL)
						or (einddatum>='".date("Y-m-d")."'))
					and werkzaamheden='1'";
		$dbset_nr_of_drgls = $db->query($query);
		list($nr_of_drgls) = $db->fetchRow($dbset_nr_of_drgls);

		while (list($drglid, $datum, $einddatum, $title, $text) = $db->fetchRow($dbset_drgls)) {
			$text = kortNieuws($text,100,true).CP_DRGL_KLIK;
			doOutput("<tr valign=\"baseline\">", $base_indent+8);
				if (strlen($einddatum)>0) {
					$datum = geefDatumWeergave($datum, true, false).' '.DRGL_TM.' '.geefDatumWeergave($einddatum, true, false);
				} else {
					$datum = geefDatumWeergave($datum, true, false);
				}
				doOutput('<td class="contentpanel" align="right" nowrap>'.$datum.'&nbsp;</td>', $base_indent+10);
				doOutput('<td>&nbsp;</td>', $base_indent+10);
				doOutput("<td class=\"contentpanel\"><a class=\"contentpanel\" href=\"".SITE_URL."/drgls/".$drglid."/\" ".giveMouseOver($text, true).">".$title."</a></td>", $base_indent+10);
			doOutput("</tr>", $base_indent+8);
		}
		if ($nr_of_drgls>$db->numRows($dbset_drgls)) {
			doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\">".DRGL_EXTRA_WZH."</td></tr>", $base_indent+8);
		}
	} else {
		doOutput("<tr valign=\"baseline\">", $base_indent+8);
			doOutput("<td class=\"contentpanel\" align=\"center\"><strong>".DRGL_NOWZH."</strong></td>", $base_indent+10);
		doOutput("</tr>", $base_indent+8);
	}
doOutput("</table><br /><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">", $base_indent+6);
	doOutput('<tr>', $base_indent+8);
		doOutput('<td>'.geefButton("location.href='".SITE_URL."/drgls/'", 'Archief bijzondere ritten', '', 'G', true, true, '', '', true).'</td>', $base_indent+10);
		if ($session->allowed(33)) {
			doOutput('<td>'.geefButton("document.location='".SITE_URL."/index.php?blokid=33'", 'Administratie', '', '', true, true, '', '', true).'</td>', $base_indent+10);
		}
	doOutput("</tr>", $base_indent+8);
doOutput("</table>", $base_indent+6);
doContentPanelFooter();
?>