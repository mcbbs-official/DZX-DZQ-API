# DZX-DZQ-API
一个能将DZX数据转为DZQ API格式的插件

# 这是什么插件？
这是一个DiscuzX的插件，能够将DiscuzX的内容输出到DiscuzQ中。

# 为什么需要这个插件？
现在很多DiscuzX的站长都在观望DiscuzQ，担心升级到DiscuzQ后会有各种功能上的缺失，以及需要一定的时间进行磨合。使用该插件后，就可以帖子、分类（版块）等内容依然使用DiscuzX，但是前端以及DiscuzQ特有的功能依然可以保留，这样未来也可以做到无缝升级。也就是说，使用该插件后，可以使DZX和DZQ的站点同时运营，也不用担心内容不一致的问题*。

# 这个插件可以替代DZQ的核心功能吗？
不能，该插件仅仅作为升级DZQ的过渡使用。毕竟DZQ和DZX的底层核心还是有很大区别的。如果你是希望升级到DZQ，并且拥有二次开发的能力，你可以使用该插件，临时过渡DZQ部分的开发，实现敏捷开发。

# 支持版本
Discuz! X: 3.4、3.5

Discuz! Q: 3.0.21111

负载均衡：Nginx

推荐PHP版本：7.3

# 安装
将git文件丢到dzx的source/plugin/zhaisoul_dzq_api下（必须是这个目录！不要老想着自己改名字，弄个大新闻）

进入dzx的管理面板安装该插件并配置JWT公钥私钥（可从DZQ的storage/cert目录下获取，publickey为公钥，privatekey为私钥）

修改nginx配置，添加location

请享用！

# 配置
## nginx
在dzx的nginx配置中，加入如下location即可

```nginx
location /api/v3 {
  rewrite ^\/api\/v3\/(.+) /plugin.php?id=zhaisoul_dzq_api:api&module=$1&$args;
}
```
