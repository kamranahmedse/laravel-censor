<?php return [

    /*
    |--------------------------------------------------------------------------
    | Replace list
    |--------------------------------------------------------------------------
    | An associative array with a list of words that you want to replace. 
    | keys of the array will be the words that you want to replace and the
    | values will be the words with which the key words will be replaced e.g.
    | 
    |     'replace' => [
    |         'idiot'    => '(not a nice word)',
    |         'seventh'  => '7th',
    |         'monthly'  => 'every month',
    |         'yearly'   => 'every year',
    |         'weekly'   => 'every week',
    |     ],
    |
    | In this case "idiot" will be replaced with "(not a nice word)", "seventh"
    | will be replaced with "7th" and so on.
    |
    */
    'replace' => [
        // 'to censor'    => 'censored word',
        // 'another word' => 'another replacement',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redact List
    |--------------------------------------------------------------------------
    | Specify the words that you want to completely redact. The words 
    | specified in here will be replaced with asterisks (*) e.g.
    |
    |    'redact' => [
    |       'idiot',
    |       'password',
    |       'word-that-i-really-dislike',
    |    ],
    |
    |  In this case "idiot" will be replaced with *****
    |  password with ******** and so on
    |
    */
    'redact' => [
        // 'idiot', 
        // 'rubbish'
    ],

];