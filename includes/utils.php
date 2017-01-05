<?
/**
 *  __  __     ______     ______     __     ______
 * /\ \/ /    /\  ___\   /\  __ \   /\ \   /\  == \
 * \ \  _"-.  \ \  __\   \ \ \/\ \  \ \ \  \ \  __<
 *  \ \_\ \_\  \ \_____\  \ \_____\  \ \_\  \ \_\ \_\
 *   \/_/\/_/   \/_____/   \/_____/   \/_/   \/_/ /_/
 *
 * Copyright (c) 2017. Developed by Zackary Pedersen, all rights reserved.
 * zackary@snaju.com - keoir.com - @keoir
 */

/**
 * Class Utils
 */
class Utils
{

    /**
     * @param $check
     * @param $arrayOfOptions
     * @return bool
     */
    static function equalOr($check, $arrayOfOptions)
    {
        $pass = false;
        foreach ($arrayOfOptions as $opt) {
            if ($check == $opt) {
                $pass = true;
            }
        }

        return $pass;
    }

    /**
     * @param $check
     * @param $arrayOfOptions
     * @return bool
     */
    static function equalAnd($check, $arrayOfOptions)
    {
        $pass = true;
        foreach ($arrayOfOptions as $opt) {
            if ($check != $opt) {
                $pass = false;
            }
        }

        return $pass;
    }

    /**
     * @param $ts
     * @param null $timeZone
     * @return false|string
     */
    public static function formatRelativeTime($ts, $timeZone = null)
    {
        if (!is_numeric($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;

        if ($diff == 0) {
            return 'now';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) return 'just now';
                if ($diff < 120) return '1 minute ago';
                if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
                if ($diff < 7200) return '1 hour ago';
                if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
            }
            if ($day_diff == 1) {
                return 'Yesterday';
            }
            if ($day_diff < 7) {
                return $day_diff . ' days ago';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7) . ' weeks ago';
            }
            if ($day_diff < 60) {
                return 'last month';
            }
            return date('F Y', $ts);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120) {
                    return 'in a minute';
                }
                if ($diff < 3600) {
                    return 'in ' . floor($diff / 60) . ' minutes';
                }
                if ($diff < 7200) {
                    return 'in an hour';
                }
                if ($diff < 86400) {
                    return 'in ' . floor($diff / 3600) . ' hours';
                }
            }
            if ($day_diff == 1) {
                return 'Tomorrow';
            }
            if ($day_diff < 4) {
                return date('l', $ts);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return 'next week';
            }
            if (ceil($day_diff / 7) < 4) {
                return 'in ' . ceil($day_diff / 7) . ' weeks';
            }
            if (date('n', $ts) == date('n') + 1) {
                return 'next month';
            }
            return date('F Y', $ts);
        }
    }

    /**
     * @param $string
     * @param bool $timeZoneAlter
     * @param null $timeZoneString
     * @return false|string
     */
    static function makeDBTimeStamp($string, $timeZoneAlter = false, $timeZoneString = null)
    {
        if ($timeZoneAlter && $timeZoneString != null) {
            $string = self::convertToGMT($string, $timeZoneString);
        } else if ($timeZoneAlter && $timeZoneString == null && self::$timeZone != null) {
            $string = self::convertToGMT($string, self::$timeZone);
        }

        if (is_numeric($string)) {
            if ($string == 0) {
                return "0000-00-00 00:00:00";
            }
            $time = $string;
        } else {
            $time = strtotime($string);
        }
        return date("Y-m-d H:i:s", $time);
    }

    /**
     * @param $length
     * @return string
     */
    static function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param string $date
     * @return false|string
     */
    static function getTimeStamp($date = "")
    {
        if ($date == "") {
            $datetime = time();
            $date2 = date("m/d/y g:i A", $datetime);
        } else {
            if (is_string($date)) {
                $datetime = strtotime($date);
            } else {
                $datetime = $date;
            }
            $date2 = date("m/d/y g:i A", $datetime);
        }

        return $date2;
    }

    /**
     * @param $dayOfTheMonth
     * @param bool $returnOnlySubPart
     * @param string $tag
     * @return string
     */
    static function getDateRep($dayOfTheMonth, $returnOnlySubPart = false, $tag = "i")
    {
        if ($dayOfTheMonth <= 3) {
            if ($dayOfTheMonth == 1) {
                $rep = "st";
            } else if ($dayOfTheMonth == 2) {
                $rep = "nd";
            } else if ($dayOfTheMonth == 3) {
                $rep = "rd";
            }
        } else if ($dayOfTheMonth > 3 && $dayOfTheMonth < 31) {
            $rep = "th";
        } else if ($dayOfTheMonth >= 31) {
            $rep = "st";
        }

        return ($returnOnlySubPart) ? $rep : $dayOfTheMonth . "<$tag>" . $rep . "</$tag>";
    }

    /**
     * @param $find
     * @param $arrayObject
     * @param bool $searchValues
     * @param bool $returnBoolean
     * @return bool|int|string
     */
    static function searchArray($find, $arrayObject, $searchValues = false, $returnBoolean = false)
    {

        foreach ($arrayObject as $k => $v) {
            if (is_array($v)) {
                return self::searchArray($find, $v);
            } else {
                if ($find == $k) {
                    return ($returnBoolean) ? true : $v;
                } else if ($find == $v && $searchValues) {
                    return ($returnBoolean) ? true : $k;
                }
            }
        }
        return false;
    }

    /**
     * @param $number
     * @return string
     */
    static function returnFinicialStatment($number)
    {
        $abs = abs($number);
        if ($number < 0) {
            $s = "($" . Utils::formatCurency($abs) . ")";
        } else if ($number > 0) {
            $s = "$" . Utils::formatCurency($abs) . "";
        } else {
            $s = " - ";
        }

        return $s;
    }

    /**
     * @param $bool
     * @param bool $addCurencySign
     * @param bool $allowNegative
     * @return int|string
     */
    static function formatCurency($bool, $addCurencySign = false, $allowNegative = false)
    {
        if ($bool < 0 && !$allowNegative) {
            $bool = 0;
        }

        $bool = number_format(round($bool, 2), 2);

        if ($addCurencySign) {
            $bool = "$" . $bool;
        }

        return $bool;
    }

    /**
     * @param $startString
     * @param $endString
     * @param $string
     * @return mixed
     */
    static function between($startString, $endString, $string)
    {
        $string = explode($startString, $string);
        if (count($string) > 1) {
            $string = explode($endString, $string[1]);
        }
        return $string[0];
    }

    /**
     * @param $a
     * @param bool $jsonToArrays
     * @return object
     */
    static function arrayToObject($a, $jsonToArrays = false)
    {
        return (object)$a;
    }

    /**
     * @param $postString
     * @return mixed
     */
    static function postStringToArray($postString)
    {
        parse_str($postString, $return);
        return $return;
    }

    /**
     * @param null $domain
     * @param int $parts
     * @return null|string
     */
    public static function getBaseDomain($domain = null, $parts = 2)
    {
        if ($domain == null) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        $parts = explode(".", $domain);
        $count = count($parts);

        if ($count > $parts) {
            $host = $parts[$count - $parts] . "." . $parts[$count - 1];
        } else {
            $host = $domain;
        }

        return $host;
    }

    /**
     * @param $string
     * @param array $data
     * @return string
     */
    static function parseURL($string, $data = array())
    {
        $check = preg_match("/((https|http)\\:)?[-a-zA-Z0-9@:%_\\+.~#?&\\\\=]{2,256}\\.[a-z]{2,4}\\b(\\/[-a-zA-Z0-9@:%_\\+.~#?&\\\\=]*)?/i", $string);

        if ($check) {
            if (preg_match("/^(http|https)\\:\\/\\//i", $string)) {
                $s = $string;
            } else if (preg_match("/^\\/\\/(.*)/i", $string)) {
                $s = "http:" . $string;
            } else {
                $s = "http://" . $string;
            }

            if (preg_match("/\\?(.*)\\=(.*)/i", $s)) {
                $split = "&";
            } else {
                $split = "?";
            }

            foreach ($data as $key => $value) {
                $s .= $split . $key . "=" . $value;
                $split = "&";
            }

            return $s;
        }
    }

    /**
     * @param $urlString
     * @param array $newParts
     * @param null $excludeContainingWord
     * @return array
     */
    static function mergeURLParts($urlString, $newParts = [], $excludeContainingWord = null)
    {
        $a = [];
        $parts = explode("&", $urlString);
        foreach ($parts as $part) {
            $part = str_replace("&", "", $part);
            $part = str_replace("?", "", $part);

            if (strpos($part, $excludeContainingWord) == false) {
                if (strpos($part, "=") !== false) {
                    $p = explode("=", $part);
                    $a[$p[0]] = $p[1];
                }

                $a[$part] = "";
            }
        }

        $a = array_merge($a, $newParts);

        return $a;
    }

    /**
     * @param $value
     * @return float|string
     */
    static function bigINT($value)
    {
        if (strpos($value, "E+") !== false) {
            return floor($value);
            return number_format($value, 0, '', '');
        }

        return $value;
    }

    /**
     * @param $domainInput
     * @return mixed
     */
    static function getPlainDomain($domainInput)
    {
        $domain = preg_replace("/((https|http|)?\\/\\/(.*?))\\/(.*)/", "$1", $domainInput);
        return $domain;
    }

    /**
     * @param $number
     * @return string
     */
    static function parsePhoneNumber($number)
    {
        $phoneNumber = Utils::stringToNumber($number);
        if (strpos($number, ":") !== false) {
            $num = explode(":", $number);
            $phoneNumber = Utils::stringToNumber($num[0]) . ":" . $num[1];
        }

        return "+" . $phoneNumber;
    }

    /**
     * @param $string
     * @return mixed
     */
    static function stringToNumber($string)
    {
        $negative = false;
        if (strpos($string, "-") !== false) {
            $negative = true;
        }
        $string = preg_replace("/[^0-9\\.]+/", "", $string);
        if ($negative) {
            $string = $string * -1;
        }
        return $string;
    }

    /**
     * @param $number
     * @return string
     */
    static function formatPhoneNumber($number)
    {
        if (preg_match('/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|4[987654310]|3[9643210]|2[70]|7|1)(\d{3})(\d{3})(\d{4})\:([0-9]*)/', $number, $m)) {
            return "+" . $m[1] . " (" . $m[2] . ") " . $m[3] . " " . $m[4] . " Ex:" . $m[5];
        } else if (preg_match('/(\d{3})(\d{3})(\d{4})\:([0-9]*)/', $number, $m)) {
            return "+1 (" . $m[1] . ") " . $m[2] . " " . $m[3] . " Ex:" . $m[4];
        } else if (preg_match('/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|4[987654310]|3[9643210]|2[70]|7|1)(\d{3})(\d{3})(\d{4})/', $number, $m)) {
            return "+" . $m[1] . " (" . $m[2] . ") " . $m[3] . " " . $m[4];
        } else if (preg_match('/(\d{3})(\d{3})(\d{4})/', $number, $m)) {
            return "+1 (" . $m[1] . ") " . $m[2] . " " . $m[3];
        } else {
            return "(---) --- ----";
        }
    }

    /**
     * @param $array
     * @param $element
     * @return array
     */
    static function deleteFromArray($array, $element)
    {
        return (is_array($element)) ? array_values(array_diff($array, $element)) : array_values(array_diff($array, array($element)));
    }

    /**
     * @param $string
     * @param $lookFor
     * @return mixed
     */
    static function replaceMany($string, $lookFor)
    {
        foreach ($lookFor as $look => $replace) {
            $string = str_replace($look, $replace, $string);
        }
        return $string;
    }

    /**
     * @param $data
     * @return bool
     */
    static function is_serialized($data)
    {
        // if it isn't a string, it isn't serialized
        if (!is_string($data))
            return false;
        $data = trim($data);
        if ('N;' == $data)
            return true;
        if (!preg_match('/^([adObis]):/', $data, $badions))
            return false;
        switch ($badions[1]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                    return true;
                break;
        }
        return false;
    }

    /**
     * @param $string
     * @return bool
     */
    static function isURL($string)
    {
        $check = preg_match("/((https|http)\\:)?[-a-zA-Z0-9@:%_\\+.~#?&\\\\=]{2,256}\\.[a-z]{2,4}\\b(\\/[-a-zA-Z0-9@:%_\\+.~#?&\\\\=]*)?/i", $string);

        if ($check) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $regexArray
     * @param $subject
     * @param $setting
     * @return array
     */
    static function pregMatchMany($regexArray, $subject, $setting)
    {
        $a = array();
        if (is_array($regexArray)) {
            foreach ($regexArray as $r) {
                preg_match_all($r, $subject, $b, $setting);
                foreach ($b as $i => $d) {
                    $a[] = $d;
                }
            }
        }

        return $a;
    }

    /**
     * @param $array
     * @param string $prefix
     * @return array
     */
    static function prefixArray($array, $prefix = "")
    {
        $new = [];
        foreach ($array as $key => $value) {
            $new[$prefix . $key] = $value;
        }

        return $new;
    }

    /**
     * @param $array
     * @return array
     */
    static function cleanArray($array)
    {
        $a = [];
        foreach ($array as $k => $value) {
            if ($value != "" && !empty($value)) {
                $a[] = $value;
            }
        }

        return $a;
    }

    /**
     * @param $string
     * @param bool $return_data
     * @return bool|mixed
     */
    static function isJSON($string, $return_data = false)
    {
        $data = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
    }
}

new Utils();

?>