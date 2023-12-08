CREATE TABLE `users` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`email` varchar(50) NOT NULL,
`password` varchar(60) NOT NULL,
`remember_token` varchar(100) DEFAULT NULL,
`name` varchar(255) NOT NULL,
`address` varchar(75) NOT NULL DEFAULT '',
`address2` varchar(75) NOT NULL DEFAULT '',
`city` varchar(75) NOT NULL DEFAULT '',
`state` varchar(10) NOT NULL DEFAULT '',
`country` varchar(10) NOT NULL DEFAULT '',
`zip` varchar(10) NOT NULL DEFAULT '',
`is_admin` tinyint(1) NOT NULL DEFAULT '0',
`status` enum('active','pending','disabled') NOT NULL DEFAULT 'pending',
`upgraded` tinyint(1) unsigned NOT NULL DEFAULT '0',
`pref_show_welcome` tinyint(1) unsigned NOT NULL DEFAULT '0',
`pref_alerts` tinyint(1) unsigned NOT NULL DEFAULT '0',
`pref_page_limit` smallint(3) unsigned NOT NULL DEFAULT '25',
`pref_quick_menu` tinyint(3) unsigned NOT NULL DEFAULT '0',
`pref_all_rule` varchar(255) NOT NULL DEFAULT '',
`sms_provider_id` int unsigned NOT NULL DEFAULT '0', 
`sms_number` varchar(20) NOT NULL default '',
`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
UNIQUE KEY `email` (`email`),
KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `restaurants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL default 0,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `type` enum('Restaurant','Dining-Event') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`), 
  KEY `entity_id` (`entity_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO restaurants (entity_id, name, url, type, created_at, updated_at) VALUES (18420374, 'The Jungle Book: Alive With Magic Dining Package', 'https://disneyworld.disney.go.com/dining/animal-kingdom/jungle-book-dining-package/','Dining-Event', '2016-05-30 00:00:00', '2016-05-30 00:00:00');
INSERT INTO restaurants (entity_id, name, url, type, created_at, updated_at) VALUES (18436515, 'Frontera Cocina', 'https://disneyworld.disney.go.com/dining/disney-springs/frontera-cocina/','Restaurant', '2016-08-04 00:00:00', '2016-08-04 00:00:00');
INSERT INTO restaurants (entity_id, name, url, type, created_at, updated_at) VALUES (18432077, 'Disney Countdown to Midnight', 'https://disneyworld.disney.go.com/dining/contemporary-resort/new-years-eve-countdown-to-midnight/','Dining-Event', '2016-08-04 00:00:00', '2016-08-04 00:00:00');

CREATE TABLE `queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL default 0,
  `entity_id` int unsigned NOT NULL default 0,
  `description` varchar(255) NOT NULL default '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `time` varchar(10) NOT NULL default '',
  `size` tinyint(2) unsigned NOT NULL default 1,
  `availability` varchar(50) NOT NULL default 'pending', 
  `success` tinyint(1) unsigned NOT NULL default 0,
  `deleted` tinyint(1) unsigned NOT NULL default 0,
  `alert` tinyint(1) unsigned NOT NULL default 0,
  `error` tinyint(1) unsigned NOT NULL default 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `alerted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`), 
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE queue ADD `description` varchar(255) NOT NULL default '' AFTER `entity_id`;
ALTER TABLE queue ADD `error` tinyint(1) unsigned NOT NULL default 0 AFTER `alert`;

CREATE TABLE `queue_results` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int unsigned NOT NULL default 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `time` varchar(10) NOT NULL default '',
  `size` tinyint(2) unsigned NOT NULL default 1,
  `availability` varchar(50) NOT NULL default '',
  `expires_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`), 
  KEY `date` (`date`),
  KEY `entity_id` (`entity_id`),
  KEY `expires_at` (`expires_at`),
  KEY `idx` (`date`,`time`,`size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
