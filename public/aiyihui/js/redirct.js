/**
 * Created by Administrator on 2018/8/29.
 */
//判断是否是微信浏览器的函数
window.onload = function isWeiXin(){
    //window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
    var ua = window.navigator.userAgent.toLowerCase();
    //通过正则表达式匹配ua中是否含有MicroMessenger字符串
    var pathname = window.location.pathname;
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
//获取当前窗口的路径
    }else if(ua.match(/MicroMessenger/i) != 'micromessenger'&&pathname == '/aiyihui/www/shopdetails.html'){

    }else{
        window.location.replace("http://aiyihui.yongweisoft.cn/aiyihui/www/attention.html");
        return false;
    }
}
window.onload();


