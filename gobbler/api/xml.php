<?php
class XML {
    public static function prettyXML($xml) {
	$dom = new DOMDocument('1.0'); 
	$dom->preserveWhitespace = false;
	$dom->loadXML(utf8_encode($xml->asXML()));  
	$dom->formatOutput = true;
	return($dom->saveXML());
    } 

    public static function error($status, $errcode, $errtext) {
	$xml = new SimpleXMLElement("<lfm></lfm>");
	$xml->addAttribute("status", $status);
	$error = $xml->addChild("error", $errtext);
	$error->addAttribute("code", $errcode);
	return($xml);
    }
}
?>
