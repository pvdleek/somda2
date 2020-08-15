--
-- Tabelstructuur voor tabel `migration_versions`
--

CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ofo_official_footnote`
--

CREATE TABLE IF NOT EXISTS `ofo_official_footnote` (
  `ofo_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ofo_footnote_id` bigint(20) NOT NULL,
  `ofo_date` date NOT NULL,
  PRIMARY KEY (`ofo_id`),
  UNIQUE KEY `idx_ofo_footnote` (`ofo_footnote_id`,`ofo_date`) USING BTREE,
  KEY `idx_ofo_footnote_id` (`ofo_footnote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ott_official_train_table`
--

CREATE TABLE IF NOT EXISTS `ott_official_train_table` (
  `ott_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ott_ofo_id` bigint(20) DEFAULT NULL,
  `ott_transporter_id` bigint(20) DEFAULT NULL,
  `ott_characteristic_id` bigint(20) DEFAULT NULL,
  `ott_route_id` bigint(20) DEFAULT NULL,
  `ott_location_id` bigint(20) DEFAULT NULL,
  `ott_order` int(11) NOT NULL DEFAULT '1',
  `ott_action` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `ott_time` int(11) DEFAULT NULL,
  `ott_track` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ott_id`),
  KEY `IDX_4577F52E2EDDB7B4` (`ott_transporter_id`),
  KEY `IDX_4577F52EBE696BF` (`ott_characteristic_id`),
  KEY `idx_ott_time` (`ott_time`),
  KEY `idx_ott_location_id` (`ott_location_id`),
  KEY `idx_ott_route_id` (`ott_route_id`),
  KEY `idx_ott_ofo_id` (`ott_ofo_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_api_logging`
--

CREATE TABLE IF NOT EXISTS `somda_api_logging` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `station` varchar(10) DEFAULT NULL,
  `tijd` varchar(5) DEFAULT NULL,
  `dagnr` int(11) DEFAULT NULL,
  `resultaat_id` int(11) DEFAULT NULL,
  `datumtijd` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_banner`
--

CREATE TABLE IF NOT EXISTS `somda_banner` (
  `bannerid` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(6) DEFAULT NULL,
  `location` varchar(6) NOT NULL DEFAULT 'header',
  `description` longtext,
  `link` varchar(100) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `customerid` bigint(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `max_views` int(11) NOT NULL DEFAULT '0',
  `max_hits` int(11) NOT NULL DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bannerid`),
  KEY `IDX_D93888C264FBF307` (`customerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_banner_customer`
--

CREATE TABLE IF NOT EXISTS `somda_banner_customer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(6) NOT NULL,
  `max_views` int(11) DEFAULT NULL,
  `max_hits` int(11) DEFAULT NULL,
  `max_days` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_banner_customer_user`
--

CREATE TABLE IF NOT EXISTS `somda_banner_customer_user` (
  `id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `allowed_new` tinyint(1) NOT NULL DEFAULT '0',
  `allowed_max_views` tinyint(1) NOT NULL DEFAULT '0',
  `allowed_max_hits` tinyint(1) NOT NULL DEFAULT '0',
  `allowed_max_date` tinyint(1) NOT NULL DEFAULT '0',
  `allowed_deactivate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`uid`),
  KEY `IDX_C9A88E10BF396750` (`id`),
  KEY `IDX_C9A88E10539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_banner_hits`
--

CREATE TABLE IF NOT EXISTS `somda_banner_hits` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bannerid` bigint(20) DEFAULT NULL,
  `timestamp` datetime NOT NULL,
  `ip_address` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bannerid` (`bannerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_banner_views`
--

CREATE TABLE IF NOT EXISTS `somda_banner_views` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bannerid` bigint(20) DEFAULT NULL,
  `ip` bigint(20) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_47831_bannerid` (`bannerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_blokken`
--

CREATE TABLE IF NOT EXISTS `somda_blokken` (
  `blokid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL DEFAULT '',
  `route` varchar(45) NOT NULL DEFAULT '',
  `menu_volgorde` int(11) NOT NULL DEFAULT '1',
  `parent_block` bigint(20) DEFAULT NULL,
  `do_separator` tinyint(1) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`blokid`),
  KEY `FK_B4865B064F2A0381` (`parent_block`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_ddar`
--

CREATE TABLE IF NOT EXISTS `somda_ddar` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `matid` bigint(20) DEFAULT NULL,
  `stam` int(11) DEFAULT NULL,
  `afkid` bigint(20) DEFAULT NULL,
  `spot_ander_laatste` date DEFAULT NULL,
  `spot_eerste` date NOT NULL,
  `spot_laatste` date DEFAULT NULL,
  `spot_ander_eerste` date DEFAULT NULL,
  `extra` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_47846_matid` (`matid`),
  KEY `IDX_9A508BFC65F5051` (`afkid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_don_donatie`
--

CREATE TABLE IF NOT EXISTS `somda_don_donatie` (
  `don_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `don_datetime` bigint(20) NOT NULL,
  `don_uid` bigint(20) DEFAULT NULL,
  `don_amount` bigint(20) NOT NULL,
  `don_transaction_id` varchar(32) NOT NULL,
  `don_ok` tinyint(1) NOT NULL,
  PRIMARY KEY (`don_id`),
  KEY `IDX_DE3B771128103CB` (`don_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_drgl`
--

CREATE TABLE IF NOT EXISTS `somda_drgl` (
  `drglid` bigint(20) NOT NULL AUTO_INCREMENT,
  `pubdatum` datetime DEFAULT NULL,
  `datum` date NOT NULL,
  `einddatum` date DEFAULT NULL,
  `title` varchar(75) NOT NULL,
  `image` varchar(20) NOT NULL,
  `text` longtext NOT NULL,
  `werkzaamheden` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL,
  PRIMARY KEY (`drglid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_drgl_read`
--

CREATE TABLE IF NOT EXISTS `somda_drgl_read` (
  `drglid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  PRIMARY KEY (`drglid`,`uid`),
  KEY `IDX_7CF8CCE9B869D711` (`drglid`),
  KEY `IDX_7CF8CCE9539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_alerts`
--

CREATE TABLE IF NOT EXISTS `somda_forum_alerts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postid` bigint(20) DEFAULT NULL,
  `senderid` bigint(20) DEFAULT NULL,
  `comment` longtext,
  `closed` tinyint(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_47886_postid` (`postid`),
  KEY `IDX_A2F3B42C65525B5F` (`senderid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_alerts_notes`
--

CREATE TABLE IF NOT EXISTS `somda_forum_alerts_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `alertid` bigint(20) DEFAULT NULL,
  `authorid` bigint(20) DEFAULT NULL,
  `text` longtext NOT NULL,
  `sent_to_reporter` tinyint(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_47898_alertid` (`alertid`),
  KEY `IDX_502511CE3412DD5F` (`authorid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_cats`
--

CREATE TABLE IF NOT EXISTS `somda_forum_cats` (
  `catid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `volgorde` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_discussion`
--

CREATE TABLE IF NOT EXISTS `somda_forum_discussion` (
  `discussionid` bigint(20) NOT NULL AUTO_INCREMENT,
  `forumid` bigint(20) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `authorid` bigint(20) DEFAULT NULL,
  `viewed` bigint(20) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`discussionid`),
  KEY `idx_47915_forumid` (`forumid`),
  KEY `IDX_64C2DF7E3412DD5F` (`authorid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_discussion_wiki`
--

CREATE TABLE IF NOT EXISTS `somda_forum_discussion_wiki` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `discussionid` bigint(20) DEFAULT NULL,
  `wiki` varchar(50) NOT NULL,
  `titel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_47927_discussionid` (`discussionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_favorites`
--

CREATE TABLE IF NOT EXISTS `somda_forum_favorites` (
  `discussionid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `alerting` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`discussionid`,`uid`),
  KEY `IDX_4E8B7C93FCC0F19E` (`discussionid`),
  KEY `IDX_4E8B7C93539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_forums`
--

CREATE TABLE IF NOT EXISTS `somda_forum_forums` (
  `forumid` bigint(20) NOT NULL AUTO_INCREMENT,
  `catid` bigint(20) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `description` varchar(100) NOT NULL,
  `volgorde` int(11) NOT NULL DEFAULT '1',
  `type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`forumid`),
  KEY `idx_47937_catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_log`
--

CREATE TABLE IF NOT EXISTS `somda_forum_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postid` bigint(20) DEFAULT NULL,
  `actie` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_256DFB117510F6AF` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_mods`
--

CREATE TABLE IF NOT EXISTS `somda_forum_mods` (
  `forumid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  PRIMARY KEY (`forumid`,`uid`),
  KEY `IDX_E20AB6A4EDB4D5F3` (`forumid`),
  KEY `IDX_E20AB6A4539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_posts`
--

CREATE TABLE IF NOT EXISTS `somda_forum_posts` (
  `postid` bigint(20) NOT NULL AUTO_INCREMENT,
  `authorid` bigint(20) DEFAULT NULL,
  `discussionid` bigint(20) DEFAULT NULL,
  `edit_uid` bigint(20) DEFAULT NULL,
  `edit_reason` varchar(50) DEFAULT NULL,
  `wiki_check` int(11) NOT NULL,
  `wiki_uid` bigint(20) DEFAULT NULL,
  `sign_on` tinyint(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  `edit_timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`postid`),
  KEY `idx_47961_authorid` (`authorid`),
  KEY `idx_47961_discussionid` (`discussionid`),
  KEY `IDX_40FD24D9ECDC13D` (`edit_uid`),
  KEY `IDX_40FD24D143C6493` (`wiki_uid`),
  KEY `idx_47961_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_posts_text`
--

CREATE TABLE IF NOT EXISTS `somda_forum_posts_text` (
  `postid` bigint(20) NOT NULL,
  `text` longtext NOT NULL,
  `new_style` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_0`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_0` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`uid`,`postid`),
  KEY `somda_forum_read_0_idx_uid` (`uid`),
  KEY `IDX_forum_read_0` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_1`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_1` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_1_idx_uid` (`uid`),
  KEY `IDX_forum_read_1` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_2`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_2` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_2_idx_uid` (`uid`),
  KEY `IDX_forum_read_2` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_3`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_3` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_3_idx_uid` (`uid`),
  KEY `IDX_forum_read_3` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_4`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_4` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_4_idx_uid` (`uid`),
  KEY `IDX_forum_read_4` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_5`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_5` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_5_idx_uid` (`uid`),
  KEY `IDX_forum_read_5` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_6`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_6` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_6_idx_uid` (`uid`),
  KEY `IDX_forum_read_6` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_7`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_7` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_7_idx_uid` (`uid`),
  KEY `IDX_forum_read_7` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_8`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_8` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_8_idx_uid` (`uid`),
  KEY `IDX_forum_read_8` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_read_9`
--

CREATE TABLE IF NOT EXISTS `somda_forum_read_9` (
  `uid` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  PRIMARY KEY (`postid`,`uid`),
  KEY `somda_forum_read_9_idx_uid` (`uid`),
  KEY `IDX_forum_read_9` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_zoeken_lijst`
--

CREATE TABLE IF NOT EXISTS `somda_forum_zoeken_lijst` (
  `woord_id` bigint(20) NOT NULL,
  `postid` bigint(20) NOT NULL,
  `titel` tinyint(1) NOT NULL,
  PRIMARY KEY (`woord_id`,`postid`),
  KEY `IDX_C9D9A41EE9BD09BA` (`woord_id`),
  KEY `IDX_C9D9A41E7510F6AF` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_forum_zoeken_woorden`
--

CREATE TABLE IF NOT EXISTS `somda_forum_zoeken_woorden` (
  `woord_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `woord` varchar(50) NOT NULL,
  PRIMARY KEY (`woord_id`),
  UNIQUE KEY `idx_48035_woord` (`woord`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_groups`
--

CREATE TABLE IF NOT EXISTS `somda_groups` (
  `groupid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_help`
--

CREATE TABLE IF NOT EXISTS `somda_help` (
  `contentid` bigint(20) NOT NULL AUTO_INCREMENT,
  `titel` tinytext NOT NULL,
  `template` tinytext NOT NULL,
  PRIMARY KEY (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_help_text`
--

CREATE TABLE IF NOT EXISTS `somda_help_text` (
  `blokid` bigint(20) NOT NULL,
  `text` longtext NOT NULL,
  `google_channel` varchar(10) NOT NULL,
  `ad_code` longtext NOT NULL,
  PRIMARY KEY (`blokid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_jargon`
--

CREATE TABLE IF NOT EXISTS `somda_jargon` (
  `jargonid` bigint(20) NOT NULL AUTO_INCREMENT,
  `term` varchar(15) NOT NULL,
  `image` varchar(20) NOT NULL,
  `description` varchar(150) NOT NULL,
  PRIMARY KEY (`jargonid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_karakteristiek`
--

CREATE TABLE IF NOT EXISTS `somda_karakteristiek` (
  `karakteristiek_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `naam` varchar(5) NOT NULL,
  `omschrijving` varchar(25) NOT NULL,
  PRIMARY KEY (`karakteristiek_id`),
  UNIQUE KEY `idx_48102_omschrijving` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_logging`
--

CREATE TABLE IF NOT EXISTS `somda_logging` (
  `logid` bigint(20) NOT NULL AUTO_INCREMENT,
  `datumtijd` datetime NOT NULL,
  `uid` bigint(20) DEFAULT NULL,
  `ip` bigint(20) NOT NULL,
  `route` varchar(255) NOT NULL,
  `route_parameters` longtext NOT NULL COMMENT '(DC2Type:array)',
  `memory_usage` double DEFAULT NULL,
  `duration` double DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `IDX_8127138D539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_mat`
--

CREATE TABLE IF NOT EXISTS `somda_mat` (
  `matid` bigint(20) NOT NULL AUTO_INCREMENT,
  `nummer` varchar(20) NOT NULL,
  `naam` varchar(35) DEFAULT NULL,
  `vervoerder_id` bigint(20) DEFAULT NULL,
  `pattern_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`matid`),
  UNIQUE KEY `idx_48117_nummer` (`nummer`),
  KEY `idx_48117_vervoerder_id` (`vervoerder_id`),
  KEY `FK_355CF79F734A20F` (`pattern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_mat_changes`
--

CREATE TABLE IF NOT EXISTS `somda_mat_changes` (
  `matsmsid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `datum` datetime NOT NULL,
  `bak1` varchar(15) DEFAULT NULL,
  `bak2` varchar(15) DEFAULT NULL,
  `bak3` varchar(15) DEFAULT NULL,
  `bak4` varchar(15) DEFAULT NULL,
  `bak5` varchar(15) DEFAULT NULL,
  `bak6` varchar(15) DEFAULT NULL,
  `bak7` varchar(15) DEFAULT NULL,
  `bak8` varchar(15) DEFAULT NULL,
  `bak9` varchar(15) DEFAULT NULL,
  `bak10` varchar(15) DEFAULT NULL,
  `bak11` varchar(15) DEFAULT NULL,
  `bak12` varchar(15) DEFAULT NULL,
  `bak13` varchar(15) DEFAULT NULL,
  `opmerkingen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`matsmsid`,`uid`),
  KEY `IDX_C6C1DF0D4CD774E2` (`matsmsid`),
  KEY `IDX_C6C1DF0D539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_mat_patterns`
--

CREATE TABLE IF NOT EXISTS `somda_mat_patterns` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `volgorde` int(11) NOT NULL DEFAULT '1',
  `pattern` varchar(80) NOT NULL DEFAULT '',
  `naam` varchar(50) NOT NULL DEFAULT '',
  `tekening` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_48139_volgorde` (`volgorde`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_mat_sms`
--

CREATE TABLE IF NOT EXISTS `somda_mat_sms` (
  `matsmsid` bigint(20) NOT NULL AUTO_INCREMENT,
  `typeid` bigint(20) DEFAULT NULL,
  `bak1` varchar(15) DEFAULT NULL,
  `bak2` varchar(15) DEFAULT NULL,
  `bak3` varchar(15) DEFAULT NULL,
  `bak4` varchar(15) DEFAULT NULL,
  `bak5` varchar(15) DEFAULT NULL,
  `bak6` varchar(15) DEFAULT NULL,
  `bak7` varchar(15) DEFAULT NULL,
  `bak8` varchar(15) DEFAULT NULL,
  `bak9` varchar(15) DEFAULT NULL,
  `bak10` varchar(15) DEFAULT NULL,
  `bak11` varchar(15) DEFAULT NULL,
  `bak12` varchar(15) DEFAULT NULL,
  `bak13` varchar(15) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `opmerkingen` varchar(255) DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `index_regel` tinyint(1) NOT NULL,
  PRIMARY KEY (`matsmsid`),
  KEY `idx_48145_typeid` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_mat_types`
--

CREATE TABLE IF NOT EXISTS `somda_mat_types` (
  `typeid` bigint(20) NOT NULL AUTO_INCREMENT,
  `omschrijving` varchar(25) NOT NULL,
  `bak1` varchar(25) DEFAULT NULL,
  `bak2` varchar(25) DEFAULT NULL,
  `bak3` varchar(25) DEFAULT NULL,
  `bak4` varchar(25) DEFAULT NULL,
  `bak5` varchar(25) DEFAULT NULL,
  `bak6` varchar(25) DEFAULT NULL,
  `bak7` varchar(25) DEFAULT NULL,
  `bak8` varchar(25) DEFAULT NULL,
  `bak9` varchar(25) DEFAULT NULL,
  `bak10` varchar(25) DEFAULT NULL,
  `bak11` varchar(25) DEFAULT NULL,
  `bak12` varchar(25) DEFAULT NULL,
  `bak13` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_news`
--

CREATE TABLE IF NOT EXISTS `somda_news` (
  `newsid` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `text` longtext NOT NULL,
  `timestamp` datetime NOT NULL,
  `archief` tinyint(1) NOT NULL,
  PRIMARY KEY (`newsid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_news_read`
--

CREATE TABLE IF NOT EXISTS `somda_news_read` (
  `uid` bigint(20) NOT NULL,
  `newsid` bigint(20) NOT NULL,
  PRIMARY KEY (`newsid`,`uid`),
  KEY `IDX_AF652C58C510C37` (`newsid`),
  KEY `IDX_AF652C5539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_poll`
--

CREATE TABLE IF NOT EXISTS `somda_poll` (
  `pollid` bigint(20) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL,
  `opt_a` varchar(150) NOT NULL,
  `opt_b` varchar(150) NOT NULL,
  `opt_c` varchar(150) NOT NULL,
  `opt_d` varchar(150) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`pollid`),
  KEY `idx_48191_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_poll_votes`
--

CREATE TABLE IF NOT EXISTS `somda_poll_votes` (
  `pollid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `vote` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollid`,`uid`),
  KEY `IDX_75CFE3876F5F43AE` (`pollid`),
  KEY `IDX_75CFE387539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_positie`
--

CREATE TABLE IF NOT EXISTS `somda_positie` (
  `posid` bigint(20) NOT NULL AUTO_INCREMENT,
  `positie` varchar(2) NOT NULL,
  PRIMARY KEY (`posid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_prefs`
--

CREATE TABLE IF NOT EXISTS `somda_prefs` (
  `prefid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sleutel` varchar(25) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(90) NOT NULL,
  `default_value` varchar(200) NOT NULL,
  `volgorde` int(11) NOT NULL,
  PRIMARY KEY (`prefid`),
  KEY `idx_48215_sleutel` (`sleutel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_rechten`
--

CREATE TABLE IF NOT EXISTS `somda_rechten` (
  `blokid` bigint(20) NOT NULL,
  `groupid` bigint(20) NOT NULL,
  PRIMARY KEY (`blokid`,`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_rijdagen`
--

CREATE TABLE IF NOT EXISTS `somda_rijdagen` (
  `rijdagenid` bigint(20) NOT NULL AUTO_INCREMENT,
  `ma` tinyint(1) NOT NULL,
  `di` tinyint(1) NOT NULL,
  `wo` tinyint(1) NOT NULL,
  `do` tinyint(1) NOT NULL,
  `vr` tinyint(1) NOT NULL,
  `za` tinyint(1) NOT NULL,
  `zf` tinyint(1) NOT NULL,
  PRIMARY KEY (`rijdagenid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_session`
--

CREATE TABLE IF NOT EXISTS `somda_session` (
  `sess_id` varbinary(128) NOT NULL,
  `sess_data` blob NOT NULL,
  `sess_lifetime` int(10) UNSIGNED NOT NULL,
  `sess_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`sess_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_sht_shout`
--

CREATE TABLE IF NOT EXISTS `somda_sht_shout` (
  `sht_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sht_uid` bigint(20) DEFAULT NULL,
  `sht_ip` bigint(20) NOT NULL,
  `sht_datumtijd` datetime NOT NULL,
  `sht_text` varchar(255) NOT NULL,
  PRIMARY KEY (`sht_id`),
  KEY `IDX_88E10AFB97AD1E10` (`sht_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_snb_spoor_nieuws_bron`
--

CREATE TABLE IF NOT EXISTS `somda_snb_spoor_nieuws_bron` (
  `snb_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `snb_bron` varchar(7) NOT NULL,
  `snb_logo` varchar(25) NOT NULL,
  `snb_url` varchar(30) NOT NULL,
  `snb_description` varchar(100) NOT NULL,
  PRIMARY KEY (`snb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_snf_spoor_nieuws_bron_feed`
--

CREATE TABLE IF NOT EXISTS `somda_snf_spoor_nieuws_bron_feed` (
  `snf_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `snf_snb_id` bigint(20) DEFAULT NULL,
  `snf_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `snf_filter_results` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`snf_id`),
  KEY `IDX_8A257AA6AD7A950` (`snf_snb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_sns_spoor_nieuws`
--

CREATE TABLE IF NOT EXISTS `somda_sns_spoor_nieuws` (
  `sns_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sns_titel` varchar(100) NOT NULL,
  `sns_url` varchar(255) NOT NULL,
  `sns_introductie` longtext NOT NULL,
  `sns_snb_id` bigint(20) DEFAULT NULL,
  `sns_actief` tinyint(1) NOT NULL DEFAULT '1',
  `sns_gekeurd` tinyint(1) NOT NULL,
  `sns_bijwerken_ok` tinyint(1) NOT NULL DEFAULT '1',
  `sns_timestamp` datetime NOT NULL,
  PRIMARY KEY (`sns_id`),
  KEY `FRK_sns_snb_id` (`sns_snb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_spots`
--

CREATE TABLE IF NOT EXISTS `somda_spots` (
  `spotid` bigint(20) NOT NULL AUTO_INCREMENT,
  `treinid` bigint(20) DEFAULT NULL,
  `posid` bigint(20) DEFAULT NULL,
  `locatieid` bigint(20) DEFAULT NULL,
  `matid` bigint(20) DEFAULT NULL,
  `uid` bigint(20) DEFAULT NULL,
  `datum` datetime NOT NULL,
  `timestamp` datetime NOT NULL,
  `input_feedback_flag` int(11) NOT NULL,
  PRIMARY KEY (`spotid`),
  UNIQUE KEY `idx_48259_treinid` (`treinid`,`posid`,`locatieid`,`matid`,`uid`,`datum`),
  KEY `idx_48259_datum` (`datum`),
  KEY `idx_48259_matid` (`matid`),
  KEY `idx_48259_uid` (`uid`),
  KEY `IDX_11A6C5C868F454BD` (`treinid`),
  KEY `IDX_11A6C5C8F4E25321` (`posid`),
  KEY `IDX_11A6C5C8D6E3DC6C` (`locatieid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_spots_extra`
--

CREATE TABLE IF NOT EXISTS `somda_spots_extra` (
  `spotid` bigint(20) NOT NULL,
  `extra` varchar(255) NOT NULL,
  `user_extra` varchar(255) NOT NULL,
  PRIMARY KEY (`spotid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_spot_provincie`
--

CREATE TABLE IF NOT EXISTS `somda_spot_provincie` (
  `provincieid` bigint(20) NOT NULL AUTO_INCREMENT,
  `naam` varchar(15) NOT NULL,
  PRIMARY KEY (`provincieid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_spot_punt`
--

CREATE TABLE IF NOT EXISTS `somda_spot_punt` (
  `puntid` bigint(20) NOT NULL AUTO_INCREMENT,
  `provincieid` bigint(20) DEFAULT NULL,
  `naam` varchar(50) NOT NULL,
  `afkid_traject_1` bigint(20) DEFAULT NULL,
  `afkid_traject_2` bigint(20) DEFAULT NULL,
  `afkid_locatie` bigint(20) DEFAULT NULL,
  `afkid_dks` bigint(20) DEFAULT NULL,
  `kilometrering` varchar(25) DEFAULT NULL,
  `gps` varchar(25) DEFAULT NULL,
  `zonstand_winter` varchar(50) DEFAULT NULL,
  `zonstand_zomer` varchar(50) DEFAULT NULL,
  `google_url` varchar(200) DEFAULT NULL,
  `foto` varchar(25) NOT NULL DEFAULT 'geen_foto.jpg',
  PRIMARY KEY (`puntid`),
  KEY `IDX_6164DEDFA5300E5` (`afkid_locatie`),
  KEY `IDX_6164DED2C0A1CCC` (`afkid_traject_1`),
  KEY `IDX_6164DEDB5034D76` (`afkid_traject_2`),
  KEY `IDX_6164DEDE2D85197` (`afkid_dks`),
  KEY `IDX_6164DED8A6CCEB4` (`provincieid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_spot_punt_text`
--

CREATE TABLE IF NOT EXISTS `somda_spot_punt_text` (
  `puntid` bigint(20) NOT NULL,
  `route_auto` longtext NOT NULL,
  `route_ov` longtext NOT NULL,
  `bijzonderheden` longtext NOT NULL,
  PRIMARY KEY (`puntid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_stats`
--

CREATE TABLE IF NOT EXISTS `somda_stats` (
  `datum` datetime NOT NULL,
  `uniek` bigint(20) NOT NULL,
  `pageviews` bigint(20) NOT NULL,
  `pageviews_home` bigint(20) NOT NULL,
  `pageviews_func` bigint(20) NOT NULL,
  `spots` bigint(20) NOT NULL,
  `posts` bigint(20) NOT NULL,
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_date` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_stats_blokken`
--

CREATE TABLE IF NOT EXISTS `somda_stats_blokken` (
  `blokid` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `pageviews` bigint(20) NOT NULL,
  PRIMARY KEY (`blokid`,`date`),
  KEY `IDX_7FAF7B1A711B2385` (`blokid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr`
--

CREATE TABLE IF NOT EXISTS `somda_tdr` (
  `tdrid` bigint(20) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL DEFAULT '1',
  `treinid` bigint(20) DEFAULT NULL,
  `rijdagenid` bigint(20) DEFAULT NULL,
  `locatieid` bigint(20) DEFAULT NULL,
  `actie` varchar(1) NOT NULL DEFAULT '-',
  `tijd` int(11) NOT NULL DEFAULT '0',
  `spoor` varchar(3) DEFAULT NULL,
  `tdr_nr` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`tdrid`),
  KEY `idx_48320_locatieid` (`locatieid`),
  KEY `idx_48320_tijd` (`tijd`),
  KEY `idx_48320_treinid` (`treinid`),
  KEY `IDX_84B606F6AE60685A` (`tdr_nr`),
  KEY `IDX_84B606F687CF3DBF` (`rijdagenid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_drgl`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_drgl` (
  `tdr_nr` bigint(20) NOT NULL AUTO_INCREMENT,
  `naam` varchar(10) NOT NULL,
  `start_datum` date NOT NULL,
  `eind_datum` date NOT NULL,
  PRIMARY KEY (`tdr_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_in`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_in` (
  `tdrid` bigint(20) NOT NULL AUTO_INCREMENT,
  `treinnr` varchar(15) NOT NULL DEFAULT '',
  `treinid` bigint(20) DEFAULT NULL,
  `rijdagenid` bigint(20) DEFAULT NULL,
  `locatie` varchar(100) NOT NULL DEFAULT '',
  `locatieid` bigint(20) DEFAULT NULL,
  `actie` varchar(1) NOT NULL DEFAULT '',
  `tijd` bigint(20) DEFAULT NULL,
  `uid` bigint(20) NOT NULL,
  `tdr_nr` int(11) NOT NULL,
  PRIMARY KEY (`tdrid`),
  UNIQUE KEY `idx_48403_treinnr` (`treinnr`,`rijdagenid`,`locatie`,`tijd`),
  KEY `idx_48403_locatieid` (`locatieid`),
  KEY `idx_48403_tijd` (`tijd`),
  KEY `idx_48403_treinid` (`treinid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_in_s_e`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_in_s_e` (
  `treinid` bigint(20) NOT NULL,
  `dag` bigint(20) NOT NULL,
  `min` bigint(20) DEFAULT NULL,
  `max` bigint(20) DEFAULT NULL,
  `tdr_nr` int(11) NOT NULL,
  PRIMARY KEY (`dag`,`treinid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_route`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_route` (
  `treinnummerlijst_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `volgorde` int(11) NOT NULL DEFAULT '1',
  `locatieid` bigint(20) DEFAULT NULL,
  `tdr_nr` bigint(20) NOT NULL,
  PRIMARY KEY (`tdr_nr`,`treinnummerlijst_id`,`type`,`volgorde`),
  KEY `IDX_1A52615BAE60685A` (`tdr_nr`),
  KEY `IDX_1A52615B9CBF59B5` (`treinnummerlijst_id`),
  KEY `IDX_1A52615BD6E3DC6C` (`locatieid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_s_e`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_s_e` (
  `treinid` bigint(20) NOT NULL,
  `dag` int(11) NOT NULL DEFAULT '1',
  `v_locatieid` bigint(20) DEFAULT NULL,
  `v_actie` varchar(1) NOT NULL DEFAULT '-',
  `v_tijd` int(11) NOT NULL DEFAULT '0',
  `a_locatieid` bigint(20) DEFAULT NULL,
  `a_actie` varchar(1) NOT NULL DEFAULT '-',
  `a_tijd` int(11) NOT NULL DEFAULT '0',
  `tdr_nr` bigint(20) NOT NULL,
  PRIMARY KEY (`tdr_nr`,`treinid`,`dag`),
  KEY `IDX_1BACB963AE60685A` (`tdr_nr`),
  KEY `IDX_1BACB96368F454BD` (`treinid`),
  KEY `IDX_1BACB9635E53C5B` (`v_locatieid`),
  KEY `IDX_1BACB9638228ED13` (`a_locatieid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_treinnummerlijst`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_treinnummerlijst` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nr_start` int(11) NOT NULL DEFAULT '1',
  `nr_eind` int(11) NOT NULL DEFAULT '2',
  `vervoerder_id` bigint(20) DEFAULT NULL,
  `karakteristiek_id` bigint(20) DEFAULT NULL,
  `traject` varchar(75) DEFAULT NULL,
  `tdr_nr` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D7A60660AE60685A` (`tdr_nr`),
  KEY `IDX_D7A6066022A00C2` (`vervoerder_id`),
  KEY `IDX_D7A60660FBDE844F` (`karakteristiek_id`),
  KEY `idx_48381_nr_start` (`nr_start`),
  KEY `idx_48381_nr_eind` (`nr_eind`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_trein_mat`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_trein_mat` (
  `treinid` bigint(20) NOT NULL,
  `posid` bigint(20) NOT NULL,
  `dag` int(11) NOT NULL DEFAULT '1',
  `spots` bigint(20) NOT NULL,
  `tdr_nr` bigint(20) NOT NULL,
  `mat_pattern_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`tdr_nr`,`treinid`,`posid`,`dag`),
  KEY `IDX_C2BF79AAAE60685A` (`tdr_nr`),
  KEY `IDX_C2BF79AA68F454BD` (`treinid`),
  KEY `IDX_C2BF79AAF4E25321` (`posid`),
  KEY `IDX_C2BF79AAE14DDC7E` (`mat_pattern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_tdr_trein_treinnummerlijst`
--

CREATE TABLE IF NOT EXISTS `somda_tdr_trein_treinnummerlijst` (
  `treinid` bigint(20) NOT NULL,
  `treinnummerlijst_id` bigint(20) NOT NULL,
  PRIMARY KEY (`treinnummerlijst_id`,`treinid`),
  KEY `IDX_95ACCAE69CBF59B5` (`treinnummerlijst_id`),
  KEY `IDX_95ACCAE668F454BD` (`treinid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_trein`
--

CREATE TABLE IF NOT EXISTS `somda_trein` (
  `treinid` bigint(20) NOT NULL AUTO_INCREMENT,
  `treinnr` varchar(15) NOT NULL,
  PRIMARY KEY (`treinid`),
  UNIQUE KEY `idx_49046_treinnr` (`treinnr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_users`
--

CREATE TABLE IF NOT EXISTS `somda_users` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `spots_ok` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cookie_ok` varchar(3) NOT NULL,
  `actkey` varchar(13) DEFAULT NULL,
  `regdate` datetime NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `active` tinyint(1) NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `ban_expire_timestamp` datetime DEFAULT NULL,
  `api_token` char(23) DEFAULT NULL,
  `api_token_expiry_timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `idx_49053_uname` (`username`),
  KEY `idx_49076_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_users_companies`
--

CREATE TABLE IF NOT EXISTS `somda_users_companies` (
  `bedrijf_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `naam` varchar(15) NOT NULL,
  PRIMARY KEY (`bedrijf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_users_groups`
--

CREATE TABLE IF NOT EXISTS `somda_users_groups` (
  `uid` bigint(20) NOT NULL,
  `groupid` bigint(20) NOT NULL,
  PRIMARY KEY (`groupid`,`uid`),
  KEY `IDX_B2ACF0767805AC12` (`groupid`),
  KEY `IDX_B2ACF076539B0606` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_users_info`
--

CREATE TABLE IF NOT EXISTS `somda_users_info` (
  `uid` bigint(20) NOT NULL,
  `avatar` varchar(30) NOT NULL DEFAULT '_blank.gif',
  `website` varchar(75) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `skype` varchar(60) DEFAULT NULL,
  `geslacht` smallint(4) NOT NULL DEFAULT '0',
  `gebdatum` date DEFAULT NULL,
  `mob_tel` bigint(20) DEFAULT NULL,
  `bedrijf_id` bigint(20) DEFAULT NULL,
  `twitter_account` varchar(255) DEFAULT NULL,
  `facebook_account` varchar(255) DEFAULT NULL,
  `flickr_account` varchar(255) DEFAULT NULL,
  `youtube_account` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `idx_49074_gebdatum` (`gebdatum`),
  KEY `IDX_46F59BD0740E9210` (`bedrijf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_users_prefs`
--

CREATE TABLE IF NOT EXISTS `somda_users_prefs` (
  `uid` bigint(20) NOT NULL,
  `prefid` bigint(20) NOT NULL,
  `value` varchar(200) NOT NULL,
  PRIMARY KEY (`uid`,`prefid`),
  KEY `IDX_3920F080539B0606` (`uid`),
  KEY `IDX_3920F08087B0DAC1` (`prefid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_verk`
--

CREATE TABLE IF NOT EXISTS `somda_verk` (
  `afkid` bigint(20) NOT NULL AUTO_INCREMENT,
  `afkorting` varchar(10) NOT NULL,
  `landid` bigint(20) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `traject` varchar(15) DEFAULT NULL,
  `route_overstaptijd` int(11) DEFAULT NULL,
  `spot_allowed` tinyint(1) NOT NULL,
  PRIMARY KEY (`afkid`),
  UNIQUE KEY `idx_49103_afkorting_2` (`afkorting`,`landid`),
  KEY `idx_49103_description` (`description`),
  KEY `idx_49103_landid` (`landid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_verk_cats`
--

CREATE TABLE IF NOT EXISTS `somda_verk_cats` (
  `verk_catid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `code` varchar(5) NOT NULL,
  PRIMARY KEY (`verk_catid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `somda_vervoerder`
--

CREATE TABLE IF NOT EXISTS `somda_vervoerder` (
  `vervoerder_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `omschrijving` varchar(35) NOT NULL,
  `prorail_desc` varchar(35) DEFAULT NULL,
  `iff_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`vervoerder_id`),
  UNIQUE KEY `idx_49122_omschrijving` (`omschrijving`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `ott_official_train_table`
--
ALTER TABLE `ott_official_train_table`
  ADD CONSTRAINT `FK_ott_characteristic_id` FOREIGN KEY (`ott_characteristic_id`) REFERENCES `somda_karakteristiek` (`karakteristiek_id`),
  ADD CONSTRAINT `FK_ott_location_id` FOREIGN KEY (`ott_location_id`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_ott_ofo_id` FOREIGN KEY (`ott_ofo_id`) REFERENCES `ofo_official_footnote` (`ofo_id`),
  ADD CONSTRAINT `FK_ott_route_id` FOREIGN KEY (`ott_route_id`) REFERENCES `somda_trein` (`treinid`),
  ADD CONSTRAINT `FK_ott_transporter_id` FOREIGN KEY (`ott_transporter_id`) REFERENCES `somda_vervoerder` (`vervoerder_id`);

--
-- Beperkingen voor tabel `somda_banner`
--
ALTER TABLE `somda_banner`
  ADD CONSTRAINT `FK_D93888C264FBF307` FOREIGN KEY (`customerid`) REFERENCES `somda_banner_customer` (`id`);

--
-- Beperkingen voor tabel `somda_banner_customer_user`
--
ALTER TABLE `somda_banner_customer_user`
  ADD CONSTRAINT `FK_C9A88E10539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_C9A88E10BF396750` FOREIGN KEY (`id`) REFERENCES `somda_banner_customer` (`id`);

--
-- Beperkingen voor tabel `somda_banner_hits`
--
ALTER TABLE `somda_banner_hits`
  ADD CONSTRAINT `FK_8610F3216BBC5658` FOREIGN KEY (`bannerid`) REFERENCES `somda_banner` (`bannerid`);

--
-- Beperkingen voor tabel `somda_banner_views`
--
ALTER TABLE `somda_banner_views`
  ADD CONSTRAINT `FK_F1B9EA066BBC5658` FOREIGN KEY (`bannerid`) REFERENCES `somda_banner` (`bannerid`);

--
-- Beperkingen voor tabel `somda_blokken`
--
ALTER TABLE `somda_blokken`
  ADD CONSTRAINT `FK_B4865B064F2A0381` FOREIGN KEY (`parent_block`) REFERENCES `somda_blokken` (`blokid`);

--
-- Beperkingen voor tabel `somda_ddar`
--
ALTER TABLE `somda_ddar`
  ADD CONSTRAINT `FK_9A508BF890261A4` FOREIGN KEY (`matid`) REFERENCES `somda_mat` (`matid`),
  ADD CONSTRAINT `FK_9A508BFC65F5051` FOREIGN KEY (`afkid`) REFERENCES `somda_verk` (`afkid`);

--
-- Beperkingen voor tabel `somda_don_donatie`
--
ALTER TABLE `somda_don_donatie`
  ADD CONSTRAINT `FK_DE3B771128103CB` FOREIGN KEY (`don_uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_drgl_read`
--
ALTER TABLE `somda_drgl_read`
  ADD CONSTRAINT `FK_7CF8CCE9539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_7CF8CCE9B869D711` FOREIGN KEY (`drglid`) REFERENCES `somda_drgl` (`drglid`);

--
-- Beperkingen voor tabel `somda_forum_alerts`
--
ALTER TABLE `somda_forum_alerts`
  ADD CONSTRAINT `FK_A2F3B42C65525B5F` FOREIGN KEY (`senderid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_A2F3B42C7510F6AF` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`);

--
-- Beperkingen voor tabel `somda_forum_alerts_notes`
--
ALTER TABLE `somda_forum_alerts_notes`
  ADD CONSTRAINT `FK_502511CE3412DD5F` FOREIGN KEY (`authorid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_502511CEF2677207` FOREIGN KEY (`alertid`) REFERENCES `somda_forum_alerts` (`id`);

--
-- Beperkingen voor tabel `somda_forum_discussion`
--
ALTER TABLE `somda_forum_discussion`
  ADD CONSTRAINT `FK_64C2DF7E3412DD5F` FOREIGN KEY (`authorid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_64C2DF7EEDB4D5F3` FOREIGN KEY (`forumid`) REFERENCES `somda_forum_forums` (`forumid`);

--
-- Beperkingen voor tabel `somda_forum_discussion_wiki`
--
ALTER TABLE `somda_forum_discussion_wiki`
  ADD CONSTRAINT `FK_D487B7F1FCC0F19E` FOREIGN KEY (`discussionid`) REFERENCES `somda_forum_discussion` (`discussionid`);

--
-- Beperkingen voor tabel `somda_forum_favorites`
--
ALTER TABLE `somda_forum_favorites`
  ADD CONSTRAINT `FK_4E8B7C93539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_4E8B7C93FCC0F19E` FOREIGN KEY (`discussionid`) REFERENCES `somda_forum_discussion` (`discussionid`);

--
-- Beperkingen voor tabel `somda_forum_forums`
--
ALTER TABLE `somda_forum_forums`
  ADD CONSTRAINT `FK_ABD72EFF3632DFC5` FOREIGN KEY (`catid`) REFERENCES `somda_forum_cats` (`catid`);

--
-- Beperkingen voor tabel `somda_forum_log`
--
ALTER TABLE `somda_forum_log`
  ADD CONSTRAINT `FK_256DFB117510F6AF` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`);

--
-- Beperkingen voor tabel `somda_forum_mods`
--
ALTER TABLE `somda_forum_mods`
  ADD CONSTRAINT `FK_E20AB6A4539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_E20AB6A4EDB4D5F3` FOREIGN KEY (`forumid`) REFERENCES `somda_forum_forums` (`forumid`);

--
-- Beperkingen voor tabel `somda_forum_posts`
--
ALTER TABLE `somda_forum_posts`
  ADD CONSTRAINT `FK_40FD24D143C6493` FOREIGN KEY (`wiki_uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_40FD24D3412DD5F` FOREIGN KEY (`authorid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_40FD24D9ECDC13D` FOREIGN KEY (`edit_uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_40FD24DFCC0F19E` FOREIGN KEY (`discussionid`) REFERENCES `somda_forum_discussion` (`discussionid`);

--
-- Beperkingen voor tabel `somda_forum_posts_text`
--
ALTER TABLE `somda_forum_posts_text`
  ADD CONSTRAINT `FK_25A0B80F7510F6AF` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`);

--
-- Beperkingen voor tabel `somda_forum_read_0`
--
ALTER TABLE `somda_forum_read_0`
  ADD CONSTRAINT `FK_24CD03BE7510F6AF` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_post_0` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_0` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_1`
--
ALTER TABLE `somda_forum_read_1`
  ADD CONSTRAINT `FK_forum_read_post_1` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_1` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_2`
--
ALTER TABLE `somda_forum_read_2`
  ADD CONSTRAINT `FK_forum_read_post_2` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_2` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_3`
--
ALTER TABLE `somda_forum_read_3`
  ADD CONSTRAINT `FK_forum_read_post_3` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_3` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_4`
--
ALTER TABLE `somda_forum_read_4`
  ADD CONSTRAINT `FK_forum_read_post_4` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_4` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_5`
--
ALTER TABLE `somda_forum_read_5`
  ADD CONSTRAINT `FK_forum_read_post_5` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_5` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_6`
--
ALTER TABLE `somda_forum_read_6`
  ADD CONSTRAINT `FK_forum_read_post_6` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_6` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_7`
--
ALTER TABLE `somda_forum_read_7`
  ADD CONSTRAINT `FK_forum_read_post_7` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_7` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_8`
--
ALTER TABLE `somda_forum_read_8`
  ADD CONSTRAINT `FK_forum_read_post_8` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_8` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_read_9`
--
ALTER TABLE `somda_forum_read_9`
  ADD CONSTRAINT `FK_forum_read_post_9` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_forum_read_user_9` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_forum_zoeken_lijst`
--
ALTER TABLE `somda_forum_zoeken_lijst`
  ADD CONSTRAINT `FK_C9D9A41E7510F6AF` FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`),
  ADD CONSTRAINT `FK_C9D9A41EE9BD09BA` FOREIGN KEY (`woord_id`) REFERENCES `somda_forum_zoeken_woorden` (`woord_id`);

--
-- Beperkingen voor tabel `somda_help_text`
--
ALTER TABLE `somda_help_text`
  ADD CONSTRAINT `FK_397D7775711B2385` FOREIGN KEY (`blokid`) REFERENCES `somda_blokken` (`blokid`);

--
-- Beperkingen voor tabel `somda_logging`
--
ALTER TABLE `somda_logging`
  ADD CONSTRAINT `FK_8127138D539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_mat`
--
ALTER TABLE `somda_mat`
  ADD CONSTRAINT `FK_355CF7922A00C2` FOREIGN KEY (`vervoerder_id`) REFERENCES `somda_vervoerder` (`vervoerder_id`),
  ADD CONSTRAINT `FK_355CF79F734A20F` FOREIGN KEY (`pattern_id`) REFERENCES `somda_mat_patterns` (`id`);

--
-- Beperkingen voor tabel `somda_mat_changes`
--
ALTER TABLE `somda_mat_changes`
  ADD CONSTRAINT `FK_C6C1DF0D4CD774E2` FOREIGN KEY (`matsmsid`) REFERENCES `somda_mat_sms` (`matsmsid`),
  ADD CONSTRAINT `FK_C6C1DF0D539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_mat_sms`
--
ALTER TABLE `somda_mat_sms`
  ADD CONSTRAINT `FK_2FC3E54DE70B032` FOREIGN KEY (`typeid`) REFERENCES `somda_mat_types` (`typeid`);

--
-- Beperkingen voor tabel `somda_news_read`
--
ALTER TABLE `somda_news_read`
  ADD CONSTRAINT `FK_AF652C5539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_AF652C58C510C37` FOREIGN KEY (`newsid`) REFERENCES `somda_news` (`newsid`);

--
-- Beperkingen voor tabel `somda_poll_votes`
--
ALTER TABLE `somda_poll_votes`
  ADD CONSTRAINT `FK_75CFE387539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_75CFE3876F5F43AE` FOREIGN KEY (`pollid`) REFERENCES `somda_poll` (`pollid`);

--
-- Beperkingen voor tabel `somda_sht_shout`
--
ALTER TABLE `somda_sht_shout`
  ADD CONSTRAINT `FK_88E10AFB97AD1E10` FOREIGN KEY (`sht_uid`) REFERENCES `somda_users` (`uid`);

--
-- Beperkingen voor tabel `somda_snf_spoor_nieuws_bron_feed`
--
ALTER TABLE `somda_snf_spoor_nieuws_bron_feed`
  ADD CONSTRAINT `FK_8A257AA6AD7A950` FOREIGN KEY (`snf_snb_id`) REFERENCES `somda_snb_spoor_nieuws_bron` (`snb_id`);

--
-- Beperkingen voor tabel `somda_sns_spoor_nieuws`
--
ALTER TABLE `somda_sns_spoor_nieuws`
  ADD CONSTRAINT `FRK_sns_snb_id` FOREIGN KEY (`sns_snb_id`) REFERENCES `somda_snb_spoor_nieuws_bron` (`snb_id`);

--
-- Beperkingen voor tabel `somda_spots`
--
ALTER TABLE `somda_spots`
  ADD CONSTRAINT `FK_11A6C5C8539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_11A6C5C868F454BD` FOREIGN KEY (`treinid`) REFERENCES `somda_trein` (`treinid`),
  ADD CONSTRAINT `FK_11A6C5C8890261A4` FOREIGN KEY (`matid`) REFERENCES `somda_mat` (`matid`),
  ADD CONSTRAINT `FK_11A6C5C8D6E3DC6C` FOREIGN KEY (`locatieid`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_11A6C5C8F4E25321` FOREIGN KEY (`posid`) REFERENCES `somda_positie` (`posid`);

--
-- Beperkingen voor tabel `somda_spots_extra`
--
ALTER TABLE `somda_spots_extra`
  ADD CONSTRAINT `FK_6EAD9515BFB6C75` FOREIGN KEY (`spotid`) REFERENCES `somda_spots` (`spotid`);

--
-- Beperkingen voor tabel `somda_spot_punt`
--
ALTER TABLE `somda_spot_punt`
  ADD CONSTRAINT `FK_6164DED2C0A1CCC` FOREIGN KEY (`afkid_traject_1`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_6164DED8A6CCEB4` FOREIGN KEY (`provincieid`) REFERENCES `somda_spot_provincie` (`provincieid`),
  ADD CONSTRAINT `FK_6164DEDB5034D76` FOREIGN KEY (`afkid_traject_2`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_6164DEDE2D85197` FOREIGN KEY (`afkid_dks`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_6164DEDFA5300E5` FOREIGN KEY (`afkid_locatie`) REFERENCES `somda_verk` (`afkid`);

--
-- Beperkingen voor tabel `somda_spot_punt_text`
--
ALTER TABLE `somda_spot_punt_text`
  ADD CONSTRAINT `FK_91652C16FD33F6CE` FOREIGN KEY (`puntid`) REFERENCES `somda_spot_punt` (`puntid`);

--
-- Beperkingen voor tabel `somda_stats_blokken`
--
ALTER TABLE `somda_stats_blokken`
  ADD CONSTRAINT `FK_7FAF7B1A711B2385` FOREIGN KEY (`blokid`) REFERENCES `somda_blokken` (`blokid`);

--
-- Beperkingen voor tabel `somda_tdr`
--
ALTER TABLE `somda_tdr`
  ADD CONSTRAINT `FK_84B606F668F454BD` FOREIGN KEY (`treinid`) REFERENCES `somda_trein` (`treinid`),
  ADD CONSTRAINT `FK_84B606F687CF3DBF` FOREIGN KEY (`rijdagenid`) REFERENCES `somda_rijdagen` (`rijdagenid`),
  ADD CONSTRAINT `FK_84B606F6AE60685A` FOREIGN KEY (`tdr_nr`) REFERENCES `somda_tdr_drgl` (`tdr_nr`),
  ADD CONSTRAINT `FK_84B606F6D6E3DC6C` FOREIGN KEY (`locatieid`) REFERENCES `somda_verk` (`afkid`);

--
-- Beperkingen voor tabel `somda_tdr_route`
--
ALTER TABLE `somda_tdr_route`
  ADD CONSTRAINT `FK_1A52615B9CBF59B5` FOREIGN KEY (`treinnummerlijst_id`) REFERENCES `somda_tdr_treinnummerlijst` (`id`),
  ADD CONSTRAINT `FK_1A52615BAE60685A` FOREIGN KEY (`tdr_nr`) REFERENCES `somda_tdr_drgl` (`tdr_nr`),
  ADD CONSTRAINT `FK_1A52615BD6E3DC6C` FOREIGN KEY (`locatieid`) REFERENCES `somda_verk` (`afkid`);

--
-- Beperkingen voor tabel `somda_tdr_s_e`
--
ALTER TABLE `somda_tdr_s_e`
  ADD CONSTRAINT `FK_1BACB9635E53C5B` FOREIGN KEY (`v_locatieid`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_1BACB96368F454BD` FOREIGN KEY (`treinid`) REFERENCES `somda_trein` (`treinid`),
  ADD CONSTRAINT `FK_1BACB9638228ED13` FOREIGN KEY (`a_locatieid`) REFERENCES `somda_verk` (`afkid`),
  ADD CONSTRAINT `FK_1BACB963AE60685A` FOREIGN KEY (`tdr_nr`) REFERENCES `somda_tdr_drgl` (`tdr_nr`);

--
-- Beperkingen voor tabel `somda_tdr_treinnummerlijst`
--
ALTER TABLE `somda_tdr_treinnummerlijst`
  ADD CONSTRAINT `FK_D7A6066022A00C2` FOREIGN KEY (`vervoerder_id`) REFERENCES `somda_vervoerder` (`vervoerder_id`),
  ADD CONSTRAINT `FK_D7A60660AE60685A` FOREIGN KEY (`tdr_nr`) REFERENCES `somda_tdr_drgl` (`tdr_nr`),
  ADD CONSTRAINT `FK_D7A60660FBDE844F` FOREIGN KEY (`karakteristiek_id`) REFERENCES `somda_karakteristiek` (`karakteristiek_id`);

--
-- Beperkingen voor tabel `somda_tdr_trein_mat`
--
ALTER TABLE `somda_tdr_trein_mat`
  ADD CONSTRAINT `FK_C2BF79AA68F454BD` FOREIGN KEY (`treinid`) REFERENCES `somda_trein` (`treinid`),
  ADD CONSTRAINT `FK_C2BF79AAAE60685A` FOREIGN KEY (`tdr_nr`) REFERENCES `somda_tdr_drgl` (`tdr_nr`),
  ADD CONSTRAINT `FK_C2BF79AAE14DDC7E` FOREIGN KEY (`mat_pattern_id`) REFERENCES `somda_mat_patterns` (`id`),
  ADD CONSTRAINT `FK_C2BF79AAF4E25321` FOREIGN KEY (`posid`) REFERENCES `somda_positie` (`posid`);

--
-- Beperkingen voor tabel `somda_tdr_trein_treinnummerlijst`
--
ALTER TABLE `somda_tdr_trein_treinnummerlijst`
  ADD CONSTRAINT `FK_95ACCAE668F454BD` FOREIGN KEY (`treinid`) REFERENCES `somda_trein` (`treinid`),
  ADD CONSTRAINT `FK_95ACCAE69CBF59B5` FOREIGN KEY (`treinnummerlijst_id`) REFERENCES `somda_tdr_treinnummerlijst` (`id`);

--
-- Beperkingen voor tabel `somda_users_groups`
--
ALTER TABLE `somda_users_groups`
  ADD CONSTRAINT `FK_B2ACF076539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_B2ACF0767805AC12` FOREIGN KEY (`groupid`) REFERENCES `somda_groups` (`groupid`);

--
-- Beperkingen voor tabel `somda_users_info`
--
ALTER TABLE `somda_users_info`
  ADD CONSTRAINT `FK_46F59BD0539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_46F59BD0740E9210` FOREIGN KEY (`bedrijf_id`) REFERENCES `somda_users_companies` (`bedrijf_id`);

--
-- Beperkingen voor tabel `somda_users_prefs`
--
ALTER TABLE `somda_users_prefs`
  ADD CONSTRAINT `FK_3920F080539B0606` FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`),
  ADD CONSTRAINT `FK_3920F08087B0DAC1` FOREIGN KEY (`prefid`) REFERENCES `somda_prefs` (`prefid`);

--
-- Beperkingen voor tabel `somda_verk`
--
ALTER TABLE `somda_verk`
  ADD CONSTRAINT `FK_F7F314CE61A61C` FOREIGN KEY (`landid`) REFERENCES `somda_verk_cats` (`verk_catid`);
