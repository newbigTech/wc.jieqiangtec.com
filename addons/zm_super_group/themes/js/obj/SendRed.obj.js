function SendRed(){
	this.box = null;   //设置红包box
	this.person = groupPerson;  //社群人数
	this.groupid = selToID;  //社群ID，云通信ID
	this.oFm = null;  //红包表单
	this.money = 0.00;  //红包总额,html显示
	this.sum = 0;  //红包总额，发送数据
	this.groupId = document.body.getAttribute('data-groupid');  //社群ID，数据ID
	this.title = '恭喜发财，大吉大利';  //红包标题
	this.orderid = '';  //支付成功后的订单ID
	this.redType = 1;   //红包类型，1=普通，2=手气
	this.redId = null;  //红包ID
	this.nowRed = null;  //当前点击的红包
	this.redBox = null;  //未领取红包元素
	this.redMask = null;  //未领取红包元素遮罩
	this.redList = null;  //抢红包列表
	this.redOwner = null;   //红包主人
	this.redTitle = null;   //当前打开红包的标题
	this.redOwnerHeader = null;  //红包主人的头像
	this.robMoney = null;   //当前抢到的总额
	this.robId = null;   //当前被抢红包的ID
	this.robDetails = null;  //被强红包的详情
}

//生成红包设置区域
SendRed.prototype.init = function(){
	if(this.box == null){
		this.box = document.createElement('div');
		this.box.id = 'set-red-box';
		this.box.innerHTML = this.getInnerHtml();
		this.setFocus();
		document.body.appendChild(this.box);
		this.clickCloseBtn();
		this.sendRed();
		this.sendCheck();
	}
}

//获取红包html结构
SendRed.prototype.getInnerHtml = function(){
	var html = `<form action="" method="post">
					<ul>
						<li>
							<span>单个金额</span>
							<input type="number" placeholder="0.00" name="unit_price" />
							<b>元</b>
						</li>
						<li></li>
						<li>
							<span>红包个数</span>
							<input type="tel" placeholder="个数" name="number" />
							<b>个</b>
						</li>
						<li>本群共${this.person}人</li>
						<li>
							<textarea name="title" placeholder="恭喜发财，大吉大利"></textarea>
						</li>
					</ul>
					<h2><span>￥</span><span>0.00</span></h2>
					<button type="button" class="send-red" disabled="disabled">塞进红包</button>
					<button type="button" class="send-cancel">取消</button>
				</form>`; 
	    return html;
}


//红包设置inptu焦点
SendRed.prototype.setFocus = function(){
	var aLi = this.box.querySelectorAll('li');
	for(var i=0;i<aLi.length;i++){
		aLi[i].addEventListener('tap',function(ev){
			ev.stopPropagation();
			if(this.querySelector('input')){
				this.querySelector('input').focus();
			}
			if(this.querySelector('textarea')){
				this.querySelector('textarea').focus();
			}
		});
	}
	
	this.box.addEventListener('tap',function(ev){
		for(var i=0;i<aLi.length;i++){
				if(aLi[i].querySelector('input')){
					aLi[i].querySelector('input').blur();
				}
				if(aLi[i].querySelector('textarea')){
					aLi[i].querySelector('textarea').blur();
				}
		}
	});
}


//关闭红包
SendRed.prototype.close = function(){
	document.body.removeChild(this.box);
}


//点击关闭按钮
SendRed.prototype.clickCloseBtn = function(){
	var This = this;
	this.box.querySelectorAll('button')[1].addEventListener('tap',function(){
		This.close();
	});
}

//点击发送红包
SendRed.prototype.sendRed = function(){
	var This = this;
	this.box.querySelectorAll('button')[0].addEventListener('tap',function(){
		mui.post(nowPage,{
				doing:'createRed',
				groupId:This.groupId,
				sum:This.sum
			},function(data){
				This.wxPay(data);
			},'json'
		);
	});
}

//微信支付
SendRed.prototype.wxPay = function(data){
	var This = this;
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
		        	   This.orderid = data.orderid;
		        	   This.redPaySuccess();
		           }else{
		        	   This.redPayError();
		           }  
		       } 
		   );
}

//红包支付成功执行函数
SendRed.prototype.redPaySuccess = function(){
	this.changePayState();
	this.sendMessage();
	this.close();
}

//红包支付失败执行函数
SendRed.prototype.redPayError = function(){
	 mui.toast('红包发送失败！',{duration:'short'});
}

//更改支付状态
SendRed.prototype.changePayState = function(){
	var This = this;
	mui.post(nowPage,{
			doing:'changePayState',
			orderId : This.orderid
		},function(data){
		},'json'
	);
}

//发送红包消息
SendRed.prototype.sendMessage = function(){
	var This = this;
	if(This.oFm.title.value != ''){
		This.title = This.oFm.title.value;
	}
	mui.post(nowPage,{
			doing:'sendRed',
			sum:This.sum,
			title:This.title,
			number:This.oFm.number.value,
			unit:This.oFm.unit_price.value,
			groupId:This.groupid,
			type:This.redType
		},function(data){
			
		},'text'
	);
}


//发送红包时验证数据,定时器验证
SendRed.prototype.sendCheck = function(){
	var This = this;
	this.oFm = this.box.querySelector('form'); 
	this.money = this.box.querySelector('h2').querySelectorAll('span')[1];
	this.oFm.unit_price.addEventListener('focus',function(){
		This.unitTimer(this);
	});
	this.oFm.unit_price.addEventListener('blur',function(){
		clearInterval(this.timer);
	});
	this.oFm.number.addEventListener('focus',function(){
		This.numberTimer(this);
	});
	this.oFm.number.addEventListener('blur',function(){
		clearInterval(this.timer);
	});
}

//单个红包验证定时器
SendRed.prototype.unitTimer = function(unit){
	var This = this;
	clearInterval(unit.timer);
	unit.timer = setInterval(function(){
		This.checkMoneyPerson();
	},100);
}

//红包个数验证定时器
SendRed.prototype.numberTimer = function(number){
	var This = this;
	clearInterval(number.timer);
	number.timer = setInterval(function(){
		This.checkMoneyPerson();
	},100);
}

//验证金钱
SendRed.prototype.checkMoney = function(money){
	if(/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(money)){
		return true;
	}else{
		return false;
	}
}

//自动补齐金钱小数点后的数
SendRed.prototype.autoFloat = function(value){
	var value=Math.round(parseFloat(value)*100)/100;
	var xsd=value.toString().split(".");
	 if(xsd.length==1){
		 value=value.toString()+".00";
		 return value;
		 }
		 if(xsd.length>1){
		 if(xsd[1].length<2){
		 value=value.toString()+"0";
		 }
		 return value;
	 }
}

//验证金钱和发送人数，改变提交按钮状态
SendRed.prototype.checkMoneyPerson = function(){
	var number = parseInt(this.oFm.number.value);
	if(number > 0 && number == parseFloat(this.oFm.number.value) && (number <= parseInt(this.person)) && this.checkMoney(this.oFm.unit_price.value)){
		this.box.querySelectorAll('button')[0].disabled = '';
	}else{
		this.box.querySelectorAll('button')[0].disabled = 'disabled';
	}
	
	this.sum = parseFloat(this.oFm.unit_price.value) * number;
	if(!this.sum || this.sum<=0){
		this.money.innerHTML = '0.00';
	}else{
		this.money.innerHTML = this.autoFloat(parseFloat(this.oFm.unit_price.value) * number);
	}
}

//点击抢红包
SendRed.prototype.robRed = function(){
	var aRed = mui('.red');
	var This = this;
	for(var i=0;i<aRed.length;i++){
		aRed[i].querySelector('.content').addEventListener('tap',function(){
			This.nowRed = this.parentNode;
			this.className = 'content red-open';
			//this.querySelector('.red-status-text').innerHTML = ''; 
			This.redId = this.parentNode.getAttribute('data-red-id');
			This.clickRed(); 
		});
	}
}

//点击红包
SendRed.prototype.clickRed = function(){
	var This = this;
	mui.post(nowPage,{
			doing:'clickRed',
			redId:This.redId
		},function(data){
			if(data == 2){
				This.createCloseRed();
			}else if(data == 0){
				This.createRedList(); 
				This.nowRed.querySelector('.red-status-text').innerHTML = '红包已被抢完';
			}else if(data == 1){
				This.createRedList();
				This.nowRed.querySelector('.red-status-text').innerHTML = '红包已领取';
			}
		},'text'
	);
}

//打开红包
SendRed.prototype.openRed = function(){
	var This = this;
	mui('.open-red-btn')[0].addEventListener('tap',function(){
		mui.post(nowPage,{
				doing:'openRed',
				redId : This.redId
			},function(data){
				if(data != 0){
					This.robMoney = data.sum;
					This.robId = data.red_id;
					This.createRedList();
					This.redMask.close();
					document.body.removeChild(This.redBox);
				}else if(data == 0){  
					This.redBox.querySelector('h4').innerHTML = '红包已被抢完';   
					This.redBox.querySelector('.open-red-btn').style.display = 'none'; 
				} 
			},'json'
		);
	});
}

//生成未打开的红包
SendRed.prototype.createCloseRed = function(){
	var This = this;
	This.redMask = mui.createMask(function(){
		if(This.redBox != null){
			document.body.removeChild(This.redBox);
		}
	});
	This.redOwner = This.nowRed.querySelector('.nickname').childNodes[0].data;
	This.redTitle = This.nowRed.querySelector('.red-title').innerHTML;
	This.redOwnerHeader = This.nowRed.querySelector('.header').src;
	This.redMask.show();
	This.redBox = document.createElement('div');
	This.redBox.id = 'close-red';
	document.body.appendChild(This.redBox);
	This.redBox.innerHTML += '<i class="mui-icon mui-icon-closeempty"></i>';
	This.redBox.innerHTML += '<img src="'+imgDir+'open-red-bg.png" class="red-bg" />';
	This.redBox.innerHTML +='<h2>'+This.redOwner+'</h2>';
	This.redBox.innerHTML += '<h3>发了一个红包</h3>';
	This.redBox.innerHTML += '<h4>'+This.redTitle+'</h4>';
	This.redBox.innerHTML += '<img src="'+This.redOwnerHeader+'" class="red-header" />';
	This.redBox.innerHTML +='<div class="open-red-btn">拆红包</div>';
	This.redBox.querySelector('.mui-icon-closeempty').addEventListener('tap',function(){
		This.redMask.close();
		document.body.removeChild(This.redBox);
	});
	This.openRed();
}


//生成红包记录列表
SendRed.prototype.createRedList = function(){
	var This = this; 
	This.redOwner = This.nowRed.querySelector('.nickname').childNodes[0].data;
	This.redTitle = This.nowRed.querySelector('.red-title').innerHTML;
	This.redOwnerHeader = This.nowRed.querySelector('.header').src;
	this.redList = document.createElement('div'); 
	this.redList.id= 'red-list-page';
	document.body.appendChild(this.redList);
	this.redDetails();
}

//关闭红包列表
SendRed.prototype.redListClose = function(){
	document.body.removeChild(this.redList);
}


//获取红包详情
SendRed.prototype.redDetails = function(){
	var This = this;
	mui.post(nowPage,{
		doing:'findRedDetails',
		redId : This.redId
	},function(data){
		var robStr = '<h4>很遗憾，您没抢到红包</h4>';
		if(data[1] != 0){
			var robStr = '<h4><span>'+data[1]+'</span>元</h4><h5>已存入钱包，可用于提现</h5>';
		} 
		This.redList.innerHTML += '<div class="mui-scroll"><div class="red-list-header"><img src="'+This.redOwnerHeader+'" class="red-list-user-header" /><h2><span>'+This.redOwner+'</span>的红包</h2><h3>'+This.redTitle+'</h3>'+robStr+'</div><div class="red-list-number">'+data[0]+'</div><div class="red-list-list"></div>';	
	    for(var i=2;i<data.length;i++){
	    	This.redList.querySelector('.red-list-list').innerHTML += '<figure><img src="'+data[i].header+'" /><figcaption class="take-user-name">'+data[i].nickname+'</figcaption><figcaption class="time">'+data[i].time+'</figcaption><figcaption class="sum">'+data[i].sum+'元</figcaption><figcaption class="line"></figcaption></figure>';
	    }
	    This.redList.addEventListener('tap',function(){ 
			This.redListClose();
		});
	},'json'
);
}



