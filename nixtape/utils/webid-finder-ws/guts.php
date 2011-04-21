<?php

require '../arc/ARC2.php';

if (!function_exists('json_decode')) {
	function json_decode($content, $assoc = false) {
		require_once 'Services/JSON.php';

		if ($assoc) {
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		} else {
			$json = new Services_JSON;
		}

		return $json->decode($content);
	}
}

if (!function_exists('json_encode')) {
	function json_encode($content) {
		require_once 'Services/JSON.php';
		$json = new Services_JSON;
		return $json->encode($content);
	}
}

function _resolve_relative_url ($absolute, $relative) {
	$p = parse_url($relative);
	if ($p['scheme']) {
		return $relative;
	}

	extract(parse_url($absolute));

	$path = dirname($path);

	if ($relative{0} == '/') {
		$cparts = array_filter(explode('/', $relative));
	} else {
		$aparts = array_filter(explode('/', $path));
		$rparts = array_filter(explode('/', $relative));
		$cparts = array_merge($aparts, $rparts);
		foreach ($cparts as $i => $part) {
			if ($part == '.') {
				$cparts[$i] = null;
			} else if ($part == '..') {
				$cparts[$i - 1] = null;
				$cparts[$i] = null;
			}
		}
		$cparts = array_filter($cparts);
	}

	$path = implode('/', $cparts);
	$url = '';

	if ($scheme) {
		$url = "$scheme://";
	}

	if ($user) {
		$url .= "$user";
		if ($pass) {
			$url .= ":$pass";
		}
		$url .= '@';
	}

	if ($host) {
		$url .= "$host/";
	}

	$url .= $path;

	return $url;
}

function _http($uri) {
	if (function_exists('curl_init')) {
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		return ob_get_clean();
	} else if (function_exists('parse_url')) {
		$_uri = parse_url($uri);
		if (!$_uri['port']) {
			$_uri['port'] = 80;
		}

		if (!($nh = fsockopen($_uri['host'], $_uri['port'], $errno, $errstr, 20))) {
			header('Content-Type: text/plain');
			die("Could not open network connection! ($errno - $errstr)\r\n");
		}

		fwrite($nh, "GET {$_uri[path]}?{$_uri[query]} HTTP/1.0\r\n"
			. "Host: {$_uri['host']}\r\n"
			. "Connection: close\r\n\r\n"
			);
		while (!feof($nh)) {
			$output .= fgets($nh, 128);
		}
		fclose($nh);

		// Remove HTTP header.
		return substr(strstr($output, "\r\n\r\n"), 4);
	}

	return null;
}

function getFromLaconica($account) {
	if (!preg_match('/^https?:\/\//i', $account)) {
		$account = "http://identi.ca/{$account}";
	}
	preg_replace('/\/$/', '', $account);

	$foaf = $account . '/foaf';

	return getFromFOAF($foaf);
}

function getFromFOAF($foaf, $knownHomepage = null, $data = null) {
	$parser = ARC2::getRDFParser();
	if (empty($data)) {
		$parser->parse($foaf);
	} else {
		$parser->parse($foaf, $data);
	}
	$index = $parser->getSimpleIndex();

	if ($index[$foaf]['http://xmlns.com/foaf/0.1/primaryTopic'][0]) {
		$webid = $index[$foaf]['http://xmlns.com/foaf/0.1/primaryTopic'][0];
	}

	if (!$webid) {
		foreach ($index as $subject => $dummy) {
			if ($index[$subject]['http://xmlns.com/foaf/0.1/homepage']) {
				foreach ($index[$subject]['http://xmlns.com/foaf/0.1/homepage'] as $homepage) {
					if ($homepage == $knownHomepage || $homepage == $foaf) {
						$webid = $subject;
						break 2;
					}
				}
			}
			if ($index[$subject]['http://xmlns.com/foaf/0.1/weblog']) {
				foreach ($index[$subject]['http://xmlns.com/foaf/0.1/weblog'] as $homepage) {
					if ($homepage == $knownHomepage) {
						$webid = $subject;
						break 2;
					}
				}
			}
		}
	}

	if ($webid) {
		$r = array(
			'WebID' => $webid,
			'Pages' => $index[$webid]['http://xmlns.com/foaf/0.1/homepage'],
			'Name' => $index[$webid]['http://xmlns.com/foaf/0.1/name'][0]
			);

		if (substr($r['WebID'], 0, 2) == '_:') {
			$r['WebID'] = 'http://thing-described-by.org/?'.$foaf;
		}

		return $r;
	}

	return null;
}

function getFromMyOpera($account) {
	return array(
		'WebID' => "http://my.opera.com/{$account}/xml/foaf#me",
		'Pages' => array("http://my.opera.com/{$account}/")
		);
}

function getFromWebsite($url) {
	$str = _http($url);
	if (preg_match('/xmlns\:[A-Za-z0-9\.\_\-]+\=.?http...xmlns.com.foaf.0.1/', $str)) {
		$r = getFromFOAF($url, $url, $str);
		if ($r['WebID']) {
			return $r;
		}
	}

	$doc = new DOMDocument();
	$e = error_reporting(1);
	$doc->loadHTML($str);
	error_reporting($e);
	$links = $doc->getElementsByTagName('link');

	foreach ($links as $l) {
		if (preg_match('/\b(meta)\b/i', $l->getAttribute('rel'))) {
			$foaf = _resolve_relative_url($url, $l->getAttribute('href'));
			$info = getFromFOAF($foaf, $url);
			if ($info['WebID']) {
				return $info;
			}
		}
	}

	return getFromGoogleSocialGraphAPI($url);
}

function getFromGoogleSocialGraphAPI($url) {
	$api   = "http://socialgraph.apis.google.com/lookup?pretty=1&sgn=1&edi=1&edo=1&fme=1&q={$url}";
	$data  = json_decode(_http($api), 1);
	$canon = $data['canonical_mapping'][$url];

	if (substr($canon, 0, 3) == 'sgn') {
		return array(
			'WebID' => $canon,
			'Homepages' => array($url)
			);
	}
}

function getFromEmail($addr) {
	if (!substr($addr, 0, 7) == 'mailto:') {
		$addr = 'mailto:' . $addr;
	}

	return array(
		'WebID' => 'http://foaf.me/mbox/' . sha1(strtolower($addr)) . '#me'
		);
}

function getBestGuess($string) {
	if (preg_match('#^http://identi.ca/([^/]+)#i', $string, $matches)) {
		return getFromLaconica($string);
	} else if (preg_match('/\@/', $string)) {
		return getFromEmail($email);
	}

	return getFromWebsite($string);
}
