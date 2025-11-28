-- Database schema for RSS Scheduler CI3 app

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  `content` longtext,
  `char_count` int(11),
  `pub_date` datetime,
  `priority` int(11),
  PRIMARY KEY (`id`)
);

CREATE TABLE `platforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100),
  PRIMARY KEY (`id`)
);

INSERT INTO `platforms` (name)
VALUES 
('Facebook'), 
('X'), 
('Instagram'), 
('LinkedIn'), 
('TikTok'), 
('Threads'), 
('Bluesky');

CREATE TABLE `post_platforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11),
  `platform_id` int(11),
  PRIMARY KEY (`id`)
);
