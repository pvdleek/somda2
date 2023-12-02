--
-- Tabelstructuur voor tabel `somda_forum_last_read`
--

CREATE TABLE IF NOT EXISTS `somda_forum_last_read` (
  `uid` unsigned int(10) NOT NULL,
  `discussionid` unsigned int(10) NOT NULL,
  `postid` unsigned bigint(20) NOT NULL,
  PRIMARY KEY (`uid`,`discussionid`),
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB

-- --------------------------------------------------------