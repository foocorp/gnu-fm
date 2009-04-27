<?php
define('BY1',	1);
define('BY2',	2);
define('BY21',	3);
define('BY25',	4);
define('BY3',	5);
define('BYSA1',	6);
define('BYSA2',	7);
define('BYSA21',	8);
define('BYSA25',	9);
define('BYSA3',	10);
define('LAL',	11);

// Arrays containing regular expressions for each license type
// (so we can support multiple URL formats in the future if needed)
$by1 = array("http://creativecommons.org/licenses/by/1.0/?.*");
$by2 = array("http://creativecommons.org/licenses/by/2.0/?.*");
$by21 = array("http://creativecommons.org/licenses/by/2.1/?.*");
$by25 =  array("http://creativecommons.org/licenses/by/2.5/?.*");
$by3 = array("http://creativecommons.org/licenses/by/3.0/?.*");
$bysa1 = array("http://creativecommons.org/licenses/by-sa/1.0/?.*");
$bysa2 =  array("http://creativecommons.org/licenses/by-sa/2.0/?.*");
$bysa21 = array("http://creativecommons.org/licenses/by-sa/2.1/?.*");
$bysa25 = array("http://creativecommons.org/licenses/by-sa/2.5/?.*");
$bysa3 = array("http://creativecommons.org/licenses/by-sa/3.0/?.*");
$lal = array("http://artlibre.org/licence.php/lal.html");

// map licenses to ids by array position
$licenses = array(array(), $by1, $by2, $by21, $by25, $by3, $bysa1, $bysa2, $bysa21, $bysa25, $bysa3, $lal);

function simplify_license($license) {
	global $licenses;

	foreach ($licenses as $key => $l) {
		foreach ($l as $urlschema) {
			if (ereg($urlschema, $license))		return $key;
		}
	}

	return 0;
}
