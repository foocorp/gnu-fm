<?php
require_once("database.php");
require_once("templating.php");

if(!$logged_in) {
	$smarty->display("login.tpl");
	die();
}

if(isset($_POST['invite'])) {

	$errors = "";
	$email = $_POST['email'];
	if(empty($email)) {
		$errors .= "You must enter an e-mail address.<br />";
	}

	if(empty($errors)) {
		$code = md5(md5($username) . time());
		$mdb2->query("INSERT INTO Invitations (inviter, code) VALUES ("
			. $mdb2->quote($username, "text") . ", "
			. $mdb2->quote($code, "text") . ")");

		$url = $base_url . "/register.php?authcode=" . $code;
		$headers = "From: Libre.fm Invitations <invitations@libre.fm>";
		mail($email, "Libre.fm Invitation", "Hi!\n\nClearly " . $username . " really likes you, because they've sent you an inivitation to join http://libre.fm\n Just visit " . $url . " to sign up, all the cool kids are doing it.\n", $headers);
		$smarty->assign("sent", true);
	} else {
		$smarty->assign("errors", $errors);
	}

}

$smarty->display("invite.tpl");
?>
