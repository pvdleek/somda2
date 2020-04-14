<?php
if (!defined('PAGE_TYPE')) { exit(); }

doContentpanelHeader('Spoornieuws');
doOutput('<table class="blog" align="center">', $base_indent+6);
	$query = 'select sns_bron, sns_titel, sns_url, sns_introductie, sns_datumtijd
			from '.DB_PREFIX.'_sns_spoor_nieuws
			where sns_actief=\'1\' and sns_gekeurd=\'1\'
			order by sns_datumtijd desc
			limit 5';
	$dbset_news = $db->query($query);
	list($sns_bron, $sns_titel, $sns_url, $sns_introductie, $sns_datumtijd) = $db->fetchRow($dbset_news);
	$sns_bron = geefNieuwsLogo($sns_bron);
	doOutput('<tr>', $base_indent+8);
		doOutput('<td valign="top"><div>', $base_indent+10);
			doOutput('<table class="contentpaneopen"><tr><td class="contentheading" style="padding-right:5px; width:auto;">'.$sns_bron.'</td><td class="contentheading" width="100%"><a href="'.htmlentities($sns_url).'" target="_blank" class="contentpagetitle">'.$sns_titel.'</a></td></tr></table>', $base_indent+12);
			doOutput('<table class="contentpaneopen">', $base_indent+12);
				doOutput('<tr><td valign="top" colspan="2" class="createdate">'.geefDatumWeergave(date('Y-m-d', $sns_datumtijd)).' '.date('H:i', $sns_datumtijd).'</td></tr>', $base_indent+14);
				doOutput('<tr><td valign="top" colspan="2">'.$sns_introductie.'<br /><a href="'.htmlentities($sns_url).'" target="_blank">Lees verder...</a></td></tr>', $base_indent+14);
			doOutput('</table>', $base_indent+12);
			doOutput('<span class="article_separator">&nbsp;</span>', $base_indent+12);
		doOutput('</div></td>', $base_indent+10);
	doOutput('</tr><tr>', $base_indent+8);
		doOutput('<td valign="top"><div class="blog_more">', $base_indent+10);
			doOutput('<div><strong><a href="/nieuws_home/">Meer spoornieuws...</a></strong></div>', $base_indent+12);
			doOutput('<ul>', $base_indent+12);
				while (list($sns_bron, $sns_titel, $sns_url, $sns_introductie, $sns_datumtijd) = $db->fetchRow($dbset_news)) {
					doOutput('<li><a class="blogsection" href="'.htmlentities($sns_url).'" target="_blank">'.$sns_titel.'</a></li>', $base_indent+14);
				}
			doOutput('</ul>', $base_indent+12);
		doOutput('</div></td>', $base_indent+10);
	doOutput('</tr>', $base_indent+8);
doOutput('</table>', $base_indent+6);
doContentPanelFooter();
?>