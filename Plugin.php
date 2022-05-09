<?php

/**
 * 强大好用的B站视频解析播放器
 * 
 * @package BiliVido
 * @author HaruhiYunona
 * @version 1.0.0
 * @link https://mdzz.pro
 */
class BiliVido_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
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
     * 
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
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        //说明书
        echo "<h2>BiliVido插件说明书:</h2><br>";
        echo "1.本插件启用以后，可以自动将带有指定链接描述的B站视频(因为版权保护原因,不包含番剧)&lt;a&gt;标签转换为视频。每页仅可以有一个这样的标签，多了会出现未知bug。<br>";
        echo "2.该插件默认启动方式:将链接描述写入<font color=\"red\">#BV#</font>即可。例如:<br>";
        echo "<center><h4>[#BV#][1]<br><br>[1]: https://www.bilibili.com/video/BV1Na411r7tN</h4></center><br>";
        echo "上述写法引用的是默认配置。在#BV#后方可以写详细的单个播放器的配置,例如:<br>";
        echo "<center><h4>[#BV# quality:1080;autoplay:false;][1]</h4></center><br>";
        echo "就像在写CSS一样非常方便。一定要注意这个也和代码一样要用英文分号 ; 结尾,不然会报错的<br>";
        echo "详细属性清单请见我的:<br>Github:<a href=\"https://github.com/HaruhiYunona/BiliVido-For-Typecho\">https://github.com/HaruhiYunona/BiliVido-For-Typecho</a><br>Blog:<a href=\"https://mdzz.pro/2022/01/19/72.html\">https://mdzz.pro/2022/01/19/72.html</a><br><br>";
        echo "<br><br><h2>默认配置:</h2><br>";
        //jQuery支持
        $jqsupport = new Typecho_Widget_Helper_Form_Element_Radio(
            'jqsupport',
            array(
                'on' => _t('开启jQuery支持'),
                'off' => _t('关闭jQuery支持'),
            ),
            'off',
            _t('jQuery支持(插件运行需要)'),
            _t('请确认您已经在模板的header.php文件手动插入了jQurey，否则请您打开jQurey支持。插件自带的jQuery版本为3.6.0。')
        );
        $form->addInput($jqsupport);
        //画质
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
        $vidvolume = new Typecho_Widget_Helper_Form_Element_Text('volume', null, '1', _t('默认音量'), '默认音量,0-1之间');
        $form->addInput($vidvolume);
        //自动播放视频
        $autoplay = new Typecho_Widget_Helper_Form_Element_Radio(
            'autoplay',
            array(
                'on' => _t('开启'),
                'off' => _t('关闭'),
            ),
            'on',
            _t('自动播放'),
            _t('允许视频自动播放。该功能可能在移动端浏览器无效。')
        );
        $form->addInput($autoplay);
        //视频循环
        $vidround = new Typecho_Widget_Helper_Form_Element_Radio(
            'vidround',
            array(
                'on' => _t('开启'),
                'off' => _t('关闭'),
            ),
            'on',
            _t('循环播放'),
            _t('允许视频循环播放。')
        );
        $form->addInput($vidround);
        //默认主题色
        $vidtheme = new Typecho_Widget_Helper_Form_Element_Text('theme', null, '#FFB6C1', _t('主题色'), '主题色。请输入例如#FFFFFF这样的16进制颜色码。(虽然好像并无软用)');
        $form->addInput($vidtheme);
        //热键
        $vidhotkey = new Typecho_Widget_Helper_Form_Element_Radio(
            'hotkey',
            array(
                'on' => _t('开启'),
                'ff' => _t('关闭'),
            ),
            'on',
            _t('热键'),
            _t('播放器是否允许热键操控，包括音量，快进快退等。')
        );
        $form->addInput($vidhotkey);
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
    public static function header()
    {
        //jQuery支持
        $jqsup = trim(Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->jqsupport);
        if ($jqsup == 'on') {
            echo '<script src='  . Helper::options()->pluginUrl . '/BiliVido/static/jquery.min.js"></script>';
        }
        //dplayer全家桶
        echo '<meta name="referrer" content="no-referrer"/>';
        echo '<script src="'  . Helper::options()->pluginUrl . '/BiliVido/static/hls.min.js"></script>';
        echo '<script src="'  . Helper::options()->pluginUrl . '/BiliVido/static/Dplayer.min.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="'  . Helper::options()->pluginUrl . '/BiliVido/static/Dplayer.min.css">';
    }


    public static function footer()
    {
        //获取插件配置信息
        $vidquality = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->quality;
        $vidautoplay = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->autoplay;
        $vidround = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->vidround;
        $vidhotkey = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->hotkey;
        $vidvolume = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->volume;
        $vidtheme = Typecho_Widget::widget('Widget_Options')->Plugin('BiliVido')->theme;
        $anaPath = Helper::options()->pluginUrl . '/BiliVido/bp/index.php';
        //输出插件工作内容
        echo '<script src="'  . Helper::options()->pluginUrl . '/BiliVido/biliVido.js" " type="text/javascript" charset="utf-8"></script>';
        echo <<<EOF

<script>
analysisLink('$anaPath','$vidquality', '$vidautoplay', '$vidround','$vidhotkey','$vidvolume','$vidtheme');
</script>  

EOF;
    }
}
?>
