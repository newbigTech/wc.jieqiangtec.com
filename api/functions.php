<?php
function echo_json($code = '', $msg = 0, $data = array())
{
     header('Response-Server: ' . php_uname('n'));

    /*$msg = gbktoutf($msg);
    $code = gbktoutf($code);
    $data = gbktoutf($data);*/

    $arr = array('code' => $code, 'msg' => $msg, 'data' => $data);
    echo json_encode($arr);
    exit();
}

function gbktoutf($string) {
    $string = charset_encode("gb2312", "utf-8", $string);
    return $string;
}

function charset_encode($_input_charset, $_output_charset, $input) {
    $output = "";
    $string = $input;
    if (is_array($input)) {
        $key = array_keys($string);
        $size = sizeof($key);
        for ($i = 0; $i < $size; $i ++) {
            $string [$key [$i]] = charset_encode($_input_charset, $_output_charset, $string [$key [$i]]);
        }
        return $string;
    } else {
        if (!isset($_output_charset))
            $_output_charset = $_input_charset;
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else
            die("sorry, you have no libs support for charset change.");

        return $output;
    }
}



?>