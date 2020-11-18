<?php

#
# Ceres
# (c) 2020 Simon Strudwick
# 

define('ENABLE_DEVELOPMENT_MODE', false); // Set to false in production environment. Set to true to view PHP warnings and errors

define('SITE_TITLE', 'Ceres Example');
define('SITE_URL', 'https://ceres-example.strudwick.org/'); // Must end with a forward-slash /

define('MARKDOWN_FILE_EXTENSION', 'markdown');

define('HOME_PAGE_NAME', 'home');
define('ARCHIVE_PAGE_NAME', 'archive');
define('CONTACT_PAGE_NAME', 'contact');
define('SITEMAP_PAGE_NAME', 'site_map');

define('SHOW_SLUG_ON_PAGES', false);
define('SHOW_TAGS_ON_PAGES', false);
define('SHOW_SLUG_ON_POSTS', true);
define('SHOW_TAGS_ON_POSTS', false);

define('HOME_PAGE_MAX_POSTS', 10); // Set to 0 to show all posts
define('HOME_POST_HEADER_LEVEL', 2);
define('HOME_POST_SUBHEADER_LEVEL', 3);
define('HOME_POST_DATE_POSITON', 2); // 0 don't show, 1 show above title, 2 show below title, 3 show below post
define('READ_MORE_LINK', 'Read More');
define('ARCHIVE_LINK', 'Archive');

// Contact Email
define('SEND_TO', 'email@example.org');
define('SEND_AUTOREPLY', false);

// RSS feed values
define('RSS_TITLE', 'Ceres');
define('RSS_DESCRIPTION', 'Example site');
define('RSS_LANGUAGE', 'en-gb'); // See https://www.rssboard.org/rss-language-codes

?>