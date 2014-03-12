

CREATE TABLE IF NOT EXISTS `drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `slug` char(100) NOT NULL,
  `content` text NOT NULL,
  `code` text,
  `cover` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `draft_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `sessions` (
  `sid` char(33) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session` text NOT NULL,
  `update_date` datetime NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` char(100) NOT NULL,
  `key_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL,
  `rkey` VARCHAR( 255 ),
  `name` char(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bio` text,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` char(50) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `posts` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS `drafts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) NOT NULL,
    `draft_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS `medias` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `large` varchar(200) NOT NULL,
      `thumb` varchar(200) NOT NULL,
      `ext` char(5) NOT NULL,
      `user_id` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS `drafts_medias` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `draft_id` int(11) NOT NULL,
      `media_id` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB;
