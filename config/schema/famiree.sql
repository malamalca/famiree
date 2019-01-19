--
-- Database: `famiree`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` char(36) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `original` varchar(255) DEFAULT NULL,
  `ext` varchar(6) NOT NULL DEFAULT 'gif',
  `mimetype` varchar(30) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `height` int(4) DEFAULT NULL,
  `width` int(4) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `checksum` varchar(32) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `attachments_links`
--

CREATE TABLE `attachments_links` (
  `id` int(11) NOT NULL,
  `attachment_id` char(36) DEFAULT NULL,
  `class` varchar(7) NOT NULL DEFAULT '',
  `foreign_id` varchar(36) NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `event_dates`
--

CREATE TABLE `event_dates` (
  `id` int(11) NOT NULL,
  `class` varchar(50) NOT NULL,
  `foreign_id` int(10) NOT NULL DEFAULT '0',
  `kind` varchar(8) NOT NULL DEFAULT '',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `imgnotes`
--

CREATE TABLE `imgnotes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `attachment_id` char(36) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `x1` mediumint(4) DEFAULT NULL,
  `y1` mediumint(4) DEFAULT NULL,
  `width` mediumint(4) DEFAULT NULL,
  `height` mediumint(4) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(10) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `foreign_id` char(36) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `change` text,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) NOT NULL,
  `blog_id` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(2) NOT NULL DEFAULT '2',
  `title` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `body` text,
  `created` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modifier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `posts_links`
--

CREATE TABLE `posts_links` (
  `id` int(11) NOT NULL,
  `post_id` int(10) NOT NULL DEFAULT '0',
  `class` varchar(20) NOT NULL,
  `foreign_id` int(10) NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(10) NOT NULL,
  `ta` char(100) DEFAULT NULL COMMENT 'Attachment',
  `d_n` varchar(200) DEFAULT NULL COMMENT 'Display name',
  `ln` varchar(100) DEFAULT NULL COMMENT 'Last name',
  `mdn` varchar(100) DEFAULT NULL COMMENT 'Maiden name',
  `fn` varchar(100) DEFAULT NULL COMMENT 'First name',
  `mn` varchar(100) DEFAULT NULL COMMENT 'Middle name',
  `g` char(1) NOT NULL DEFAULT 'm' COMMENT 'Gender',
  `l` tinyint(1) DEFAULT '0' COMMENT 'Living',
  `e` varchar(100) DEFAULT NULL COMMENT 'Email',
  `rst` varchar(36) DEFAULT NULL,
  `u` varchar(100) DEFAULT NULL COMMENT 'Username',
  `p` varchar(255) DEFAULT NULL COMMENT 'Password',
  `lvl` mediumint(4) NOT NULL DEFAULT '10',
  `dob_y` varchar(10) DEFAULT NULL,
  `dob_m` mediumint(4) DEFAULT NULL,
  `dob_d` mediumint(4) DEFAULT NULL,
  `dod_y` varchar(10) DEFAULT NULL,
  `dod_m` mediumint(4) DEFAULT NULL,
  `dod_d` mediumint(4) DEFAULT NULL,
  `dob_c` tinyint(1) DEFAULT NULL,
  `dod_c` tinyint(1) DEFAULT NULL,
  `h_c` mediumint(4) DEFAULT NULL COMMENT 'Hair Color',
  `e_c` mediumint(4) DEFAULT NULL COMMENT 'Eye Color',
  `n_n` varchar(100) DEFAULT NULL COMMENT 'Nick Names',
  `loc` varchar(200) DEFAULT NULL,
  `plob` varchar(100) DEFAULT NULL COMMENT 'Place of Birth',
  `plod` varchar(100) DEFAULT NULL COMMENT 'Place of Death',
  `cod` varchar(100) DEFAULT NULL COMMENT 'Cause of Death',
  `plobu` varchar(100) DEFAULT NULL COMMENT 'Place of Burrial',
  `in_i` varchar(200) DEFAULT NULL COMMENT 'Interests',
  `in_a` varchar(200) DEFAULT NULL COMMENT 'Activities',
  `in_p` varchar(200) DEFAULT NULL COMMENT 'PeopleHeroes',
  `in_c` varchar(200) DEFAULT NULL COMMENT 'Cuisines',
  `in_q` varchar(200) DEFAULT NULL COMMENT 'Quotes',
  `in_m` varchar(200) DEFAULT NULL COMMENT 'Movies',
  `in_tv` varchar(200) DEFAULT NULL COMMENT 'TV Shows',
  `in_mu` varchar(200) DEFAULT NULL COMMENT 'Music',
  `in_b` varchar(200) DEFAULT NULL COMMENT 'Books',
  `in_s` varchar(200) DEFAULT NULL COMMENT 'Sports',
  `cn_med` int(11) DEFAULT NULL COMMENT 'Media Count',
  `cn_mem` int(11) DEFAULT NULL COMMENT 'Memory Count',
  `last_login` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modifier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `date_order` varchar(3) DEFAULT NULL,
  `date_separator` varchar(1) DEFAULT NULL,
  `date_24hr` tinyint(1) DEFAULT NULL,
  `datef_common` varchar(50) DEFAULT NULL,
  `datef_noyear` varchar(50) DEFAULT NULL,
  `datef_short` varchar(50) DEFAULT NULL,
  `locale` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `unions`
--

CREATE TABLE `unions` (
  `id` int(10) NOT NULL,
  `t` char(1) DEFAULT NULL,
  `dom_d` int(4) DEFAULT NULL,
  `dom_m` int(4) DEFAULT NULL,
  `dom_y` varchar(10) DEFAULT NULL,
  `loc` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(10) NOT NULL,
  `union_id` int(10) DEFAULT NULL,
  `profile_id` int(10) DEFAULT NULL,
  `kind` char(1) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attachments_links`
--
ALTER TABLE `attachments_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IX_UNIQUE` (`attachment_id`,`class`,`foreign_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `event_dates`
--
ALTER TABLE `event_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `imgnotes`
--
ALTER TABLE `imgnotes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts_links`
--
ALTER TABLE `posts_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unions`
--
ALTER TABLE `unions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_UNION` (`union_id`),
  ADD KEY `IX_PROFILE` (`profile_id`,`kind`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments_links`
--
ALTER TABLE `attachments_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_dates`
--
ALTER TABLE `event_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT for table `imgnotes`
--
ALTER TABLE `imgnotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1213;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `posts_links`
--
ALTER TABLE `posts_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=643;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `unions`
--
ALTER TABLE `unions`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=851;
COMMIT;
