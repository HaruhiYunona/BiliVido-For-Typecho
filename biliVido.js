/**
 **BiliVido.js
 **@pakage 
 **@authors HaruhiYunona(lashanda13fg@gmail.com)
 **@version 1.0.0
 **@blog https://mdzz.pro
 */
function analysisLink(anaPath, oquality, oauto, oround, ohotkey, ovolume, otheme) {
    //版权标识符!请不要删除,非常感谢您对版权的尊重!
    if (window.console && window.console.log) {
        console.log("%c BiliVido %c https://mdzz.pro ", "color: #fff; margin: 1em 0; padding: 5px 0; background: #F08080;", "margin: 1em 0; padding: 5px 0; background: #EFEFEF;");
    }
    $('body').find('a').each(function() {
        var videoObj = $(this);
        var config = $(this).html();
        var link = $(this).attr('href');
        //解析视频区域配置
        if (config.indexOf('#BV#') != -1) {
            var config = config.replace(/\#BV\#+/g, "");
            var config = config.replace(/\s+/g, "");
            var quality = config.match(/(?<=quality:).+?(?=;)/g);
            var auto = config.match(/(?<=autoplay:).+?(?=;)/g);
            var round = config.match(/(?<=round:).+?(?=;)/g);
            var hotkey = config.match(/(?<=hotkey:).+?(?=;)/g);
            var volume = config.match(/(?<=volume:).+?(?=;)/g);
            var theme = config.match(/(?<=theme:).+?(?=;)/g);
            //判断用默认配置还是用户自定义配置:
            var quality = (quality == null || quality == undefined) ? oquality : quality[0];
            var auto = (auto == null || auto == undefined) ? oauto : auto[0];
            var round = (round == null || round == undefined) ? oround : round[0];
            var hotkey = (hotkey == null || hotkey == undefined) ? ohotkey : hotkey[0];
            var volume = (volume == null || volume == undefined) ? ovolume : volume[0];
            var theme = (theme == null || theme == undefined) ? otheme : theme[0];
            //转换基本设置
            switch (quality) {
                case ('1080'):
                    var quality = 80;
                    break;
                case ('720'):
                    var quality = 64;
                    break;
                case ('480'):
                    var quality = 32;
                    break;
                case ('320'):
                    var quality = 16;
                    break;
                default:
                    var quality = 80;
            }
            var autoplay = (auto == 'on' || auto == true) ? true : false;
            var round = (round == 'on' || round == true) ? true : false;
            var hotkey = (hotkey == 'on' || hotkey == true) ? true : false;
            //解析b站链接配置
            var link = link + '&';
            if (link.indexOf('/video/') != -1) {
                var bvcode = link.match(/(?<=\/video\/).+?(?=[\?&])/g);
                var bvpage = link.match(/(?<=p\=).+?(?=[\?&])/g);
                var bvpage = (bvpage == null || bvpage == undefined) ? '1' : bvpage[0];
                var require = {
                    bv: bvcode[0],
                    p: bvpage,
                    q: quality,
                    type: 'video',
                    format: 'mp4',
                    otype: 'json'
                }
            } else {
                videoObj.after('<p>【BiliVido:哎呀,暂不支持这类视频解析!对不起嘛~】</p><a href="' + link.trim('\&') + '">→点击可以跳转查看视频哦←</a>');
                videoObj.remove();
            }
            //向API发送请求
            if (require != null && require != undefined) {
                $.post(anaPath, require, function(data) {
                    if (data != '') {
                        var data = JSON.stringify(data);
                        var obj = JSON.parse(data);
                        if (obj.code == 0) {
                            videoObj.after('<div id="dplayer" class="dplayer"></div>');
                            videoObj.remove();
                            var url = obj.url.replace('http://', 'https://');
                            //添加播放器
                            new DPlayer({
                                element: document.getElementById('dplayer'),
                                autoplay: autoplay,
                                theme: theme,
                                loop: round,
                                lang: 'zh-cn',
                                hotkey: hotkey,
                                volume: volume,
                                playbackSpeed: '[0.5,0.75,1,1.25,1.5,2]',
                                preload: 'auto',
                                video: {
                                    url: url,
                                }
                            });
                        } else {
                            videoObj.after('<p>【BiliVido:哎呀,这个视频解析失败了呢!】</p><a href="' + link.trim('\&') + '">→点击可以跳转查看视频哦←</a>');
                            videoObj.remove();
                        }
                    } else {
                        videoObj.after('<p>【BiliVido:哎呀,这个视频解析失败了呢!】</p><a href="' + link.trim('\&') + '">→点击可以跳转查看视频哦←</a>');
                        videoObj.remove();
                    }
                });
            }

        }
    });
}
