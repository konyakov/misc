<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Скрипт, получает все ссылки с сайта | КОНЬЯКОВ.ру</title>
	<meta name="description" content="Скрипт, получает все ссылки с сайта | КОНЬЯКОВ.ру" />
  </head>
  <body>
<p><a href="javascript:history.back();"><small>Вернуться назад</small></a></p>
<h1>Скрипт, получает все ссылки с сайта | <a href="http://konyakov.ru/">КОНЬЯКОВ.ру</a></h1>
<p><?php
//скрипт, получает все ссылки с сайта
	$site = 'http://konyakov.ru/';
	foreach(get_urls($site) as $url) {
		echo '<a href="'.$url.'">'.$url.'</a><br>'."\n";
	}
	function my_sort($array) {
		$new_array = array();
		foreach($array as $value) {
			$new_array[] = $value;
		}
		return $new_array;
	}
	function DirnameNormal($url) {
		$fulldir = '';
		$explode = explode('/', $url);
		foreach($explode as $i => $dir) {
			if($dir && $i != (count($explode)-1)) {
				$fulldir .= $dir.'/';
			}
		}
		return $fulldir;
	}
	function JoinToSite($url, $site) {
		$domain = parse_url($site);
		$domain = $domain['scheme'].'://'.$domain['host'];
		if($url{0} == '/') {
			$link = $domain.$url;
		} else if(preg_match('~^http(s)?:~i', $url)) {
			if(parse_url($url, PHP_URL_HOST) == parse_url($site, PHP_URL_HOST)) {
				$link = $url;
			}
		} else {
			if(!preg_match('~^(ftp(s)?|javascript|mailto):~i',   $url)) {
				$dirname = DirnameNormal(parse_url($site, PHP_URL_PATH));
				$link = $domain.'/'.$dirname.$url;
			}
		}
		return (isset($link) ? $link : false);
	}
	function GetAllUrlsFromUrl($url, $all_links) {
		$first = file_get_contents($url);
		preg_match_all('~<a[^>]+href[\x20]?=[\x20\x22\x27]?([^\x20\x22\x27\x3E]+)[\x20\x22\x27]?[^>]*>~i',  $first, $second);
		$array_urls = array();
		foreach($second[1] as $link) {
			$link = JoinToSite($link, $url);
			if($link !== false && !in_array($link, $all_links)) {
				$array_urls[] = $link;
			}
		}
		return ((count($array_urls) > 0) ? $array_urls : false);
	}
	function get_urls($url, $all_links = array()) {
		$get_urls = GetAllUrlsFromUrl($url, $all_links);
		if($get_urls) {
			if($all_links == array()) {
				$all_links[] = $url;
			}
			$all_links = array_merge($all_links, $get_urls);
			foreach($get_urls as $url) {
				$GetAllUrls = get_urls($url, $all_links);
				return my_sort(array_unique($GetAllUrls));
			}
		} else {
			return $all_links;
		}
	}
?></p>
</body>
</html>
