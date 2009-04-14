<?php
require_once('API.php');

class album extends API {
    public static function getInfo($params) {
        $required = array('api_key' => 'string');
        $optional = array('artist' => 'string', 'album' => 'string', 'mbid' => 'string', 'lang' => 'string');

        $this->checkParams($params, $required);
    }
}
?>
