<?php

#
# Ceres
# (c)2020 Simon Strudwick
# 

	function hasPrefix($needle, $haystack) {
		 $length = strlen($needle);
		 return substr($haystack, 0, $length) === $needle;
	}
	
	function hasSuffix($needle, $haystack) {
		$length = strlen($needle);
		if(!$length) {
			return true;
		}
		return substr($haystack, -$length) === $needle;
	}
	
	function listdir_by_date($pathtosearch) {
		$files = glob($pathtosearch);
		usort($files, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});
		
		return $files;
	}

	function listdir_by_name($pathtosearch) {
		$files = glob($pathtosearch);
		usort($files, function($a, $b) {
			return strcasecmp($a, $b);
		});
		
		return $files;
	}
	
	function getParameter($paramName) {
		if (isset($_GET[$paramName])) {
			$paramValue = filter_input(INPUT_GET, $paramName, FILTER_SANITIZE_STRING);
			$paramValue = preg_replace('/[^-a-zA-Z0-9_]/', '', $paramValue);
			if ($paramValue !== null && $paramValue !== "") {
				return $paramValue;
			}
		}
		
		return false;
	}
?>