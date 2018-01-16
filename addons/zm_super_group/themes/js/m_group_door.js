mui('.mui-scroll-wrapper').scroll({ 
	deceleration: 0.0003,
	 indicators: false, 
});
enterGroup();

var oAttention = document.getElementById('attention-btn');
var oStatus = '';
if(oAttention){
	var nowPage = document.querySelector('#now-page').value;
	oAttention.addEventListener('tap',function(){
		var This = this;
		mui.post(nowPage,{
						doing:'attention',
						by_userid:This.getAttribute('data-by-userid'),
						change:This.getAttribute('data-status')
					},function(data){
						if(data == 1){
							if(This.getAttribute('data-status') == 1){
								This.setAttribute('data-status',0);
								This.innerHTML = '取消关注';
							}else{
								This.setAttribute('data-status',1); 
								This.innerHTML = '+ 关注'; 
							}
						}
					},'json' 
				);
	});   
} 


/**
 * 进入群聊操作
 */
function enterGroup(){
	var oEnterBtn = document.querySelector('#enter-group');
	var nowPage = document.querySelector('#now-page').value;
	if(oEnterBtn){
		oEnterBtn.addEventListener('tap',function(){
			var This = this;
			this.disabled = 'disabled'; 
			//E无需验证 
			if(this.getAttribute('data-type') == '2'){
				var xhr = new XMLHttpRequest();
				xhr.open('post',nowPage,true);
				xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
				xhr.send('doing=enterNoCheck&groupid='+this.getAttribute('data-groupid'));  
				xhr.onreadystatechange = function(){
					if(xhr.readyState == 4){
						var d = xhr.responseText;
						if(d == 0) mui.alert('进入群聊失败！','提示','确定',function(){
							This.disabled = '';
						});
						if(d == 2) mui.alert('您已经在群聊中了！','提示','确定',function(){
							This.disabled = '';
						});
						if(d == 1) mui.alert('进入群聊成功','提示','确定',function(){
							window.location.href = This.parentNode.getAttribute('data-succ-location'); 
						}); 
					}
				}
			}
			//E无需验证
			
			
			//付费入群
			if(this.getAttribute('data-type') == '3'){
				mui.post(nowPage,{
					doing:'getWxPayParam',
					groupId:this.getAttribute('data-groupid')
				},function(data){
					wxPay(data,This); 
				},'json'
				);
				
				function wxPay(data,This){
					WeixinJSBridge.invoke(
						       'getBrandWCPayRequest', {
						           "appId":data.appId,     //公众号名称，由商户传入     
						           "timeStamp":data.timeStamp,         //时间戳，自1970年以来的秒数     
						           "nonceStr":data.nonceStr, //随机串     
						           "package":data.package,     
						           "signType":"MD5",         //微信签名方式：     
						           "paySign":data.paySign //微信签名 
						       },
						       function(res){     
						           if(res.err_msg == "get_brand_wcpay_request:ok" ) {
						        	   mui.post(nowPage,{
											doing:'paySuccDoing',
											groupid:This.getAttribute('data-groupid'),
											orderid:data.orderid 
										},function(data){
											if(data == 1) mui.alert('进入群聊成功','提示','确定',function(){
												window.location.href = This.parentNode.getAttribute('data-succ-location'); 
											});  
										},'json'
										);
						           }else{
						        	   mui.alert('支付失败！'); 
						           }  
						       }
						   );
				}
			}
			//付费入群		
		});
	}
}



/**
 * 生成推广海报
 */
if(mui('#create-poster')[0]){
	mui('#create-poster')[0].addEventListener('tap',function(){
		window.location.href = this.getAttribute('data-url'); 
	}); 
}

   






















