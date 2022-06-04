<?php

/**
 * Typecho danmaku service api
 * @link https://mdzz.pro
 * Version 1.1.3
 *
 * Copyright 2022, HaruhiYunona
 * Released under the MIT license
 */

class BiliVido_Action extends Typecho_Widget implements Widget_Interface_Do
{
    /** @var  数据操作对象 */
    private $_db;

    /** @var  插件根目录 */
    private $_dir;

    /** @var  插件配置信息 */
    private $_cfg;

    /** @var  系统配置信息 */
    private $_options;

    /** @var bool 是否记录日志 */
    private $_isMailLog = false;

    /** @var 当前登录用户 */
    private $_user;

    /** 自动加载类 */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
    }
    public function init()
    {
        $this->_dir = dirname(__FILE__);
        $this->_db = Typecho_Db::get();
        $this->_user = $this->widget('Widget_User');
        $this->_options = $this->widget('Widget_Options');
        $this->_cfg = Helper::options()->plugin('BiliVido');
    }

    public function execute()
    {
        return;
    }

    /** POST接收处理方法 */
    public function bvGetPost($name)
    {
        return json_decode(file_get_contents("php://input"), true)[$name];
    }

    /** 接口处理方法 */
    public function action()
    {
        $this->init();
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $action = $this->request->action;
        if ($action == 'empty') {
            $db->query('TRUNCATE ' . $prefix . 'bilivido');
            $this->widget('Widget_Notice')->set("弹幕池已被清空!", 'notice');
            $this->response->goBack();
        } else if ($action == 'del') {
            $db->query('DROP TABLE ' . $prefix . 'bilivido');
            $this->widget('Widget_Notice')->set("弹幕池已被删除!您可以卸载本插件了!如果您是误操作,将本插件禁用再重启一次即可恢复正常!", 'notice');
            $this->response->goBack();
        } else {
            $id = $this->request->id;
            if ($id != null) {
                self::getDanmakuList($id);
            } else {
                self::sendDanmaku(self::bvGetPost('token'), self::bvGetPost('author'), self::bvGetPost('color'), self::bvGetPost('id'), self::bvGetPost('text'), self::bvGetPost('time'), self::bvGetPost('type'));
            }
        }
    }

    /** 获取本站弹幕池内容 */
    public function getDanmakuList($bv)
    {

        //获取本站主弹幕列表、禁用接口缓存(防止弹幕更新不及时)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('content-type:application/json;charset=utf-8');
        header ('Expires: Mon, 26 Jul 1970 05:00:00 GMT');
        header ('Cache-Control: no-cache, must-revalidate');
        header ('Pragma: no-cache');
        $this->init();
        $db = Typecho_Db::get();
        $query = $db->select()->from('table.bilivido')->where('bv = ?', $bv);
        $result = $db->fetchAll($query);

        //处理弹幕json
        $danmaku = [[0, 0, "#fff", "eeeeee", " "]];
        foreach ($result as $row) {
            $danmaku[] = [
                (float)$row['time'],
                $row['type'],
                $row['color'],
                $row['author'],
                $row['text']
            ];
        }
        echo json_encode([
            "code" => 0,
            "version" => 3,
            "data" => $danmaku,
            "msg" => '弹幕装填完成'
        ], JSON_UNESCAPED_UNICODE);
    }

    /** 向弹幕池发送弹幕 */
    public function sendDanmaku($token, $author, $color, $id, $text, $time, $type)
    {
        $this->init();
        $db = Typecho_Db::get();
        if ($token == md5($id . "biliVido")) {
            if ($author != null && $color != null && $id != null && $time !== null && $type !== null) {
                if (mb_strlen($text) <= 20) {

                    //添加弹幕到弹幕池
                    $userLoginedId = $this->_user->uid;
                    $insert = $db->insert('table.bilivido')->rows(['uid' => NULL, 'user' => $userLoginedId, 'bv' => $id, 'text' => $text, 'color' => $color, 'type' => $type, 'time' => $time, 'author' => $author]);
                    $insertId = $db->query($insert);

                    if ($insertId !== false && $insertId != null) {
                        echo json_encode([
                            'code' => 0,
                            'msg' => '成功'
                        ], JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode([
                            'code' => 504,
                            'msg' => '数据库出现错误'
                        ], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    echo json_encode([
                        'code' => 503,
                        'msg' => '弹幕不能超过20字'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                echo json_encode([
                    'code' => 502,
                    'msg' => '必须参数为空'
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                'code' => 501,
                'msg' => '错误的token'
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
