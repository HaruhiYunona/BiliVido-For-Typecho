# 欢迎使用BiliVido插件!(现已更新1.1.3版本)
![](https://mdzz.pro/usr/uploads/2022/06/4054526885.png)
> BiliVido插件是为Typecho开发专门用于Bilibili视频解析的插件,可以解决bilibili自有分享视频的`<iframe>`组件,播放不清晰,无法调整和自适应大小的问题，可以完美融入文章之中。目前更新了批量转换以及弹幕解析功能（尚在测试）
>
> 该插件内部引用了 Bilibili解析API(https://github.com/injahow/bilibili-parse)和Dplayer等开源库。

> 作者博客:[https://mdzz.pro](https://mdzz.pro)
>
> 作者Github:[https://github.com/HaruhiYunona](https://github.com/HaruhiYunona)

这个插件我就单刀直入了！对比图奉上!

> Bilibili自有分享组件画面

<img src="https://tva2.sinaimg.cn/large/0088jPZqly1gyisqh5vaoj30rs0nralv.jpg" alt="Bilibili样式"  />



> BiliVido插件解析后的组件画面

![](https://tva2.sinaimg.cn/large/0088jPZqly1gyisnxp0i6j30sp0ny7if.jpg)

可以看出清晰度差距那是一个天上一个地下。这是因为B站限制了站外分享视频只有320P,而BiliVido采用直接解析B站视频接口的方法来播放，清晰度高达1080P!和站内播放是一致的! **唯一可惜的是,由于B站对动漫资源做了保护,该插件暂时只支持分析B站UP上传的视频。**



## 一、安装方法

1.将本插件下载下来。检查文件夹文件是否完整(请注意文件夹层次,有的解压软件可能会在插件文件夹外再新建一个同名文件夹,这样是读取不到插件的)

2.插件文件夹改名为  **`BiliVido`**  。最好是这样,这次更新已经自动适应了文件夹位置。

3.将插件文件夹上传到 **`网站根目录/usr/Plugins/`** 文件夹内，刷新Typecho后台,进入插件管理即可看到BiliVido。启用后进行配置即可。



## 二、配置细节

1.由于本插件依赖于jQuery,所以请您自行确认网站头部是否引入jQuery。如果没有引入也没有关系,本插件有一键配置面板,可以直接打开jQuery。

![jquery支持](https://tva2.sinaimg.cn/large/0088jPZqly1gyeagtfhlgj30rk03sabw.jpg)

2.根据需要可以调整画面清晰度。不过一般不需要调整，毕竟不耗你服务器的性能，吃的是叔叔家服务器的性能和流量，1080P高清走起多好,难道还想给叔叔省钱吗?

3.默认音量在  0-1之间的小数,例如  0.2 ，0.75 这样的。超过范围可能会出错。

4.弹幕功能尚在测试,不一定稳定。如果遇到问题可以在控制台插件设置里整体关闭。



## 三、使用方法

##### 1.最快捷的使用方法是直接将`<a>`链接描述修改为  **`#BV#`**

①纂写文章的时候插入一条B站视频的链接，任何位置都行。

<img src="https://tva2.sinaimg.cn/large/0088jPZqly1gyitqpn7fuj30uv0mbgp0.jpg" style="zoom:67%;" />

![](https://tva2.sinaimg.cn/large/0088jPZqly1gyitr1dds4j30ub05vwfp.jpg)





②修改链接描述为  **`#BV#`**  ,就配置好了!

![](https://tva2.sinaimg.cn/large/0088jPZqly1gyitrdsf7nj30u805375a.jpg)





③发布以后效果

<img src="https://tva2.sinaimg.cn/large/0088jPZqly1gyitt0i62jj30sp0kigv3.jpg" style="zoom:67%;" />



##### 2.详细配置方法

本插件支持单个定义播放器控件的一些数据。后续版本可能还会有所更新。

依据本格式编辑详细参数即可。每个参数数值前需要有英文冒号 :       数值结束后需要有英文分号 ;

虽然数值填错或者属性填错不影响播放器运行，但会导致您自定义的参数不会反映到播放器上。

![](https://tva2.sinaimg.cn/large/0088jPZqly1gyitsl7f6nj30uj05q0u7.jpg)



以下是可以自定义属性的表格。

| 属性名   | 允许值            | 作用     |
| :------- | ----------------- | -------- |
| quality  | 1080,720,480,360  | 调整画质 |
| autoplay | true,false        | 自动播放 |
| round    | true,false        | 自动循环 |
| hotkey   | true,false        | 热键开关 |
| volume   | 0~1 (小数,浮点数) | 默认音量 |
| danmaku  | true，false       | 弹幕功能 |

