<?php

namespace app\lakecms\support;

/**
 * 拼音转换
 */
class Pinyin 
{
    /**
     * 获取中文字符拼音首字母组合
     */
    public function getPinyinFirst($zh) 
    {
        if (empty($zh)) {
            return '';
        }
        
        $zhEncode = mb_detect_encoding($zh, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5']);
        $zh = mb_convert_encoding($zh, 'UTF-8', $zhEncode);
        
        $ret = "";
        for ($i = 0; $i < strlen($zh); $i++) {
            $s1 = substr($zh, $i, 1);
            $p  = ord($s1);
            if ($p > 160) {
                $s2 = substr($zh, $i++, 2);
                $ret .= $this->getFirstCharter($s2);
            } else {
                $ret .= $s1;
            }
        }
        
        return $ret;
    }
    
    /**
     * 获取中文字符拼音首字母
     */
    public function getFirstCharter($s0) 
    {
        $fchar = ord($s0{0});
        if ($fchar >= ord("A") and $fchar <= ord("z")) {
            return strtoupper($s0{0});
        }

        $s0Encode = mb_detect_encoding($s0, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5']);
        $s = mb_convert_encoding($s0, 'UTF-8', $s0Encode);
        
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 and $asc <= -20284) {
            return "A";
        }

        if ($asc >= -20283 and $asc <= -19776) {
            return "B";
        }

        if ($asc >= -19775 and $asc <= -19219) {
            return "C";
        }

        if ($asc >= -19218 and $asc <= -18711) {
            return "D";
        }

        if ($asc >= -18710 and $asc <= -18527) {
            return "E";
        }

        if ($asc >= -18526 and $asc <= -18240) {
            return "F";
        }

        if ($asc >= -18239 and $asc <= -17923) {
            return "G";
        }

        if ($asc >= -17922 and $asc <= -17418) {
            return "H";
        }

        if ($asc >= -17417 and $asc <= -16475) {
            return "J";
        }

        if ($asc >= -16474 and $asc <= -16213) {
            return "K";
        }

        if ($asc >= -16212 and $asc <= -15641) {
            return "L";
        }

        if ($asc >= -15640 and $asc <= -15166) {
            return "M";
        }

        if ($asc >= -15165 and $asc <= -14923) {
            return "N";
        }

        if ($asc >= -14922 and $asc <= -14915) {
            return "O";
        }

        if ($asc >= -14914 and $asc <= -14631) {
            return "P";
        }

        if ($asc >= -14630 and $asc <= -14150) {
            return "Q";
        }

        if ($asc >= -14149 and $asc <= -14091) {
            return "R";
        }

        if ($asc >= -14090 and $asc <= -13319) {
            return "S";
        }

        if ($asc >= -13318 and $asc <= -12839) {
            return "T";
        }

        if ($asc >= -12838 and $asc <= -12557) {
            return "W";
        }

        if ($asc >= -12556 and $asc <= -11848) {
            return "X";
        }

        if ($asc >= -11847 and $asc <= -11056) {
            return "Y";
        }

        if ($asc >= -11055 and $asc <= -10247) {
            return "Z";
        }

        return null;
    }

}