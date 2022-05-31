<?php
//头标识,允许跨域
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

//获取传入参数
$av = isset($_POST['av']) ? intval($_POST['av']) : 0;
$bv = isset($_POST['bv']) ? $_POST['bv'] : '';
$ep = isset($_POST['ep']) ? intval($_POST['ep']) : 0;
$otype = isset($_POST['otype']) ? $_POST['otype'] : 'json';
$otype = in_array($otype, ['json', 'url', 'dplayer']) ? $otype : 'json';
$p = isset($_POST['p']) ? intval($_POST['p']) : 1;
$q = isset($_POST['q']) ? intval($_POST['q']) : 32;
$type = isset($_POST['type']) ? $_POST['type'] : 'video';
$format = isset($_POST['format']) ? $_POST['format'] : 'flv';

//引用解析包。该包来自于:https://github.com/injahow/bilibili-parse
include __DIR__ . '/src/Bilibili.php';
use Injahow\Bilibili;
$bp = new Bilibili($type); 
$bp->epid($ep);
$bp->aid($av)->bvid($bv)->page($p);
$bp->quality($q)->format($format);
$result = json_decode($bp->result(), true);
//返回json

if ($format == 'dash' || $otype == 'json') {
    header('Content-type: application/json; charset=utf-8;');
    echo json_encode($result);
}
