INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_0` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_1` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_2` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_3` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_4` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_5` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_6` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_7` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_8` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
INSERT IGNORE INTO `somda_forum_last_read` 
  (`uid`, `discussionid`, `postid`)
  SELECT `uid`, discussionid, max(r.postid)
  from `somda_forum_read_9` r
  left join `somda_forum_posts` p
  on p.postid = r.postid
  group by discussionid;

-- --------------------------------------------------------
