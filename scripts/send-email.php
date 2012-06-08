#!/usr/bin/env php5
<?php

if (PHP_SAPI != 'cli') {
	die("This script must be run from the commandline.\n");
}

require_once('adodb/adodb-exceptions.inc.php');
require_once('adodb/adodb.inc.php');

$connect_string = 'postgres://dbname=librefm';
$delay = 1;

try {
	$adodb =& NewADOConnection($connect_string);
} catch (Exception $e) {
	var_dump($e);
	adodb_backtrace($e->gettrace());
}

if(count($argv) < 2) {
	die("Usage: " . $argv[0] . " <filename>\n\nWhere <filename> is a file containing a template of the e-mail you'd like to send.\n");
}

$tfile = fopen($argv[1], 'r');
$header = "From: " . fgets($tfile);
$subject = fgets($tfile);
$template = str_replace('"', '\"', fread($tfile, filesize($argv[1])));
fclose($tfile);

if(empty($template)) {
	die("The template appears to be empty, giving up.");
}

$res = $adodb->GetAll("SELECT username, md5(md5(email) || md5(password)) as unsubid, email FROM Users WHERE receive_emails = 1 AND active = 1 AND email <> '' ORDER BY uniqueid ASC");
$total = count($res);

echo "\nYou're about to send " . $total . " e-mails, with a " . $delay . " second delay
between each. This is estimated to take roughly " . round(($total * $delay) / 3600, 2) . " hours. 
	
The text of the message will look as follows:\n\n\n";

// Show an example
$username = "Elleo";
$unsubscribe = "http://alpha.libre.fm/unsubscribe.php?id=dummy";
$emailtext = eval('return "' . $template . '";');

echo $header;
echo "Subject: " . $subject . "\n";

echo $emailtext;

system("stty -icanon");

$c = '';
do {
	if ($c == 'n') {
		die("\nAborting!\n");
	}
	echo "\n\nAre you sure you want to send this message to " . $total . " people? (y/n): ";
} while(($c = fread(STDIN, 1)) != 'y');

echo "\n";

$i = 1;
foreach($res as &$row) {

	$username = $row['username'];
	$email = $row['email'];
	$unsubscribe = "http://alpha.libre.fm/unsubscribe.php?id=" . $row['unsubid'];
	$emailtext = eval('return "' . $template . '";');

	printf("[%" . strlen($total) . "s/%s]", $i, $total);
	echo " Sending to " . $username . " (" . $email . ")... ";

	mail($email, $subject, $emailtext, $header);
	
	echo "Sent!\n";
	sleep($delay);
	$i++;
}

?>
