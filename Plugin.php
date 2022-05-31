<?php

/**
 * 强大好用的B站视频解析播放器
 * 
 * @package BiliVido
 * @author HaruhiYunona
 * @version 1.1.0
 * @link https://mdzz.pro
 */

class BiliVido_Plugin implements Typecho_Plugin_Interface
{


    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = array('BiliVido_Plugin', 'footer');
        Typecho_Plugin::factory('Widget_Archive')->header = array('BiliVido_Plugin', 'header');
        return '启用成功!请前往管理面板设置,否则该插件将以默认配置运行';
    }


    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        return '禁用成功!插件已经停用。遇到问题了?去作者博客 https://mdzz.pro 看看吧!';
    }


    /**
     * 插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {

        /**
         * 插件更新检测
         * @param string $name 插件名
         * @param string $version 插件版本
         */
        function mdzzUpdater($name, $version)
        {
            echo "<style>.paul-info{ margin:1em 0;} .paul-info > *{margin:0 0 1rem} .buttons a{background:#467b96; color:#fff; border-radius:4px; padding:.5em .75em; display:inline-block}</style>";
            echo "<div class='paul-info'>";
            echo "<center><h2>BiliVido 视频解析 (版本ID:" . $version . ")</h2></center>";
            echo "<center><p>By: <a href='https://github.com/HaruhiYunona'>HaruhiYunona</a></p></center>";
            $curlOption = curl_init();
            curl_setopt($curlOption, CURLOPT_URL, 'https://bottle.mdzz.pro/test.php?name=' . $name);
            curl_setopt($curlOption, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlOption, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curlOption, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlOption, CURLOPT_CONNECTTIMEOUT, 10);
            $versionInfo = json_decode(curl_exec($curlOption), true);
            if ($versionInfo['code'] == 0 && $versionInfo['msg']['version'] > $version) {
                echo "<h3>新版本</h3>";
                echo $versionInfo['msg']['log'];
                echo "<center><p class='buttons'><a href='https://github.com/HaruhiYunona/BiliVido-For-Typecho'>下载新版</a></p></center>";
            } else {
                echo "<center><h4>您正在使用的是最新版本哦~</h4></center>";
            }
        }
        mdzzUpdater("biliVido", 2);

        /**
         * 插件说明书
         */
        echo "<h2>BiliVido插件说明书:</h2>";
        echo "1.本插件启用以后,可以自动将带有指定链接描述的B站视频(因为版权保护原因,不包含番剧)&lt;a&gt;标签转换为视频。支持多个视频同时转化。<br>";
        echo "2.该插件默认启动方式:将链接描述写入<font color=\"red\">#BV#</font>即可。例如:<br>";
        echo "<center><h4>[#BV#][1]<br><br>[1]: https://www.bilibili.com/video/BV1Na411r7tN</h4></center><br>";
        echo "上述写法引用的是默认配置。在#BV#后方可以写详细的单个播放器的配置,例如:<br>";
        echo "<center><h4>[#BV# quality:1080;autoplay:false;][1]</h4></center><br>";
        echo "就像在写CSS一样非常方便。一定要注意这个也和代码一样要用英文分号 ; 结尾,不然会报错的<br>";
        echo "详细属性清单请见我的:<br>Github:<a href=\"https://github.com/HaruhiYunona/BiliVido-For-Typecho\">https://github.com/HaruhiYunona/BiliVido-For-Typecho</a><br>Blog:<a href=\"https://mdzz.pro/2022/01/19/72.html\">https://mdzz.pro/2022/01/19/72.html</a><br><br>";
        echo "<br><h2>默认配置:</h2><br>";


        /**
         * 插件配置表盘
         */

        //jQuery支持
        $httpsSupport = new Typecho_Widget_Helper_Form_Element_Radio(
            'https',
            array(
                'on' => _t('开启HTTPS'),
                'off' => _t('关闭HTTPS'),
            ),
            'on',
            _t('HTTPS同步'),
            _t('如果您的网站是HTTPS访问,请打开它;如果您的网站是HTTP访问,请关闭它')
        );
        $form->addInput($httpsSupport);

        //jQuery支持
        $jqSupport = new Typecho_Widget_Helper_Form_Element_Radio(
            'jqsupport',
            array(
                'on' => _t('开启jQuery支持'),
                'off' => _t('关闭jQuery支持'),
            ),
            'on',
            _t('jQuery支持(插件运行需要)'),
            _t('请确认您已经在模板的header.php文件手动插入了jQurey,否则请您打开jQurey支持。插件自带的jQuery版本为3.6.0。')
        );
        $form->addInput($jqSupport);

        //Dplayer播放器
        $dpSupport = new Typecho_Widget_Helper_Form_Element_Radio(
            'dpsupport',
            array(
                'on' => _t('开启Dplayer支持'),
                'off' => _t('关闭Dplayer支持'),
            ),
            'on',
            _t('Dplayer播放器支持'),
            _t('请确认您已有默认安装的Dplayer播放器,否则请打开该开关')
        );
        $form->addInput($dpSupport);

        //HLS解码器
        $hlsSupport = new Typecho_Widget_Helper_Form_Element_Radio(
            'hlssupport',
            array(
                'on' => _t('开启HLS解码器支持'),
                'off' => _t('关闭HLS解码器支持'),
            ),
            'on',
            _t('HLS解码器支持(插件运行需要)'),
            _t('请确认您已有默认的HLS解码器,否则请打开该开关')
        );
        $form->addInput($hlsSupport);

        //默认画质
        $VidQuality = new Typecho_Widget_Helper_Form_Element_Radio(
            'quality',
            array(
                '1080' => _t('1080P'),
                '720' => _t('720P'),
                '480' => _t('480P'),
                '360' => _t('360P'),
            ),
            '1080',
            _t('默认解析画质'),
            _t('默认的解析画质。越高越清晰')
        );
        $form->addInput($VidQuality);

        //默认音量
        $vidVolume = new Typecho_Widget_Helper_Form_Element_Text('volume', null, '1', _t('默认音量'), '默认音量,0-1之间');
        $form->addInput($vidVolume);

        //默认视频循环
        $vidRound = new Typecho_Widget_Helper_Form_Element_Radio(
            'round',
            array(
                'on' => _t('开启'),
                'off' => _t('关闭'),
            ),
            'on',
            _t('循环播放'),
            _t('允许视频循环播放。')
        );
        $form->addInput($vidRound);

        //热键
        $vidHotkey = new Typecho_Widget_Helper_Form_Element_Radio(
            'hotkey',
            array(
                'on' => _t('开启'),
                'ff' => _t('关闭'),
            ),
            'on',
            _t('热键'),
            _t('播放器是否允许热键操控，包括音量，快进快退等。')
        );
        $form->addInput($vidHotkey);

        //解析弹幕
        $vidDanmaku = new Typecho_Widget_Helper_Form_Element_Radio(
            'danmaku',
            array(
                'on' => _t('开启弹幕'),
                'off' => _t('关闭弹幕'),
            ),
            'on',
            _t('开启视频弹幕'),
            _t('自动解析视频弹幕功能!')
        );
        $form->addInput($vidDanmaku);
    }

    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
    }

    //页面头部所添加的内容
    public static function header()
    {
        //获取本插件目录
        $vidRoot = Helper::options()->pluginUrl . "/" . basename(dirname(__FILE__));

        //添加no-referrer标记防止控制台显示跨域错误
        echo '<meta name="referrer" content="no-referrer"/>';

        //jQuery支持
        $jqSuport = trim(Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->jqsupport);
        if ($jqSuport == 'on') {
            echo '<script src='  . $vidRoot . '/static/jquery.min.js></script>';
        }

        //dPlayer全家桶
        $dpSupport = trim(Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->dpsupport);
        if ($dpSupport == 'on') {
            echo '<script src="'  . $vidRoot . '/static/DPlayer.min.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="'  . $vidRoot . '/static/DPlayer.min.css">';
        }

        //HLSj解码器
        $hlsSupport = trim(Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->hlssupport);
        if ($hlsSupport == 'on') {
            echo '<script src="'  . $vidRoot . '/static/hls.min.js"></script>';
        }

        //加载插件JS
        echo '<script src="'  . $vidRoot . '/biliVido.js" " type="text/javascript" charset="utf-8"></script>';
    }

    //页面页脚添加的内容
    public static function footer()
    {

        //获取本插件目录
        $vidRoot = Helper::options()->pluginUrl . "/" . basename(dirname(__FILE__));

        //获取插件配置信息
        $vidQuality = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->quality;
        $vidRound = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->round;
        $vidHotkey = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->hotkey;
        $vidVolume = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->volume;
        $vidDanmaku = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->danmaku;
        $vidHttps = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->https;
        $anaPath = $vidRoot . '/bp';
        //输出插件工作内容
        echo <<<EOF

             <script>
             $(document).ready(function(){
                biliAnaLink('$anaPath','$vidQuality', '$vidRound','$vidHotkey','$vidVolume','$vidDanmaku','$vidHttps');
             });
             </script>  

EOF;
    }
}
