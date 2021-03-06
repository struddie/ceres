<?php

#
# Ceres
# (c)2020 Simon Strudwick
# 

include 'Ceres.php';

class PageBuilder {
	
	public $enableHomePage;
	public $enableArchivePage;
	public $enableSiteMap;
	public $enableRSSFeed;
	
	function __construct($enableHomePage = true, $enableArchivePage = true, $enableSiteMap = true, $enableRSSFeed = true) {
		$this->enableHomePage = $enableHomePage;
		$this->enableArchivePage = $enableArchivePage;
		$this->enableSiteMap = $enableSiteMap;
		$this->enableRSSFeed = $enableRSSFeed;
	}
	
	function buildSite() {
		if ($this->enableHomePage) {
			if ($this->makeHomePage()) {
				echo "<p>Updated home page.</p>";
			} else {
				echo "<p>ERROR: Could not create home page.</p>";
			}
		}
		
		if ($this->enableArchivePage) {
			if ($this->makeArchivePage()) {
				echo "<p>Updated archive.</p>";
			} else {
				echo "<p>ERROR: Could not create archive.</p>";
			}
		}
		
		if ($this->enableSiteMap) {
			if ($this->makeSiteMap()) {
				echo "<p>Updated site map.</p>";
			} else {
				echo "<p>ERROR: Could not create site map.</p>";
			}
		}
		
		if ($this->enableRSSFeed) {
			if ($this->makeRSSFeed()) {
				echo "<p>Updated RSS feed.</p>";
			} else {
				echo "<p>ERROR: Could not create RSS feed.</p>";
			}
		}
	}
	
	function makeHomePage() {
		$homePageContents = "@@ Do not edit this file, it will be updated when you run the Update Site".PHP_EOL.PHP_EOL;
		$homePageContents .= file_get_contents("../content/fragments/".HOME_PAGE_NAME.".".MARKDOWN_FILE_EXTENSION).PHP_EOL.PHP_EOL;
		
		$files = listdir_by_date("../content/posts/*.".MARKDOWN_FILE_EXTENSION);
		
		$maxPostCount = HOME_PAGE_MAX_POSTS === 0 ? PHP_INT_MAX : HOME_PAGE_MAX_POSTS;
		$fileCounter = 0;
		
		foreach ($files as $file) {
			if ($fileCounter >= $maxPostCount) {
				break;
			}
			
			$filename = basename($file);
			$post = new Post($filename);
			$headerStr = str_repeat('#', HOME_POST_HEADER_LEVEL);
			$subHeaderStr = str_repeat('#', HOME_POST_SUBHEADER_LEVEL);
			
			if (HOME_POST_DATE_POSITON === 1) {
				$homePageContents .= "{$subHeaderStr} {$post->formattedDate()}".PHP_EOL.PHP_EOL;
			}
			
			$homePageContents .= "{$headerStr} [{$post->title}](/post/{$post->pageName})".PHP_EOL.PHP_EOL;
			
			if (HOME_POST_DATE_POSITON === 2) {
				$homePageContents .= "{$subHeaderStr} {$post->formattedDate()}".PHP_EOL.PHP_EOL;
			}
			
			$homePageContents .= $post->excerpt(100).PHP_EOL.PHP_EOL;
			$homePageContents .= "{$subHeaderStr} [".READ_MORE_LINK."](/post/{$post->pageName})".PHP_EOL.PHP_EOL;
			
			if (HOME_POST_DATE_POSITON === 3) {
				$homePageContents .= "{$subHeaderStr} {$post->formattedDate()}".PHP_EOL.PHP_EOL;
			}
			
			$homePageContents .= "----".PHP_EOL.PHP_EOL;
		}
		
		$homePageContents = $homePageContents.PHP_EOL."[".ARCHIVE_LINK."](/page/".ARCHIVE_PAGE_NAME.")";
		$homePagePath = $_SERVER['DOCUMENT_ROOT']."/content/pages/".HOME_PAGE_NAME.".".MARKDOWN_FILE_EXTENSION;
		
		return file_put_contents($homePagePath, $homePageContents);
	}
	
	function makeArchivePage() {
		$archivePageContents = "@@ Do not edit this file, it will be updated when you run the Update Site".PHP_EOL.PHP_EOL;
		$archivePageContents .= file_get_contents("../content/fragments/".ARCHIVE_PAGE_NAME.".".MARKDOWN_FILE_EXTENSION).PHP_EOL.PHP_EOL;
		
		$files = listdir_by_date('../content/posts/*.'.MARKDOWN_FILE_EXTENSION);
		
		foreach ($files as $file) {
			$filename = basename($file);
			$post = new Post($filename);
			$archivePageContents .= "[{$post->title}](/post/{$post->pageName}) {$post->formattedDate()}".PHP_EOL.PHP_EOL;			
		}
		
		$archivePagePath = $_SERVER['DOCUMENT_ROOT']."/content/pages/".ARCHIVE_PAGE_NAME.".".MARKDOWN_FILE_EXTENSION;
		return file_put_contents($archivePagePath, $archivePageContents);
	}
	
	function makeSiteMap() {
		$siteMapPageContents = "@@ Do not edit this file, it will be updated when you run the Update Site".PHP_EOL.PHP_EOL;
		$siteMapPageContents .= file_get_contents("../content/fragments/".SITEMAP_PAGE_NAME.".".MARKDOWN_FILE_EXTENSION).PHP_EOL.PHP_EOL;
		
		$pageExclusions = ['home', 'site_map']; // TODO: Change to @@ parameter in page
		$files = listdir_by_name('../content/pages/*.'.MARKDOWN_FILE_EXTENSION);
		
		foreach ($files as $file) {
			if (!in_array(basename($file,".".MARKDOWN_FILE_EXTENSION), $pageExclusions)) {
				$filename = basename($file);
				$page = new Page($filename);
				$siteMapPageContents .= "- [".$page->title."](/page/".$page->pageName.") ".PHP_EOL;
			}
		}
		
		$sitemapPagePath = $_SERVER['DOCUMENT_ROOT'].'/content/pages/'.SITEMAP_PAGE_NAME.'.'.MARKDOWN_FILE_EXTENSION;
		return file_put_contents($sitemapPagePath, $siteMapPageContents);
	}
	
	function makeRSSFeed() {
		$files = listdir_by_date('../posts/*.'.MARKDOWN_FILE_EXTENSION);
		
		$feed = "<?xml version='1.0' encoding='UTF-8'?>".PHP_EOL."<rss version='2.0'>".PHP_EOL;
		$feed .= "  <channel>".PHP_EOL;
		
		$feed .= "    <title>".RSS_TITLE."</title>".PHP_EOL;
		$feed .= "    <link>".SITE_URL."</link>".PHP_EOL;
		$feed .= "    <description>".RSS_DESCRIPTION."</description>".PHP_EOL;
		$feed .= "    <language>".RSS_LANGUAGE."</language>".PHP_EOL;
		
		foreach ($files as $file) {
			$filename = basename($file);
			$post = new Post($filename);
			
			if ($post->hasContent()) {
				$post->showTitle = false;
				
				$feed .= "    <item>".PHP_EOL;
				$feed .= "      <title>{$post->title}</title>".PHP_EOL;
				$feed .= "      <link>{$post->fullURL()}</link>".PHP_EOL;
				$feed .= "      <guid>{$post->relativeURL()}</guid>".PHP_EOL;
				$feed .= "      <pubDate>{$post->feedPublishDate()}</pubDate>".PHP_EOL;
				$feed .= "      <description><![CDATA[{$post->html()}]]></description>".PHP_EOL;
				$feed .= "    </item>".PHP_EOL;
			}
		}
		
		$feed .= "  </channel>".PHP_EOL;
		$feed .= "</rss>".PHP_EOL;
		
		$rssPagePath = $_SERVER['DOCUMENT_ROOT'].'/rss.xml';
		return file_put_contents($rssPagePath, $feed);
	}

	private function updateWorkingDirectory() {
		$workingDirectory = getcwd();
		
		if ($workingDirectory) {
			if (hasSuffix('/Ceres', $workingDirectory)) {
				if (!chdir('..')) {
					die("Couldn't select working directory"); 
				}
			}
		} else {
			die("Couldn't get working directory"); 
		}
	}
}

?>
