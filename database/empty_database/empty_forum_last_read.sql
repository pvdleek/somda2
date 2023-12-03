--
-- Tabelstructuur voor tabel `somda_forum_last_read`
--

CREATE TABLE IF NOT EXISTS `somda_forum_last_read` (
  `uid` int(10) unsigned NOT NULL,
  `discussionid` int(10) unsigned NOT NULL,
  `postid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`discussionid`),
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB

-- --------------------------------------------------------