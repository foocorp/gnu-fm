<?php
    /*
     * Humanized timestamps
     */

    function human_timestamp ($unix_timestamp, $now = null) {
        if (is_null($now)) {
            $now = time(); 
        }
        
        $diff = $now - $unix_timestamp;

        # ugly!
        $number_to_alpha = array(
            'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
            'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty',
            'twenty-one', 'twenty-two', 'twenty-three', 'twenty-four', 'twenty-six', 'twenty-seven', 'twenty-eight', 'twenty-nine', 'thirty',
            'thirty-one', 'thirty-two', 'thirty-three', 'thirty-four', 'thirty-five', 'thirty-six', 'thirty-seven', 'thirty-eight', 'thirty-nine',
            'fourty-one', 'fourty-two', 'fourty-three', 'fourty-four', 'fourty-five', 'fourty-six', 'fourty-seven', 'fourty-eight', 'fourty-nine',
            'fifty-one', 'fifty-two', 'fifty-three', 'fifty-four', 'fifty-five', 'fifty-six', 'fifty-seven', 'fifty-eight', 'fifty-nine'
        );

        switch ($unix_timestamp) {
        case ($now < $unix_timestamp):
            return 'in the future (?)';
            break;
        case ($diff == 1):
            # one second
            return 'a second ago';
            break;
        case ($diff < 60):
            # less than a minute
            return $diff . ' seconds ago';
            break;
        case ($diff < 120):
            # between a minute and two
            return 'about a minute ago';
            break;
        case ($diff < 3600):
            # less than an hour
            return round($diff / 60) . ' minutes ago';
            break;
        case ($diff < 7200):
            # between an hour and two
            return 'about an hour ago';
            break;
        case ($diff < 86400):
            # less than a day
            return round($diff / 3600) . ' hours ago';
            break;
        case ($diff < 172800):
            # less than two days
            return 'about a day ago';
            break;
        case ($diff < 604800):
            # less than a week
            return round($diff / 86400) . ' days ago';
            break;
        case ($diff < 691200):
            # a week an a day
            return 'about a week ago';
            break;
        case ($diff < 2764800):
            # less than a month
            return round($diff / 691200) . ' weeks ago';
            break;
        case ($diff < 4579200):
            # a month and three weeks
            return 'about a month ago';
            break;
        case ($diff < 33177600);
            # less than a year
            return round($diff / 2764800) . ' months ago';
            break;
        case ($diff < 35942400):
            # a year and a month
            return 'about a year ago';
            break;
        case ($diff > 35942400):
            return 'more than a year ago';
            break;
        }
    }
?>
