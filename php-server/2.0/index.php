<?php

# Error constants
define("LFM_INVALID_SERVICE",       2);
define("LFM_INVALID_METHOD",        3);
define("LFM_INVALID_TOKEN",         4);
define("LFM_INVALID_FORMAT",        5);
define("LFM_INVALID_PARAMS",        6);
define("LFM_INVALID_RESOURCE",      7);
define("LFM_TOKEN_ERROR",           8);
define("LFM_INVALID_SESSION",       9);
define("LFM_INVALID_APIKEY",       10);
define("LFM_SERVICE_OFFLINE",      11);
define("LFM_SUBSCRIPTION_ERROR",   12);
define("LFM_INVALID_SIGNATURE",    13);
define("LFM_SUBSCRIPTION_REQD",    18);

# Error descriptions as per API documentation
$error_text = array(
  LFM_INVALID_SERVICE         => "Invalid service -This service does not exist",
  LFM_INVALID_METHOD          => "Invalid Method - No method with that name in this package",
  LFM_INVALID_TOKEN           => "Invalid authentication token supplied",
  LFM_INVALID_FORMAT          => "Invalid format - This service doesn't exist in that format",
  LFM_INVALID_PARAMS          => "Invalid parameters - Your request is missing a required parameter",
  LFM_INVALID_RESOURCE        => "Invalid resource specified",
  LFM_TOKEN_ERROR             => "There was an error granting the request token. Please try again later",
  LFM_INVALID_SESSION         => "Invalid session key - Please re-authenticate",
  LFM_INVALID_APIKEY          => "Invalid API key - You must be granted a valid key by last.fm",
  LFM_SERVICE_OFFLINE         => "Service Offline - This service is temporarily offline. Try again later.",
  LFM_SUBSCRIPTION_ERROR      => "Subscription Error - The user needs to be subscribed in order to do that",
  LFM_INVALID_SIGNATURE       => "Invalid method signature supplied",
  LFM_SUBSCRIPTION_REQD       => "This user has no free radio plays left. Subscription required."
);

# Resolves method= parameters to handler functions
$method_map = array(
  "auth.gettoken"             => method_auth_gettoken,
  "auth.getsession"           => method_auth_getsession
);

function method_auth_gettoken() {
  if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
    report_failure(LFM_INVALID_SIGNATURE);
  
  $key = md5(time() . rand());
  print("<lfm status=\"ok\">\n");
  print("    <token>$key</token></lfm>");
}

function method_auth_getsession() {
  if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
    report_failure(LFM_INVALID_SIGNATURE);
  
  if (!isset($_GET['token']) || !valid_token($_GET['token']))
    report_failure(LFM_INVALID_TOKEN);
  
  $session = md5(time() . rand());
  
  print("<lfm status=\"ok\">\n");
  print("    <session>\n");
  print("        <name>A User</name>\n");
  print("        <key>$session</key>\n");
  print("        <subscriber>0</subscriber>\n");
  print("    </session>\n");
  print("</lfm>");
}

function valid_api_key($key) {
  return strlen($key) == 32;
}

function valid_api_sig($sig) {
  return strlen($sig) == 32;
}

function valid_token($token) {
  return strlen($token) == 32;
}

function report_failure($code) {
  global $error_text;
  
  print("<lfm status=\"failed\">\n");
  print("    <error code=\"$code\">".$error_text[$code]."</error></lfm>");
  die();
}

if (!isset($_GET['method']) || !isset($method_map[$_GET['method']]))
  report_failure(LFM_INVALID_METHOD);

if (!isset($_GET['api_key']) || !valid_api_key($_GET['api_key']))
  report_failure(LFM_INVALID_APIKEY);

$method = $method_map[$_GET['method']];
$method();

?>