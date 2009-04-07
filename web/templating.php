<?php
require_once("config.php");
require_once("auth.php");
require_once("smarty/Smarty.class.php");

$smarty = new Smarty();
$smarty->template_dir = $install_path . "/themes/". $default_theme . "/templates/";
$smarty->compile_dir = $install_path. "/themes/" . $default_theme . '/templates_c/';
$smarty->assign("base_url", $base_url);
if($logged_in) {
	$smarty->assign("logged_in", true);
	$smarty->assign("username", $username);
}

?>
