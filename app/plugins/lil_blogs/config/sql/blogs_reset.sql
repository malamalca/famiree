
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

TRUNCATE `authors`;
TRUNCATE `authors_blogs`;
TRUNCATE `blogs`;
TRUNCATE `categories`;
TRUNCATE `categories_posts`;
TRUNCATE `comments`;
TRUNCATE `nb_categories`;
TRUNCATE `nb_references`;
TRUNCATE `nb_wordfreqs`;
TRUNCATE `posts`;


INSERT INTO `authors` VALUES(1, 1, 'Administrator', '', 'admin', 'fd1b755a266f9473582780a9bfa8312fcc1ee920', '2009-01-01 12:00:00', '2009-01-01 12:00:00');


INSERT INTO `authors_blogs` VALUES(1, 1, 1);


INSERT INTO `blogs` VALUES(1, 'My First Blog', 'my-first-lilblog', 'This is my first blog.', NULL, '2009-01-01 12:00:00', '2009-01-01 12:00:00');


INSERT INTO `categories` VALUES(1, 1, 'Parent', '2009-01-01 12:00:00', '2009-01-01 12:00:00');
INSERT INTO `categories` VALUES(2, 1, 'First Child', '2009-01-01 12:00:00', '2009-01-01 12:00:00');
INSERT INTO `categories` VALUES(3, 1, 'Second Child', '2009-01-01 12:00:00', '2009-01-01 12:00:00');
INSERT INTO `categories` VALUES(4, 1, 'One GrandChild', '2009-01-01 12:00:00', '2009-01-01 12:00:00');


INSERT INTO `categories_posts` VALUES(1, 1, 2);
INSERT INTO `categories_posts` VALUES(2, 2, 2);
INSERT INTO `categories_posts` VALUES(3, 3, 2);
INSERT INTO `categories_posts` VALUES(4, 4, 2);


INSERT INTO `comments` VALUES(1, 4, 'I never could remember more than the first few lines of that nursery rhyme.', 'Joseph Scott', 'http://joseph.randomnetworks.com/', 'joseph@randomnetworks.com', '127.0.0.1', 2, '2008-06-21 12:07:00', '2008-06-21 12:07:00');
INSERT INTO `comments` VALUES(2, 1, 'Hi, this is a comment.\r\nTo delete a comment, just log in and view the post''s comments. There you will have the option to edit or delete them.', 'Mr LilBlogs', 'http://www.lilcake.net/', 'info@lilcake.net', '127.0.0.1', 2, '2008-06-04 22:40:00', '2008-06-04 22:40:00');
INSERT INTO `comments` VALUES(3, 1, 'Need a comment with a real Gravatar.', 'Malamalca', 'http://www.malamalca.com/', 'info@malamalca.com', '127.0.0.1', 2, '2008-11-05 19:38:00', '2008-11-05 19:38:00');




INSERT INTO `nb_categories` VALUES('spam', 0.5, 0);
INSERT INTO `nb_categories` VALUES('ham', 0.5, 0);






INSERT INTO `posts` VALUES(7, 1, 1, 2, 'Worth A Thousand Words', 'worth-a-thousand-words', '<div class="caption alignnone" style="width: 445px"><img alt="Boat" src="lil_blogs/img/boat.jpg" title="Boat" width="435" height="288" /><p class="caption-text">Boat</p></div>\r\n\r\n<p>Boat.</p>', 0, 0, 1, '2008-10-17 04:33:00', '2008-10-17 04:33:00');
INSERT INTO `posts` VALUES(6, 1, 1, 2, 'Elements', 'elements', '<p><!-- Sample Content to Plugin to Template --></p>\r\n<p>The purpose of this HTML is to help determine what default settings are with CSS and to make sure that all possible HTML Elements are included in this HTML so as to not miss any possible Elements when designing a site.</p>\r\n<hr />\r\n<h1>Heading 1</h1>\r\n<h2>Heading 2</h2>\r\n<h3>Heading 3</h3>\r\n\r\n<h4>Heading 4</h4>\r\n<h5>Heading 5</h5>\r\n<h6>Heading 6</h6>\r\n<p><small><a href="#wrapper">[top]</a></small></p>\r\n<hr />\r\n<h2 id="paragraph">Paragraph</h2>\r\n<p>Lorem ipsum dolor sit amet, <a href="#" title="test link">test link</a> adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>\r\n<p>Lorem ipsum dolor sit amet, <em>emphasis</em> consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>\r\n\r\n<p><small><a href="#wrapper">[top]</a></small></p>\r\n<hr />\r\n<h2 id="list_types">List Types</h2>\r\n<h3>Definition List</h3>\r\n<dl>\r\n<dt>Definition List Title</dt>\r\n<dd>This is a definition list division.</dd>\r\n</dl>\r\n<h3>Ordered List</h3>\r\n<ol>\r\n<li>List Item 1</li>\r\n\r\n<li>List Item 2</li>\r\n<li>List Item 3</li>\r\n</ol>\r\n<h3>Unordered List</h3>\r\n<ul>\r\n<li>List Item 1</li>\r\n<li>List Item 2</li>\r\n<li>List Item 3</li>\r\n</ul>\r\n<p><small><a href="#wrapper">[top]</a></small></p>\r\n\r\n<hr />\r\n<h2 id="form_elements">Forms</h2>\r\n<p><fieldset><br />\r\n	<legend>Legend</legend></p>\r\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus.</p>\r\n<form>\r\n<h2>Form Element</h2>\r\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui.</p>\r\n<p><label for="text_field">Text Field:</label></p>\r\n<input type="text" id="text_field" />\r\n\r\n<p><label for="text_area">Text Area:</label><br />\r\n		<textarea id="text_area"></textarea></p>\r\n<p><label for="select_element">Select Element:</label></p>\r\n<select name="select_element">\r\n			<optgroup label="Option Group 1"><br />\r\n				<option value="1">Option 1</option><br />\r\n				<option value="2">Option 2</option><br />\r\n				<option value="3">Option 3</option><br />\r\n\r\n			</optgroup></p>\r\n<p>			<optgroup label="Option Group 2"><br />\r\n				<option value="1">Option 1</option><br />\r\n				<option value="2">Option 2</option><br />\r\n				<option value="3">Option 3</option><br />\r\n			</optgroup><br />\r\n		</select>\r\n\r\n</p>\r\n<p><label for="radio_buttons">Radio Buttons:</label></p>\r\n<input type="radio" class="radio" name="radio_button" value="radio_1" /> Radio 1<br/></p>\r\n<input type="radio" class="radio" name="radio_button" value="radio_2" /> Radio 2<br/></p>\r\n<input type="radio" class="radio" name="radio_button" value="radio_3" /> Radio 3<br/>\r\n		</p>\r\n<p><label for="checkboxes">Checkboxes:</label></p>\r\n<input type="checkbox" class="checkbox" name="checkboxes" value="check_1" /> Radio 1<br/></p>\r\n\r\n<input type="checkbox" class="checkbox" name="checkboxes" value="check_2" /> Radio 2<br/></p>\r\n<input type="checkbox" class="checkbox" name="checkboxes" value="check_3" /> Radio 3<br/>\r\n		</p>\r\n<p><label for="password">Password:</label></p>\r\n<input type="password" class="password" name="password" />\r\n<p><label for="file">File Input:</label></p>\r\n<input type="file" class="file" name="file" />\r\n<p>\r\n<input class="button" type="reset" value="Clear" />\r\n<input class="button" type="submit" value="Submit" /></p></form>\r\n\r\n<p></fieldset></p>\r\n<p><small><a href="#wrapper">[top]</a></small></p>\r\n<hr />\r\n<h2 id="tables">Tables</h2>\r\n<table cellspacing="0" cellpadding="0">\r\n<tr>\r\n<th>Table Header 1</th>\r\n<th>Table Header 2</th>\r\n<th>Table Header 3</th>\r\n</tr>\r\n<tr>\r\n<td>Division 1</td>\r\n\r\n<td>Division 2</td>\r\n<td>Division 3</td>\r\n</tr>\r\n<tr class="even">\r\n<td>Division 1</td>\r\n<td>Division 2</td>\r\n<td>Division 3</td>\r\n</tr>\r\n<tr>\r\n<td>Division 1</td>\r\n<td>Division 2</td>\r\n\r\n<td>Division 3</td>\r\n</tr>\r\n</table>\r\n<p><small><a href="#wrapper">[top]</a></small></p>\r\n<hr />\r\n<h2 id="misc">Misc Stuff &#8211; abbr, acronym, pre, code, sub, sup, etc.</h2>\r\n<p>Lorem <sup>superscript</sup> dolor <sub>subscript</sub> amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. <cite>cite</cite>. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. <acronym title="National Basketball Association">NBA</acronym> Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.  <abbr title="Avenue">AVE</abbr></p>\r\n\r\n<pre>\r\n\r\n\r\nLorem ipsum dolor sit amet,\r\n consectetuer adipiscing elit.\r\n Nullam dignissim convallis est.\r\n Quisque aliquam. Donec faucibus.\r\nNunc iaculis suscipit dui.\r\nNam sit amet sem.\r\nAliquam libero nisi, imperdiet at,\r\n tincidunt nec, gravida vehicula,\r\n nisl.\r\nPraesent mattis, massa quis\r\nluctus fermentum, turpis mi\r\nvolutpat justo, eu volutpat\r\nenim diam eget metus.\r\nMaecenas ornare tortor.\r\nDonec sed tellus eget sapien\r\n fringilla nonummy.\r\n<acronym title="National Basketball Association">NBA</acronym>\r\nMauris a ante. Suspendisse\r\n quam sem, consequat at,\r\ncommodo vitae, feugiat in,\r\nnunc. Morbi imperdiet augue\r\n quis tellus.\r\n<abbr title="Avenue">AVE</abbr>\r\n</pre>\r\n<blockquote><p>\r\n	&#8220;This stylesheet is going to help so freaking much.&#8221; <br />-Blockquote\r\n</p></blockquote>\r\n<p><small><a href="#wrapper">[top]</a></small><br />\r\n\r\n<!-- End of Sample Content --></p>', 0, 0, 1, '2008-09-05 19:35:00', '2008-09-05 19:35:00');
INSERT INTO `posts` VALUES(5, 1, 1, 2, 'More Tags', 'more-tags', '<p>More of these posts need tags.</p>\r\n', 0, 1, 1, '2008-06-21 12:09:00', '2008-06-21 12:09:00');
INSERT INTO `posts` VALUES(4, 1, 1, 2, 'HTML', 'html', '<p>What HTML tags would you like to see?</p>\r\n<p>Let&#8217;s start with an unordered list:</p>\r\n<ul>\r\n<li>One</li>\r\n<li>Two</li>\r\n\r\n<li>Three</li>\r\n<li>Four</li>\r\n</ul>\r\n<p>And then move on to a more interesting ordered list:</p>\r\n<ol>\r\n<li>one, two\r\n<ol>\r\n<li>buckle my shoe</li>\r\n</ol>\r\n</li>\r\n<li>three, four\r\n<ol>\r\n<li>knock at the door</li>\r\n\r\n</ol>\r\n</li>\r\n<li>Five, six\r\n<ol>\r\n<li>pick up sticks</li>\r\n</ol>\r\n</li>\r\n<li>Seven, eight, lay them straight\r\n<ol>\r\n<li>Nine, ten, a big fat hen</li>\r\n<li>Eleven, twelve, dig and delve</li>\r\n<li>Thirteen, fourteen, maids a&#8217;courting</li>\r\n\r\n<li>Fifteen, sixteen, maids in the kitchen</li>\r\n<li>Seventeen, eighteen, maids a&#8217;waiting</li>\r\n<li>Nineteen, twenty, my platter&#8217;s empty &#8230;</li>\r\n</ol>\r\n</li>\r\n</ol>\r\n', 1, 1, 1, '2008-06-21 12:04:00', '2008-06-21 12:04:00');
INSERT INTO `posts` VALUES(3, 1, 1, 2, 'Links', 'links', '<p>A few well known LilBlogs links: <a href="http://lilblogs.net/">LilBlogs.net</a>, <a href="http://codex.lilblogs.net/Main_Page">the Codex</a> and <a href="http://lilblogs.net/download/">the download page</a>.</p>\r\n', 0, 0, 1, '2008-06-20 20:06:00', '2008-06-20 20:06:00');
INSERT INTO `posts` VALUES(2, 1, 1, 2, 'Category Hierarchy', 'category-hierarchy', '<p>This post has 4 categories, part of a hierarchy that is 3 deep.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Fusce euismod commodo ante. Suspendisse potenti. Nunc pellentesque quam vel pede. Ut a lorem non urna molestie euismod. Fusce consequat tortor eu urna. Pellentesque aliquam, pede eget tincidunt feugiat, nunc massa hendrerit magna, non ultricies neque lectus nec dui. In hac habitasse platea dictumst. Sed feugiat quam eget lectus. Fusce at pede. Morbi sagittis tristique tortor. Sed erat justo, blandit ac, dignissim in, pretium ut, urna.</p>\r\n', 0, 0, 1, '2008-06-20 19:59:00', '2008-06-20 19:59:00');
INSERT INTO `posts` VALUES(1, 1, 1, 2, 'Hello world!', 'hello-world', '<p>Welcome to LilBlogs. This is your first post. Edit or delete it, then start blogging!</p>\r\n', 2, 1, 1, '2008-06-04 22:40:00', '2008-06-04 22:40:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
