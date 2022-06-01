<?php

/**
 * bilbili danmaku api
 * @link https://mdzz.pro
 * Version 1.1.3
 *
 * Copyright 2022, HaruhiYunona
 * Released under the MIT license
 */



//头标识,允许跨域
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('content-type:application/json;charset=utf-8');
header ( " Expires: Mon, 26 Jul 1970 05:00:00 GMT " );
header ( " Cache-Control: no-cache, must-revalidate " );
header ( " Pragma: no-cache " );

//根据BV号获取cid
$bv = isset($_GET['bv']) ? $_GET['bv'] : '';
$page = isset($_GET['p']) ? $_GET['p'] - 1 : 0;

//curl获取对应BV的cid
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.bilibili.com/x/player/pagelist?bvid=' . $bv . '&jsonp=jsonp');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
$myvideoInfo = json_decode(curl_exec($ch), true);
curl_close($ch);
if ($myvideoInfo['code'] == 0) {
    $getmycid = $myvideoInfo['data'][$page]['cid'];

    //根据cid获取弹幕并返回
    $ch = curl_init();
    $url = 'https://api.bilibili.com/x/v1/dm/list.so?oid=' . $getmycid;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    $videoInfo = curl_exec($ch);
    curl_close($ch);

    //截取中间字符方法
    function cut($begin, $end, $str)
    {
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }

    //分解弹幕xml
    if (strstr($videoInfo, '<source>') !== null) {
        $videoInfo = cut('</source>', '</i>', $videoInfo);
        $danmakuArray = explode("<d", $videoInfo);
        foreach ($danmakuArray as $row) {
            $danmakuData = cut('p="', '">', $row);
            $danmakuText = cut('">', '</d>', $row);
            $danmakuDataArray = explode(",", $danmakuData);
            if ($danmakuDataArray[3] != null) {
                $danmakuInfoArray = [(float)$danmakuDataArray[0], (int)$danmakuDataArray[5], $danmakuDataArray[3], $danmakuDataArray[6], $danmakuText];
                $danmakuStream[] = $danmakuInfoArray;
            }
        }

        if (is_array($danmakuStream)&&$danmakuStream!==null) {
            echo json_encode(array(
                "code" => 0,
                "msg"=>"外挂弹幕装填完成",
                "data" => $danmakuStream
            ), JSON_UNESCAPED_UNICODE);
        } else {
            $danmaku = [[0,0,"#fff","eeeeee"," "]];
            echo json_encode(array(
                "code" => 0,
                "msg" => "无外挂弹幕",
                "data"=>$danmaku
            ), JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(array(
            "code" => 0,
            "msg" => "无外挂弹幕"
        ), JSON_UNESCAPED_UNICODE);
    }
}
