/**
 **BiliVido.js
 **@pakage 
 **@authors HaruhiYunona(lashanda13fg@gmail.com)
 **@version 1.1.0
 **@blog https://mdzz.pro
 */

function biliAnaLink(anaPath, obili_quality, obili_round, obili_hotkey, obili_volume, obili_danmaku, bili_https) {

    //版权标识符!请不要删除,非常感谢您对版权的尊重!
    if (window.console && window.console.log) {
        console.log("%c BiliVido %c https://mdzz.pro ", "color: #fff; margin: 1em 0; padding: 5px 0; background: #F08080;", "margin: 1em 0; padding: 5px 0; background_round: #EFEFEF;");
    }

    //搜寻页面中可转换区域
    $(document).find('a').each(function() {

        //标记元素
        var bili_videoObj = $(this);
        var bili_config = $(this).html();
        var bili_link = $(this).attr('href');

        //解析视频区域配置
        if (bili_config.indexOf('#BV#') != -1) {
            var bili_config = bili_config.replace(/\#BV\#{1}\s*/g, "");
            var bili_quality = bili_config.match(/(?<=quality:).+?(?=;)/g);
            var bili_round = bili_config.match(/(?<=round:).+?(?=;)/g);
            var bili_hotkey = bili_config.match(/(?<=hotkey:).+?(?=;)/g);
            var bili_volume = bili_config.match(/(?<=volume:).+?(?=;)/g);
            var bili_danmaku = bili_config.match(/(?<=danmaku:).+?(?=;)/g);

            //判断用默认配置还是用户自定义配置:
            var bili_quality = (bili_quality == null || bili_quality == undefined) ? obili_quality : bili_quality[0];
            var bili_round = (bili_round == null || bili_round == undefined) ? obili_round : bili_round[0];
            var bili_hotkey = (bili_hotkey == null || bili_hotkey == undefined) ? obili_hotkey : bili_hotkey[0];
            var bili_volume = (bili_volume == null || bili_volume == undefined) ? obili_volume : bili_volume[0];
            var bili_danmaku = (bili_danmaku == null || bili_danmaku == undefined) ? obili_danmaku : bili_danmaku[0];

            //转换基本设置
            switch (bili_quality) {
                case ('1080'):
                    var bili_quality = 80;
                    break;
                case ('720'):
                    var bili_quality = 64;
                    break;
                case ('480'):
                    var bili_quality = 32;
                    break;
                case ('360'):
                    var bili_quality = 16;
                    break;
                default:
                    var bili_quality = 80;
            }
            var bili_round = (bili_round == 'on' || bili_round == true) ? true : false;
            var bili_hotkey = (bili_hotkey == 'on' || bili_hotkey == true) ? true : false;
            var bili_danmaku = (bili_danmaku == 'on' || bili_danmaku == true) ? true : false;

            //解析b站链接配置
            var bili_link = bili_link.trim() + '&';
            if (/BV{1}[a-zA-Z0-9]{10}/g.test(bili_link) == true) {
                var bvcode = bili_link.match(/BV{1}[a-zA-Z0-9]{10}/g);
                var bvpage = bili_link.match(/(?<=p\=).+?(?=[\?&])/g);
                var bvpage = (bvpage == null || bvpage == undefined) ? '1' : bvpage[0];
                var bili_require = {
                    bv: bvcode[0],
                    p: bvpage,
                    q: bili_quality,
                    type: 'video',
                    format: 'mp4',
                    otype: 'json'
                }

                //发送解析请求
                if (bili_require != null && bili_require != undefined) {
                    $.post(anaPath + '/index.php', bili_require, function(bili_data) {
                        if (bili_data != '') {
                            var bili_data = JSON.stringify(bili_data);
                            var bili_obj = JSON.parse(bili_data);
                            if (bili_obj.code == 0) {
                                bili_videoObj.after('<div id="dplayer_' + bvcode[0] + '" class="dplayer"></div>');
                                bili_videoObj.remove();
                                var bili_url = bili_obj.url;
                                if (bili_https == 'on') {
                                    var bili_url = bili_url.replace('http://', 'https://');
                                }

                                //添加播放器
                                if (bili_danmaku == true) {
                                    new DPlayer({
                                        element: document.getElementById('dplayer_' + bvcode[0]),
                                        loop: bili_round,
                                        lang: 'zh-cn',
                                        hotkey: bili_hotkey,
                                        volume: bili_volume,
                                        playbackSpeed: '[0.5,0.75,1,1.25,1.5,2]',
                                        preload: 'auto',
                                        video: {
                                            url: bili_url,
                                        },
                                        danmaku: {
                                            id: bvcode[0],
                                            api: anaPath + '/src/send.php',
                                            token: "212323213",
                                            addition: [anaPath + '/src/danmaku.php?bv=' + bvcode[0] + '&page=' + bvpage]
                                        }
                                    });
                                } else {
                                    new DPlayer({
                                        element: document.getElementById('dplayer_' + bvcode[0]),
                                        loop: bili_round,
                                        lang: 'zh-cn',
                                        hotkey: bili_hotkey,
                                        volume: bili_volume,
                                        playbackSpeed: '[0.5,0.75,1,1.25,1.5,2]',
                                        preload: 'auto',
                                        video: {
                                            url: bili_url,
                                        }
                                    });
                                }

                            } else {
                                bili_videoObj.after('<p>【BiliVido:哎呀,这个视频解析失败了呢!】</p><a href="' + bili_link.trim('\&') + '">→点击可以跳转查看视频哦←</a>');
                                bili_videoObj.remove();
                            }

                        } else {
                            bili_videoObj.after('<p>【BiliVido:哎呀,这个视频解析失败了呢!】</p><a href="' + bili_link.trim('\&') + '">→点击可以跳转查看视频哦←</a>');
                            bili_videoObj.remove();
                        }
                    });
                }

            } else {
                bili_videoObj.after('<p>【BiliVido:哎呀,暂不支持这类视频解析!对不起嘛~】</p><a href="' + bili_link.trim('\&') + '">→点击可以跳转查看视频哦←</a>');
                bili_videoObj.remove();
            }

        }

    });

}