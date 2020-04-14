<?php
if (!defined('PAGE_TYPE')) { exit(); }

doContentpanelHeader(CONTENTPANELS_FOUTESPOTS);
doOutput('<table cellspacing="0" cellpadding="0" border="0" align="center">', $base_indent+6);
	doOutput('<tr valign="baseline"><td class="contentpanel" colspan="3" align="center">'.CP_FOUTESPOTS_BOLD.'</td></tr>', $base_indent+8);
	$post_counter = 0;
	$query = 'select d.discussionid, concat(p.date, \' \', p.time), p.authorid, u.username, u.name, d.title, d.locked, case when count(r.uid)=count(p.postid) then 1 else null end
			from '.DB_PREFIX.'_forum_discussion d
			join '.DB_PREFIX.'_forum_posts p on d.discussionid=p.discussionid
			join '.DB_PREFIX.'_forum_posts_text t on t.postid=p.postid
			join '.DB_PREFIX.'_users u on u.uid=p.authorid
			join '.DB_PREFIX.'_forum_forums f on d.forumid=f.forumid
			left join '.DB_PREFIX.'_forum_read_'.substr($session->uid, -1).' r on r.postid=p.postid and r.uid='.$session->uid.'
			where (f.type=\'0\' or f.type=\'1\') and f.catid='.geefConfig('foutespots_forum_cat').'
			group by d.discussionid, p.date, p.time, p.authorid, u.username, u.name, d.title, d.locked
			order by p.date desc, p.time desc';
	$dbset_posts = $db->query($query);

    $query = 'SELECT p.discussionid
			    FROM '.DB_PREFIX.'_forum_discussion d
			    JOIN '.DB_PREFIX.'_forum_posts p ON p.discussionid=d.discussionid
			    JOIN '.DB_PREFIX.'_forum_forums f ON f.forumid=d.forumid
				JOIN '.DB_PREFIX.'_forum_zoeken_lijst zl ON zl.post_id = p.postid
				JOIN '.DB_PREFIX.'_forum_zoeken_woorden zw ON zw.woord_id = zl.woord_id
			    WHERE p.authorid > 0 AND f.catid='.geefConfig('foutespots_forum_cat').' and zw.woord = \'' . $session->username . '\'
				    AND p.postid NOT IN (SELECT r.postid FROM '.DB_PREFIX.'_forum_read_'.substr($session->uid, -1).' r WHERE r.uid='.$session->uid.')
			    GROUP BY p.discussionid';
	$dbset_contains_username = $db->query($query);
	$disc_contains_username = array();
	while (list($disc_id) = $db->fetchRow($dbset_contains_username)) {
		$disc_contains_username[$disc_id] = true;
	}

	$posts_username = array();
	$posts_normal   = array();
	while (list($post_discid, $post_date_time, $post_authorid, $post_username, $post_name, $post_title, $locked, $read) = $db->fetchRow($dbset_posts)) {
		if (isset($posts_done)) {
			if (isset($posts_done[$post_discid])) {
				if ($posts_done[$post_discid]!=true) {
					$do = true;
					++$post_counter;
				} else {
					$do = false;
				}
			} else {
				$do = true;
				++$post_counter;
			}
		} else {
			$do = true;
			++$post_counter;
		}
		if ($post_counter>$params['max_foutespots']) {
			$do = false;
		}

		if ($do) {
			$posts_done[$post_discid] = true;

			$out = '<tr valign="baseline">';
				$out .= '<td align="right" nowrap>';
				if (substr($post_date_time,0,10)==date('Y-m-d')) {
					$out .= '<strong>Vandaag</strong>&nbsp;'.substr($post_date_time,11,8).'&nbsp;</td>';
				} else {
					$out .= geefDatumWeergave(substr($post_date_time,0,10), true, false).'&nbsp;'.substr($post_date_time,11,8).'&nbsp;</td>';
				}
				if ($session->logged_in) {
					$out .= '<td align="center">'.forumImage($locked, false, !($read)).'</td>';
				}
				$out .= '<td>&nbsp;';
				if (strlen($post_title)>40) {
					$display_post_title = trim(substr($post_title,0,38)).'...';
				} else {
					$display_post_title = $post_title;
				}
				if (isset($disc_contains_username[$post_discid])) { $display_post_title = '<strong>'.$display_post_title.'</strong>'; }
				$out .= '<a href="'.SITE_URL.'/forum/'.$post_discid.'/'.urlencode(str_replace('/', '', $post_title)).'/" '.giveMouseOver($post_title.'<br />Laatste post door '.$post_username, true).'>'.$display_post_title.'</a>';
				$out .= '</td>';
			$out .= '</tr>';

			if (isset($disc_contains_username[$post_discid])) {
				$posts_username[] = $out;
			} else {
				$posts_normal[] = $out;
			}
		}
	}
	foreach($posts_username as $post) {
		doOutput($post, $base_indent+8);
	}
	foreach($posts_normal as $post) {
		doOutput($post, $base_indent+8);
	}
doOutput('</table><br /><table cellspacing="0" cellpadding="0" border="0" align="center"><tr>', $base_indent+6);
	doOutput('<td align="center">'.geefButton('document.location=\''.SITE_URL.'/forum/c'.geefConfig('foutespots_forum').'/\'', 'Naar het Foute Spots forum overzicht', '', '', true, true, '', '', true).'</td>', $base_indent+8);
doOutput('</table>', $base_indent+6);
doContentPanelFooter();
?>