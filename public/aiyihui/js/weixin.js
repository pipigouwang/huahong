var AJAXURL = "http://aiyihui.yongweisoft.cn",
	IMGURL = "http://aiyihui.yongweisoft.cn";
//引入JS文件
var new_element = document.createElement("script");
new_element.setAttribute("type", "text/javascript");
new_element.setAttribute("src", "http://res.wx.qq.com/open/js/jweixin-1.2.0.js");
document.body.appendChild(new_element);
//end引入js文件

function geography(url) {
	var da = {
		url: url
	}
	$.ajax({
		type: 'POST',
		url: AJAXURL + '/index/index/geography',
		contentType: "application/x-www-form-urlencoded",
		dataType: 'json',
		data: da,
		success: function(success) {
			console.log('数据' + JSON.stringify(success))
			if(success.err > 0) {
				var configData = success.data.data;
				wx.config({
					debug: false,
					appId: configData.appId,
					timestamp: configData.timestamp,
					nonceStr: configData.nonceStr,
					signature: configData.signature,
					jsApiList: ['openLocation', 'getLocation']
				});
				wx.ready(function() {
					wx.getLocation({
						type: 'wgs84',
						success: function(res) {
							var latitude = res.latitude;
							console.log(latitude)
							var longitude = res.longitude;
							console.log(longitude)
							$('body').attr('data-lat', latitude);
							$('body').attr('data-lng', longitude);
						}
					});
				});
				wx.error(function(res) {
					console.log(res)
				});
			} else {
				$.toast(success.data, "cancel");
			}
		}
	});
}
//微信配置
function wxShareConfig(appId, timestamp, nonceStr, signature) {
	wx.config({
		debug: false,
		appId: appId,
		timestamp: timestamp,
		nonceStr: nonceStr,
		signature: signature,
		jsApiList: [
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ',
			'onMenuShareQZone'
		]
	});
	
	wx.error(function (res) {
		alert(res.errMsg);
	});
}
