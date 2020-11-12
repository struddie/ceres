<?php

#
# Ceres
# (c)2020 Simon Strudwick
# 

class Page {
	
	protected $pageContent;
	protected $markdownDirectory;
	
	public $pageName;
	public $title;
	public $parent;
	public $tags;
	public $author;
	public $timestamp;
	public $date;
	public $type;
	
	public $showTitle;
	
	function __construct($pageName, $type = 'page') {
		$this->pageName = basename($pageName, '.'.MARKDOWN_FILE_EXTENSION);
		$this->type = $type;
		$this->title = ucwords(str_replace('_', ' ', $this->pageName));
		$this->markdownDirectory = $type."s";
		$this->showTitle = ($type !== 'fragment');
		
		if (!hasSuffix(MARKDOWN_FILE_EXTENSION, $pageName)) {
			$fileName = "{$pageName}.".MARKDOWN_FILE_EXTENSION;
		} else {
			$fileName = $pageName;
		}
		
		$pageFilePath = $_SERVER['DOCUMENT_ROOT']."/{$this->markdownDirectory}/{$fileName}";
		$this->pageContent = file_get_contents($pageFilePath);
		
		if ($type !== 'fragment') {
			$this->getValues();
		}
	}
	
	function hasContent() {
		return $this->pageContent !== false;
	}
	
	function excerpt($wordLimit) {
		// Source: https://www.hashbangcode.com/article/getting-excerpt-text-php
		$tok = strtok($this->pageContent, " ");
		
		$text = "";
		$words = 0;
		while ($tok !== false) {
			$text .= " ".$tok;
			$words++;
			if (($words >= $wordLimit) && ((substr($tok, -1) == "!") || (substr($tok, -1) == ".") || (substr($tok, -1) == "?"))) {
				break;
			}
			$tok = strtok(" ");
		}
		
		return ltrim($text);
	}
	
	function html() {
		if ($this->showTitle && $this->title !== '') {
			$pageContent = "# ".$this->title.PHP_EOL.PHP_EOL.$this->pageContent;			
		} else {
			$pageContent = $this->pageContent;
		}
		
		$Parsedown = new Parsedown();
		return $Parsedown->text($pageContent);
	}
	
	public function headerHTML() {
		return '';
	}

	public function footerHTML() {		
		$html = '';
		
		$showSlug = $this->type === 'post' ? SHOW_SLUG_ON_POSTS : SHOW_SLUG_ON_PAGES;
		
		if ($showSlug && $this->author !== null) {
			$html = "<p>By {$this->author} on {$this->formattedDate()}</p>";
		}
		
		if (SHOW_TAGS_ON_PAGES) {
			$tagHTML = "";
			if ($this->tags !== null && array_count_values($this->tags) > 0) {
				foreach ($this->tags as $tag) {
					$tagHTML = $tagHTML."<span class=\"tag\">{$tag}</span>";
				}				
				$html = $html."<p>{$tagHTML}</p>";
			}
		}
		
		return $html;
	}	
	
	public function formattedDate() {
		if ($this->date !== null && $this->date !== false) {
			if ($this->date->getTimeStamp() >= strtotime('today')) {
				return 'Today';
			} else if ($this->date->getTimeStamp() >= strtotime('yesterday')) {
				return 'Yesterday';
			} else {
				return date_format($this->date,"dS F Y H:i")." GMT";
			}	
		}
		return "";
	}
	
	public function feedPublishDate() {
		if ($this->date !== null && $this->date !== false) {
			return date_format($this->date, DATE_RSS);
		}
		return '';
	}

	public function relativeURL() {
		return '/'.$this->type.'/'.$this->pageName;
	}

	public function fullURL() {
		return SITE_URL.$this->type.'/'.$this->pageName;
	}

	private function getValues() {
		if ($this->pageContent === false or $this->pageContent === null or $this->pageContent === "") {
			return;
		}
		
		$newContent = '';
		$isEscaped = false;
		
		foreach (explode(PHP_EOL, $this->pageContent) as $line) {
			if ($line === '```') {
				$isEscaped = !$isEscaped;
			}
			
			if (!$isEscaped && hasPrefix('@@', $line)) {
				$params = explode('=', str_replace('@@', '', $line));
				if (count($params) > 1) {
					$key = strtolower(trim($params[0]));
					$value = trim($params[1]);
					
					if ($key === 'tags') {
						$tags = $value;
						$this->$key = ($tags != '') ? explode(',', $tags) : null;
					} else if ($key === 'show_title') {
						$this->showTitle = !(strtolower($value) === 'false' || strtolower($value) === 'no');
					} else {
						$this->$key = $value;	
					}
					
					if ($this->timestamp !== null) {
						$this->date = date_create_from_format('Y/m/d H:i:s',$this->timestamp);
					}
				}
			} else {
				$newContent .= $line.PHP_EOL;
			}
		}
		$this->pageContent = $newContent;	
	}
}

class Post extends Page {
	function __construct($postName) {
		parent::__construct($postName, 'post');
	}
}

?>