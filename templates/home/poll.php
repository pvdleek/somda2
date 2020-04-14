<?php
if (!defined('PAGE_TYPE')) { exit(); }

$query = "select pollid from ".DB_PREFIX."_poll where date>='".date("Y-m-d")."'";
$dbset_polls = $db->query($query);
if (list($pollid) = $db->fetchRow($dbset_polls)) {
	$query = "select vote from ".DB_PREFIX."_poll_votes where pollid=".$pollid." and uid=".$session->uid;
	$dbset_voted = $db->query($query);
	if ($db->numRows($dbset_voted)>0 && $params["display_poll"]=="0") {
		// Do not display poll, already voted
	} else {
		doContentpanelHeader(CONTENTPANELS_POLL);
		if (isset($poll_op) && substr($poll_op,0,9)=="poll_vote") {
			// Sla stem op
			if ($pollid>0 && $session->logged_in && isset($poll_vote) && $poll_vote>0) {
				$query = "insert into ".DB_PREFIX."_poll_votes (pollid, uid, vote)
						values (".$pollid.", ".$session->uid.", '".($poll_vote-1)."')";
				$db->queryF($query);

				echo "<script language=\"javascript\" type=\"text/javascript\">";
					echo "document.location.replace('".SITE_URL."');";
				echo "</script>";
				exit();
			}
		}
		doOutput("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">", $base_indent+6);
			$doButton = false;
			$query = "select pollid, question, opt_a, opt_b, opt_c, opt_d, date from ".DB_PREFIX."_poll where date>='".date("Y-m-d")."'";
			$dbset_polldata = $db->query($query);
			list($pollid, $question, $opt[0], $opt[1], $opt[2], $opt[3], $date) = $db->fetchRow($dbset_polldata);
			if ($session->logged_in) {
				if (isset($poll_op) && substr($poll_op,0,9)=="poll_vote" || $db->numRows($dbset_voted)>0) {
					doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\"><i>".POLL_VOTED."</i></td></tr>", $base_indent+8);
					doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\">&nbsp;</td></tr>", $base_indent+8);
					doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\"><b>".$question."</b></td></tr>", $base_indent+8);
					for ($j=0; $j<4; ++$j) {
						if (strlen($opt[$j])>0) {
							doOutput("<tr valign=\"baseline\">", $base_indent+8);
								doOutput("<td class=\"contentpanel\" align=\"right\">".$opt[$j]."</td>", $base_indent+10);
								$percentage = geefPollPercentage($pollid, $j);
								doOutput("<td class=\"contentpanel\" align=\"center\" nowrap>(".$percentage."%)</td>", $base_indent+10);
								doOutput("<td class=\"contentpanel\" align=\"left\" nowrap>", $base_indent+10);
									doOutput("<img src=\"".IMAGES_URL."/poll/opt".$j.".gif\" border=\"0\" width=\"".$percentage."\" height=\"10\" alt=\"".$opt[$j]."\" />", $base_indent+12);
								doOutput("</td>", $base_indent+10);
							doOutput("</tr>", $base_indent+8);
						}
					}
				} else {
					$doButton = true;
					doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\"><i>".POLL_PLEASEVOTE."</i></td></tr>", $base_indent+8);
					doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\">&nbsp;</td></tr>", $base_indent+8);
					doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\">", $base_indent+8);
						doOutput("<input type=\"hidden\" name=\"poll_op\" value=\"poll_vote\"><b>".$question."</b>", $base_indent+10);
					doOutput("</td></tr>", $base_indent+8);
					for ($j=0; $j<4; ++$j) {
						if (strlen($opt[$j])>0) {
							doOutput("<tr valign=\"baseline\">", $base_indent+8);
								doOutput("<td class=\"contentpanel\" align=\"right\" nowrap>", $base_indent+10);
									doOutput("<input type=\"radio\" name=\"poll_vote\" value=\"".($j+1)."\" tabindex=\"".giveTabIndex()."\">", $base_indent+12);
								doOutput("</td>", $base_indent+10);
								doOutput("<td class=\"contentpanel\" align=\"left\">&nbsp;".$opt[$j]."</td>", $base_indent+10);
							doOutput("</tr>", $base_indent+8);
						}
					}
				}
			} else {
				doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\"><i>".POLL_GUEST."</i></td></tr>", $base_indent+8);
				doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\">&nbsp;</td></tr>", $base_indent+8);
				doOutput("<tr valign=\"baseline\"><td colspan=\"3\" class=\"contentpanel\" align=\"center\"><b>".$question."</b></td></tr>", $base_indent+8);
				for ($j=0; $j<4; ++$j) {
					if (strlen($opt[$j])>0) {
						doOutput("<tr valign=\"baseline\">", $base_indent+8);
							doOutput("<td class=\"contentpanel\" align=\"right\">".$opt[$j]."</td>", $base_indent+10);
							$percentage = geefPollPercentage($pollid, $j);
							doOutput("<td class=\"contentpanel\" align=\"center\" nowrap>(".$percentage."%)</td>", $base_indent+10);
							doOutput("<td class=\"contentpanel\" align=\"left\" nowrap>", $base_indent+10);
								doOutput("<img src=\"".IMAGES_URL."/poll/opt".$j.".gif\" border=\"0\" width=\"".$percentage."\" height=\"10\" alt=\"".$opt[$j]."\" />", $base_indent+12);
							doOutput("</td>", $base_indent+10);
						doOutput("</tr>", $base_indent+8);
					}
				}
			}
		doOutput('</table>', $base_indent+6);
		if ($doButton) {
			doOutput("<br /><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\"><tr>", $base_indent+4);
				doOutput("<td>".geefButton("document.MasterForm.submit();", 'Stem')."</td>", $base_indent+6);
			doOutput("</tr></table>", $base_indent+4);
		}
		$query = "select count(*) from ".DB_PREFIX."_poll_votes where pollid=".$pollid;
		$dbset_nrvotes = $db->query($query);
		list($nr_of_votes) = $db->fetchRow($dbset_nrvotes);
		doOutput("<div align=\"center\"><i>", $base_indent+6);
			switch($nr_of_votes) {
				case 0:
					doOutput(sprintf(POLL_EXPIRES_NOVOTES, geefDatumWeergave($date, true)), $base_indent+8);
					break;
				case 1:
					doOutput(sprintf(POLL_EXPIRES_ONEVOTE, geefDatumWeergave($date, true)), $base_indent+8);
					break;
				default:
					doOutput(sprintf(POLL_EXPIRES, $nr_of_votes, geefDatumWeergave($date, true)), $base_indent+8);
					break;
			}
		doOutput("</i></div>", $base_indent+6);
		doContentPanelFooter();
	}
}

function geefPollPercentage($pollid, $opt_nr) {
	global $db;

	$query = "select count(*), count(if(vote='".$opt_nr."', 1, NULL))
			from ".DB_PREFIX."_poll_votes
			where pollid=".$pollid;
	$dbset_votes = $db->query($query);
	list($total_votes, $opt_votes) = $db->fetchRow($dbset_votes);
	if ($total_votes>0) {
		return (round(100*$opt_votes/$total_votes));
	} else {
		return 0;
	}
}
?>