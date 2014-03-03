-- --------------------------------------------------------

--
-- Structure for `authors`
--

CREATE TABLE `authors` (
  `id` int(4) unsigned NOT NULL auto_increment,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `passwd` varchar(50) NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
);

-- please be aware - following password is valid only for
-- Configure::write('Security.salt', '40ab12cb6b2241ec7272030ac0b6f8d65cd8031a');
INSERT INTO `authors` (`admin`, `name`, `username`, `passwd`, `created`, `modified`) VALUES (1, 'Administrator', 'admin', 'fd1b755a266f9473582780a9bfa8312fcc1ee920', NOW(), NOW());

-- --------------------------------------------------------

--
-- Structure for `blogs`
--

CREATE TABLE `blogs` (
  `id` int(4) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(100) NOT NULL,
  `description` text,
  `theme` varchar(100) NULL default NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);
INSERT INTO `blogs` (`name`, `short_name`, `description`, `created`, `modified`) VALUES ('My First LilBlog', 'my-first-lilblog', 'This is your first blog. Enyoj!', NOW(), NOW());

-- --------------------------------------------------------

--
-- Structure for `authors_blogs`
--

CREATE TABLE `authors_blogs` (
  `id` int(10) NOT NULL auto_increment,
  `author_id` int(10) default NULL,
  `blog_id` int(10) default NULL,
  PRIMARY KEY  (`id`)
);
INSERT INTO `authors_blogs` (`author_id`, `blog_id`) VALUES (1, 1);

-- --------------------------------------------------------

--
-- Structure for `categories`
--

CREATE TABLE `categories` (
  `id` int(4) unsigned NOT NULL auto_increment,
  `blog_id` int(4) default NULL,
  `name` varchar(100) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Structure for `categories_posts`
--

CREATE TABLE `categories_posts` (
  `id` int(10) NOT NULL auto_increment,
  `category_id` int(4) default NULL,
  `post_id` int(10) default NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Structure for `comments`
--

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL,
  `body` text,
  `author` varchar(100) default NULL,
  `url` varchar(255)default NULL,
  `email` varchar(255) default NULL,
  `ip` varchar(15) default NULL,
  `status` mediumint(4) default '1',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `post_id` (`post_id`)
);

-- --------------------------------------------------------

--
-- Structure for `nb_categories`
--

CREATE TABLE `nb_categories` (
  `id` varchar(100) NOT NULL,
  `probability` double NOT NULL default '0',
  `word_count` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
INSERT INTO `nb_categories` (`id`, `probability`, `word_count`) VALUES('spam', 0.5, 0);
INSERT INTO `nb_categories` (`id`, `probability`, `word_count`) VALUES('ham', 0.5, 0);
-- --------------------------------------------------------

--
-- Structure for `nb_references`
--

CREATE TABLE `nb_references` (
  `id` varchar(100) NOT NULL,
  `category_id` varchar(100) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`)
);

-- --------------------------------------------------------

--
-- Structure for `nb_wordfreqs`
--

CREATE TABLE `nb_wordfreqs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `word` varchar(100) NOT NULL,
  `category_id` varchar(100) NOT NULL default '',
  `count` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Structure for `posts`
--

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `blog_id` int(10) default NULL,
  `author_id` int(10) default NULL,
  `status` tinyint(2) NOT NULL default '0',
  `title` varchar(100) default NULL,
  `slug` varchar(100) default NULL,
  `body` text,
  `no_comments` int(4) NOT NULL default '0',
  `allow_comments` tinyint(1) NOT NULL default '1',
  `allow_pingback` tinyint(1) NOT NULL default '1',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
);
INSERT INTO `posts` (`id`, `blog_id`, `author_id`, `status`, `title`, `slug`, `body`, `no_comments`, `allow_comments`, `allow_pingback`, `created`, `modified`) VALUES (NULL, '1', '1', '2', 'My First Post', 'my-first-post', 'Hello world! This is my first post. Please do enjoy your reading.', '0', '1', '1', NULL, NULL);
