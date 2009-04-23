<?php

require_once('../../database.php');
require_once('xml.php');


class API {
    function __construct() {
        $this->api_errors = array(
            2  => 'Invalid Service - This service does not exist',
            3  => 'Invalid Method - No method with that name in this package',
            4  => 'Authentication Failed - You do not have permissions to access the service',
            5  => 'Invalid format - This service doesn\'t exist in that format',
            6  => 'Invalid parameters - Your request is missing a required parameter',
            7  => 'Invalid resource especified',
            8  => '',
            9  => 'Invalid session key',
            10 => 'Invalid API key - You must be granted a valid key by libre.fm',
            11 => 'Service Offline - This Service is temporarily offline. Try again later.',
            12 => 'Subscription Error - The user needs to be subscribed in order to do that',
            13 => 'Invalid method signature supplied',
            14 => '',
            15 => '',
            16 => '',
            17 => '',
            18 => 'This user has no free radio plays left. Subscription required.',
            19 => '',
            20 => '',
         );
    }

    static public function checkParams($params, $required, $optional = null) {
        /*
         * @param array $params parameters
         * @param array $required required params
         */
        if (empty($required) || empty($params)) return false;
        if (count(array_diff_key($params, $required) > 0)) {
            $this->giveError(6);
        }
    }

    static public function giveError($error) {
        /*
         * @param integer $error error key to give back
         */

        global $xml;
        return (XML::error('failed', $error, $this->api_error[$error]));
    }
}

?>
