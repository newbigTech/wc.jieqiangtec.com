/**
 * 套用消息数据
 * @param data 消息数据
 * @param type 1=历史消息  2=新消息
 * @param isBtootm 是否到页面底部
 * @param ismeImg 是否是当前用户发送图片
 * @returns
 */
function LaddMsg(data,type,isBottom = true,ismeImg = false){

	var html = '';

	data.forEach(function(item){ 
		//文本
		if(item.MsgType == 'TIMTextElem'){
			if(item.From_Account == nowUser){
					if(type == 1){
						html +=`<figure class="me text" data-userid="${item.From_Account}"> 
						    		<img src="${item.header}" class="header" />
						    		<figcaption class="nickname"><span>${item.msgTime}&nbsp;&nbsp;&nbsp;</span>${item.nickname}</figcaption>
						    		<figcaption class="content">
						    			${item.MsgContent}
						    		</figcaption>
						    	</figure>`;
					}else{
						html +=`<figure class="me text" data-userid="${item.From_Account}"> 
			    		<img src="${item.header}" class="header" />
			    		<figcaption class="nickname">${item.nickname}</figcaption>
			    		<figcaption class="content">
			    		<span class="msgStatus"><img src="${imgDir}loading.gif" /></span>
			    			${item.MsgContent}
			    		</figcaption>
			    		</figure>`; 
					}
			}else{
				html +=`<figure class="other text" data-userid="${item.From_Account}">
	    		<img src="${item.header}" class="header" />
	    		<figcaption class="nickname">${item.nickname}<span>&nbsp;&nbsp;&nbsp;${item.msgTime}</span></figcaption> 
	    		<figcaption class="content">
	    			${item.MsgContent}
	    		</figcaption>
	    	</figure>`;
			}
		}
		
		//图片
		if(item.MsgType == 'TIMImageElem'){
			if(item.From_Account == nowUser){
				if(type == 1){
					html +=`<figure class="me image" data-userid="${item.From_Account}">
					    		<img src="${item.header}" class="header" />
						    		<figcaption class="nickname"><span>${item.msgTime}&nbsp;&nbsp;&nbsp;</span>${item.nickname}</figcaption>
						    		<figcaption class="content" style="width:${item.min_width}rem;height:${item.min_height}rem;">
						    			<img src="${item.min_url}" data-msg-img="${item.MsgContent}" />  
						    		</figcaption>
						    	</figure>`;
				}else{
					if(!ismeImg){
						html +=`<figure class="me image" data-userid="${item.From_Account}">
			    		<img src="${item.header}" class="header" />
				    		<figcaption class="nickname">${item.nickname}</figcaption>
				    		<figcaption class="content" style="width:${item.width}rem;height:${item.height}rem;background:#ccc;">
				    			${item.content}
				    		</figcaption>
				    	</figure>`;
					}else{
						setTimeout(function(){
							mui('#now-user-send-img')[0].parentNode.style = "width:"+newWidth+"rem;height:"+newHeight+"rem;background:#fff;";
							mui('#now-user-send-img')[0].parentNode.innerHTML = "<img src='"+data[0].min_url+"' data-msg-img='"+data[0].MsgContent+"'/>";  
						},500);
					}
				}	    
			}else{
				html +=`<figure class="other image" data-userid="${item.From_Account}">
	    		<img src="${item.header}" class="header" />
		    		<figcaption class="nickname">${item.nickname}<span>&nbsp;&nbsp;&nbsp;${item.msgTime}</span></figcaption>
		    		<figcaption class="content" style="width:${item.min_width}rem;height:${item.min_height}rem;">
		    			<img src="${item.min_url}" data-msg-img="${item.MsgContent}" />  
		    		</figcaption>
		    	</figure>`; 
			}
		}
		
		//语音
		if(item.MsgType == 'TIMSoundElem'){
				if(item.From_Account == nowUser){
					if(type == 1){
						html +=`<figure class="me voice" data-userid="${item.From_Account}">
					    		<img src="${item.header}" class="header" /> 
						    		<figcaption class="nickname"><span>${item.msgTime}&nbsp;&nbsp;&nbsp;</span>${item.nickname}</figcaption>
						    		<figcaption class="content" style="width:${item.contentWidth}rem;" data-voice-url="${item.MsgContent}" data-voice-id="${item.voiceid}"> 
						    			<span class="time">${item.duration}"</span>
						    			<span class="progress">
						    				<span class="now"></span>
						    				<span class="control"></span> 
						    			</span>
						    			<span class="iconfont icon-bofang1"></span> 
						    		</figcaption>  
						    	</figure>`;       
					}else{
						html +=`<figure class="me voice" data-userid="${item.From_Account}">
					    		<img src="${item.header}" class="header" /> 
						    		<figcaption class="nickname">${item.nickname}</figcaption>
						    		<figcaption class="content" style="width:${item.contentWidth}rem;">  
						    		<span class="msgStatus"><img src="${imgDir}loading.gif" /></span>
						    			<span class="time">${item.duration}"</span>   
						    			<span class="progress">
						    				<span class="now"></span>
						    				<span class="control"></span>
						    			</span>
						    			<span class="iconfont icon-bofang1"></span>
						    		</figcaption> 
						    	</figure>`;   
					}
			}else{
				html +=`<figure class="other voice" data-userid="${item.From_Account}">
				    		<img src="${item.header}" class="header" />  
				    		<figcaption class="nickname">${item.nickname}<span>&nbsp;&nbsp;&nbsp;${item.msgTime}</span></figcaption>
				    		<figcaption class="content" style="width:${item.contentWidth}rem;" data-voice-url="${item.MsgContent}" data-voice-id="${item.voiceid}"> 
				    			<span class="time">${item.duration}"</span> 
				    			<span class="progress">
				    				<span class="now"></span>
				    				<span class="control"></span>
				    			</span>
				    			<span class="iconfont icon-bofang1"></span>
				    		</figcaption> 
				    	</figure>`;    
			}
		}
		
		//红包
		if(item.MsgType == 'TIMRedElem'){
				if(item.From_Account == nowUser){
					if(type == 1){
						   html += `<figure class="me red" data-red-id="${item.redId}" data-userid="${item.From_Account}">
						    		<img src="${item.header}" class="header" />
							    		<figcaption class="nickname">${item.nickname}<span>&nbsp;&nbsp;&nbsp;${item.msgTime}</span></figcaption>
							    		<figcaption class="content red-${item.status}"> 
							    			<div class="red-main">
							    				<div class="red-title">${item.title}</div>
							    				<div class="red-status-text">${item.statusText}</div>
							    			</div>
							    			<div class="red-footer">
							    				现金红包
							    			</div>
							    		</figcaption> 
							    	</figure>`;
					}else{
						  html +=`<figure class="me red" data-red-id="${item.redId}" data-userid="${item.From_Account}">
						    		<img src="${item.header}" class="header" />
							    		<figcaption class="nickname">${item.nickname}<span>&nbsp;&nbsp;&nbsp;${item.msgTime}</span></figcaption>
							    		<figcaption class="content red-close"> 
							    			<div class="red-main">
							    				<div class="red-title">${item.title}</div>
							    				<div class="red-status-text">领取红包</div>
							    			</div>
							    			<div class="red-footer">
							    				现金红包
							    			</div>
							    		</figcaption> 
							    	</figure>`;
					}
				}else{
					html += `<figure class="other red" data-red-id="${item.redId}" data-userid="${item.From_Account}">
				    		<img src="${item.header}" class="header" />
					    		<figcaption class="nickname">${item.nickname}<span>&nbsp;&nbsp;&nbsp;${item.msgTime}</span></figcaption>
					    		<figcaption class="content red-${item.status}"> 
					    			<div class="red-main">
					    				<div class="red-title">${item.title}</div>
					    				<div class="red-status-text">${item.statusText}</div> 
					    			</div>
					    			<div class="red-footer">
					    				现金红包
					    			</div>
					    		</figcaption> 
					    	</figure>`; 
				}
		}
		
	});
	
	
	if(type == 1){
		mui('.mui-scroll')[0].innerHTML = html + mui('.mui-scroll')[0].innerHTML;
	} 
	
	if(type == 2){
		mui('.mui-scroll')[0].innerHTML += html; 	
	}
	
	clickShowMaxImg();
	clickPlayVoice();
	if(mui('#user-control')[0]){
		callUserControl();  //唤出用户控制面板
	}
	var red = new SendRed();
	red.robRed();
	if(isBottom){
		setTimeout(resetContentBottom,500);  
	}
	
}

//S发送文本消息 
function LsendTextMsg(){
	var oSendBtn = document.querySelector('.send-btn');
	var oTextBorder = document.getElementById('text-border'); 
	oTextBorder.addEventListener('foucs',checkBannedTalk);
	oSendBtn.addEventListener('tap',function(){	
		checkBannedTalk();  
		if(oTextBorder.value != ''){
			var This = this;
			This.oValue = parseFace(oTextBorder.value); 
			
			nowUserSendMsg(This.oValue,'TIMTextElem');  
			mui.post(nowPage,{
					doing : 'sendTextMsg',
					groupId : selToID,
					userId : nowUser, 
					content : This.oValue,
					eith : oTextBorder.getAttribute('data-userid')
				},function(data){ 
					var oStatus = mui('.msgStatus');
					if(data != 0){   
						closeEith(); 
						var nickname = oStatus[oStatus.length-1].parentNode.parentNode.getElementsByTagName('figcaption')[0];
						nickname.innerHTML = '<span>'+data + '</span>&nbsp;&nbsp;&nbsp;' + nickname.innerHTML;
						oStatus[oStatus.length-1].parentNode.removeChild(oStatus[oStatus.length-1]);
					}else{
						oStatus[oStatus.length-1].innerHTML = '!失败';
					}
				},'text'); 
			}
		});
}

//解析文本消息（表情）
function parseFace(string){
	//S解析消息
	var parent =/\{(1_([1-9][\d]{0,2}))\}/m;       
	var m = null; 
		while((m = parent.exec(string)) != null){
			if(parseInt(m[2]) <= 36){
				string = string.replace(parent,'<img src="'+faceDir+'1/'+m[1]+'.png" style="width:0.9rem;height:0.9rem;margin-right:0.1rem;display:inline-block;" align="absmiddle" />');
			}
		}   
	return string;
}   
 
//当前用户发送文本消息 
function nowUserSendMsg(content,type = 'TIMTextElem'){
	if(mui('.eith-status-text')[0].innerHTML != ''){
		content = mui('.eith-status-text')[0].innerHTML+' '+content;
	} 
	var data = [{
		MsgType : type,
		From_Account : nowUser,
		header : nowHeader,
		nickname : nowNickname,
		MsgContent : content
	}]; 

	LaddMsg(data,2,true);
	document.querySelector('#text-border').value = ''; 
}


//当前用户发送图片消息
function nowUserSendImageMsg(width,height){
	var loadingW = width / 5; 
	var content = '<img id="now-user-send-img" src="'+imgDir+'loading.gif" style="width:'+loadingW+'rem;position:relative;left:'+((width-loadingW)/2)+'rem;top:'+((height-loadingW)/2)+'rem;" /> '; 
	var data = [{
		MsgType : 'TIMImageElem',
		From_Account : nowUser,
		header : nowHeader,
		nickname : nowNickname,
		width : width,
		height : height,
		content : content
	}]; 
	LaddMsg(data,2,true); 
}
 

//newMsgList 为新消息数组，结构为
function OnMsgNotify(newMsgList) {     
	mui.post(nowPage,{ 
		doing : 'newMsg',
		groupId : selToID,   
		seq : newMsgList[0].seq
	},function(data){ 
		if(data[0].groupid == selToID){
			var bottom = true;
			var bottomPosition = mui('.mui-scroll')[0].offsetHeight - mui('.mui-scroll-wrapper')[0].offsetHeight+parseFloat(getStyle(mui('.mui-content')[0],'paddingTop'))+parseFloat(getStyle(mui('.mui-content')[0],'paddingBottom'));
			if(-LouieScroll.y != bottomPosition) bottom = false;
			if(data[0].MsgType == 'TIMTextElem' && nowUser == data[0].From_Account) return ;
			if(data[0].MsgType == 'TIMSoundElem' && nowUser == data[0].From_Account) return ; 
			if(data[0].MsgType == 'TIMImageElem' && nowUser == data[0].From_Account){
				LaddMsg(data,2,bottom,true);
				return;
			}; 
			LaddMsg(data,2,bottom); 
		} 
	},'json');
	
	var timer = setTimeout(function(){
		mui.post(nowPage,{
				doing : 'isEithTpl',
				seq:newMsgList[0].seq
				},function(data){
					  
				},'text'
			);
		
		if(roomType == 'private'){ 
			mui.post(nowPage,{
				doing :'privateTpl',
				seq:newMsgList[0].seq
				},function(data){
					  console.log(data);
				},'json' 
			);
		}
	},3000); 
	

	 
}



/*
 * 获取元素的样式
 */
function getStyle(element,attr){ 
    return element.currentStyle ? element.currentStyle[attr] : getComputedStyle(element)[attr];
}


function Lhistroy(){
	//S初始拉取20条历史消息
	var start = 0;
	var end = 20;
	LHistoryMsg();
	function LHistoryMsg(bottom = true){
		mui.post(nowPage,{ 
			doing : 'findHistoryMsg', 
			groupId : selToID,  
			start : start,
			end : end
		},function(data){ 
			var total = data[0];
			if(data){
					data.shift();
					LaddMsg(data.reverse(),1,bottom);
					start +=20;
				}	
			if(total > 20){
				mui('.mui-content')[0].addEventListener('scroll',louieDown);
			}
		},'json'); 
	}   
	

	function louieDown(){
		if(LouieScroll.y == 0){ 
			LHistoryMsg(false); 
		}
	}
	
	
}


/**
 * 点击放大大图
 * @returns
 */
function clickShowMaxImg(){
	//S获取所有图片消息
	setTimeout(function(){
		var msgImage = mui('.image .content img');
	for(var i=0;i<msgImage.length;i++){
		msgImage[i].addEventListener('tap',function(){
			this.disabled = 'disabled'; 
			var This = this;
			mui.post(nowPage,{
				doing:'showMaxImage',
				msgImgId:This.getAttribute('data-msg-img')
			},function(data){
				if(data == 0){
					mui.alert('获取图片失败！');
					return false;
				}else{
					var screenW = document.documentElement.clientWidth;
					var screenH = document.documentElement.clientHeight;
					var css = '';
					This.maxImg = document.createElement('div'); 
					This.maxImg.className = 'showMaxImage';
					This.maxImg.innerHTML = '<img src="'+data.max_url+'" class="mui-scroll" />';
					if(screenW <= data.max_width){
						css = 'width:100%;';
						var nowImgHeight = screenW*(data.max_height/data.max_width);
						if(nowImgHeight <= screenH){
							css = 'top:'+((screenH - nowImgHeight) / 2)+'px';  
						}else{
							css = 'top:0;';  
						}
						This.maxImg.getElementsByTagName('img')[0].style.cssText = css;
					}else{
						css = 'width:'+data.max_width+'px;';
						css +='left:'+(screenW-data.max_width)/2+'px;';
						css += 'height:'+data.max_height+'px;';
						css +='top:'+(screenH-data.max_height)/2+'px;';
						This.maxImg.getElementsByTagName('img')[0].style.cssText = css; 
					}	
					document.body.appendChild(This.maxImg);
					This.maxImg.addEventListener('tap',function(){
						This.disabled = ''; 
						this.parentNode.removeChild(this);
					});  
					mui('.showMaxImage').scroll({
						deceleration: 0.005,
						 indicators: false,
					});
				} 
			},'json'
			);
		});
	}
	//S获取所有图片消息 
	},500);  
}

/**
 * 重置聊天页面底部位置 
 * @returns
 */
function resetContentBottom(){
	if(mui('.mui-scroll')[0].scrollHeight > mui('.mui-scroll-wrapper')[0].offsetHeight){
		var oNavHeight = document.querySelector('nav').offsetHeight;
		mui('.mui-content')[0].style.paddingBottom = oNavHeight + 'px'; 
		var bottomPosition = mui('.mui-scroll')[0].offsetHeight - mui('.mui-scroll-wrapper')[0].offsetHeight+parseFloat(getStyle(mui('.mui-content')[0],'paddingTop'))+parseFloat(getStyle(mui('.mui-content')[0],'paddingBottom')); 
		mui('.mui-scroll-wrapper').scroll().scrollTo(0,-bottomPosition,0);
	} 
}
 

/**
 * 显示或者隐藏图片、表情、红包按钮
 */
function showHiddenPic(){
	var oPicBtn = document.querySelector('#showHiddenPic');
	var oPicBox = document.querySelector('#pic-face-red'); 
	var recodeBox = mui('#recode')[0];
	oPicBtn.addEventListener('tap',function(){
		checkBannedTalk();
		if(getStyle(oPicBox,'display') == 'none'){
			oPicBox.style.display = 'block'; 
			recodeBox.style.display = 'none';
			recodeBox.showRecode = true;  
			resetContentBottom();
			var oTextBorder = document.getElementById('text-border');
			oTextBorder.blur();  
		}else{
			oPicBox.style.display = 'none';
			if(mui('.face')[0]){
				mui('.face')[0].style.display = 'none'; 
				mui('.face')[0].parentNode.removeChild(mui('.face')[0]);
			}
			resetContentBottom();
		}
	});
}

/**
 * 显示隐藏语音区域
 * @returns
 */
function showHiddenRecode(){
	var recodeBtn = mui('#showHiddenVoice')[0]; 
	var recodeBox = mui('#recode')[0];
	var oPicBox = document.querySelector('#pic-face-red'); 
	recodeBtn.addEventListener('tap',function(){
		checkBannedTalk();
		if(getStyle(recodeBox,'display') == 'block'){
			recodeBox.style.display = 'none';
			resetContentBottom();
		}else{ 
			recodeBox.style.display = 'block';  
			oPicBox.style.display = 'none';
			resetContentBottom();   
		}
	});
}


/**
 * 选择表情
 */
function selectFace(btn){
	var aLi = '';
	for(var i=1;i<37;i++){ 
		aLi += '<li data-face-coding="1_'+i+'"><img src="'+faceDir+'1/1_'+i+'.png"></li>';
	}
	btn.face = document.createElement('div');
	btn.face.className = 'face';
	btn.face.innerHTML += '<ul class="mui-scroll">'+aLi+'</ul>';
	btn.parentNode.appendChild(btn.face); 
	mui('.face')[0].style.display = 'block'; 
	mui('.face').scroll({
		deceleration: 0.0009,
		
		 indicators: false,
		 scrollX: true, 
		 scrollY: false,
	});
	var aLis = mui('.face ul li');
	for(var i=0;i<aLis.length;i++){
		aLis[i].addEventListener('tap',function(){
			mui('#text-border')[0].value += '{'+this.getAttribute('data-face-coding')+'}'; 
			mui('#text-border')[0].style.cssText = 'width:8rem;';
			mui('#text-border')[0].focus();  
			document.querySelector('.send-btn').style.display = 'block';  
		});
	}
}


/**
 * 发送图片
 */ 
function sendPic(btn){
	if(btn.upload){
		btn.parentNode.removeChild(btn.upload);
	}
	btn.upload = document.createElement('div');
	btn.upload.style.display = 'none';
	btn.upload.innerHTML = "<input type='file' accept='image/*' />";  
	btn.parentNode.appendChild(btn.upload);
	btn.upload.getElementsByTagName('input')[0].click(); 
	
	
	btn.upload.getElementsByTagName('input')[0].addEventListener('change',function(){ 
		if(!/^image\/\w+$/.test(this.files[0].type)){
			mui.alert('请选择一张图片！');
			return false;  
		}
		
		
		var MyTest = this.files[0];
		var reader = new FileReader();
		reader.readAsDataURL(MyTest);
		reader.onload = function(theFile) {
		　  var image = new Image();
		   image.src = theFile.target.result;
		   image.onload = function() {
		   　　if(this.width > 360){
			   	if(this.width > this.height){
			   		var ratio = this.height / this.width;
			   		var cW = 360; 
			   		var cH = 360 * ratio;   
			   	}else if(this.width < this.height){
			   		var cW = 360;
			   		var cH = this.height * (360 / this.width); 
			   	}else{
			   		var cW = 360;
			   		var cH = 360;
			   	}
		   　　}else{
			   var cW = this.width;
			   var cH = this.height;
		   　　}
		   	 newWidth = cW/60;
		   	 newHeight = cH/60; 
		     nowUserSendImageMsg(cW/60,cH/60);
		   };
		};
		
		
		
		var xhr = new XMLHttpRequest();
		xhr.onload = function(){
			var nickname = mui('#now-user-send-img')[0].parentNode.parentNode.getElementsByTagName('figcaption')[0];
			nickname.innerHTML = '<span>'+this.responseText+'</span>&nbsp;&nbsp;&nbsp;'+nickname.innerHTML;
		}	
		xhr.open('post',nowPage,true);
		xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
		var oFormData = new FormData();
		for(var i=0;i<this.files.length;i++){
			oFormData.append('img[]',this.files[i]);
		}
		oFormData.append('doing','sendPicMsg');
		oFormData.append('userId',nowUser);
		oFormData.append('groupId',selToID);
		xhr.send(oFormData);
	});
}










/**
 * 发送语音
 * @returns
 */
function sendVoice(){
	var LchangeRecord = mui('.circle-inner')[0];
	var oTime = 0;
	
	wx.ready(function(){ 
		
		 
		LchangeRecord.addEventListener('tap',function(){
				closeEith();  //发送语音不包含@功能，取消@
				var This = this;
				if(this.getAttribute('data-status') == 'working'){
					LStopRecord(This);  
					this.setAttribute('data-status','send'); 
					this.style.backgroundImage = 'url('+imgDir+'voice-send.png)';   
					this.innerHTML = '发送'; 
				}else if(this.getAttribute('data-status') == 'start'){
					Lstatus = true; 
					this.setAttribute('data-status','working');
					oTime = 0;
					This.timer = setInterval(function(){
						oTime ++;
						if(oTime >= 60){
							oTime == 60; 
							clearInterval(This.timer); 
						}
						mui('.recode-hint-text')[0].innerHTML = '正在录音...('+oTime+')';
					},1000);
					this.style.backgroundImage = 'url('+imgDir+'voice-stop.png)';
					mui('.voice-audition')[0].style.display = 'none';
					mui('.voice-reset')[0].style.display = 'none'; 
					wx.startRecord();
				}else if(this.getAttribute('data-status') == 'send'){
					this.setAttribute('data-status','start'); 
					this.style.backgroundImage = 'url('+imgDir+'voice-start.png)';
					this.innerHTML = '';
					LuploadVoice(this.getAttribute('data-voice-id'));  
					nowUserSendVoiceMsg(this.getAttribute('data-voice-id')); 
					mui('.voice-audition')[0].style.display = 'none';
					mui('.voice-reset')[0].style.display = 'none';  
				}
			});
		
		//录音监听，超过1分钟执行
		wx.onVoiceRecordEnd({
		    complete: function (res) {
		        var localId = res.localId;   
		        mui('.recode-hint-text')[0].innerHTML = '单击一次录音';
		        mui('.circle-inner')[0].innerHTML = '发送';     
		        mui('.circle-inner')[0].style.backgroundImage = 'url('+imgDir+'voice-send.png)';
		        mui('.circle-inner')[0].setAttribute('data-status','send');  
		        mui('.circle-inner')[0].setAttribute('data-voice-id',localId);
		        mui('.voice-audition')[0].setAttribute('data-voice-id',localId);
		        mui('.voice-audition')[0].style.display = 'block'; 
		        mui('.voice-reset')[0].style.display = 'block'; 
		    }
		});
		
		//上传语音至微信服务器,然后发送
		function LuploadVoice(id){
			wx.uploadVoice({
			    localId: id, 
			    isShowProgressTips: 0,  
			        success: function (res) {
			        var serverId = res.serverId; 
			        mui.post(nowPage,{ 
			    		doing:'sendVoiceMsg',
			    		voiceId:serverId,
			    		userId:nowUser,
			    		groupId:selToID,
			    		duration:oTime
			    	},function(data){
			    		var oStatus = mui('.msgStatus');
			    		if(data != 0){ 
			    			var nickname = oStatus[oStatus.length-1].parentNode.parentNode.getElementsByTagName('figcaption')[0];
			    			nickname.innerHTML = '<span>'+data.msgTime+'</span>&nbsp;&nbsp;&nbsp;' + nickname.innerHTML;
			    			oStatus[oStatus.length-1].parentNode.setAttribute('data-voice-url',data.url);
			    			oStatus[oStatus.length-1].parentNode.setAttribute('data-voice-id',data.voiceid);
			    			oStatus[oStatus.length-1].parentNode.removeChild(oStatus[oStatus.length-1]);
			    		}else{ 
			    			oStatus[oStatus.length-1].innerHTML = '!失败'; 
			    		}
			    	},'json'
			        );
			    }
			});
		}
		
		//停止录音
		function LStopRecord(This){
			Lstatus = false;
			clearInterval(This.timer);  
			mui('.recode-hint-text')[0].innerHTML = '单击一次录音';
			This.innerHTML = '录音'; 
			wx.stopRecord({
			    success: function (res) {
			        var localId = res.localId; 
			        mui('.voice-audition')[0].setAttribute('data-voice-id',localId);
			        mui('.voice-audition')[0].style.display = 'block';
			        mui('.voice-reset')[0].style.display = 'block';  
			        This.setAttribute('data-voice-id',localId);
			    }
			});
		}
		
		
		
		Laudition();
		//试听录音
		function Laudition(){
			mui('.voice-audition')[0].addEventListener('tap',function(){
	        	mui('.recode-hint-text')[0].innerHTML = '试听中...';
	        	LPlayVoice(this.getAttribute('data-voice-id'));
	        	onLVoicePlayEnd();
	        });
		}
		
		
		
		//重置录音
		mui('.voice-reset')[0].addEventListener('tap',function(){ 
			this.style.display = 'none';  
			mui('.voice-audition')[0].style.display = 'none';
			mui('.circle-inner')[0].setAttribute('data-status','start');
			mui('.circle-inner')[0].innerHTML = ''; 
			mui('.circle-inner')[0].style.backgroundImage = 'url('+imgDir+'voice-start.png)'; 
		}); 
		
		//当前用户发送语音
		function nowUserSendVoiceMsg(localId){
			var data = [{
				MsgType : 'TIMSoundElem',
				From_Account : nowUser,
				header : nowHeader,
				nickname : nowNickname,
				duration : oTime, 
				contentWidth : 3 + (oTime / 10)
			}]; 
			LaddMsg(data,2,true);  
		}
		
		 
		//播放临时录音
		function LPlayVoice(id){
			wx.playVoice({
			    localId: id
			}); 	
		}
		
		
		//监听语音播放完毕
		function onLVoicePlayEnd(){
			wx.onVoicePlayEnd({
			    success: function (res) {
			        var localId = res.localId;
			        mui('.recode-hint-text')[0].innerHTML = '单击一次录音';
			    }
			});
		}
			
	});

}

function clickPlayVoice(){
	var aPlayVoiceBtn = mui('figure.voice .content .iconfont');
	var aVoiceLength = aPlayVoiceBtn.length; 
	for(var i=0;i<aVoiceLength;i++){
		aPlayVoiceBtn[i].index = i;
		aPlayVoiceBtn[i].addEventListener('tap',function(){
			goOn(this,aPlayVoiceBtn);   
		});
	}
}



/**
 * 不间断播放语音
 */
function goOn(This,aVoice){
	if(This.className == 'iconfont icon-bofang1'){
		This.className = 'iconfont icon-bofang';
		if(nowPlayVoice == null){ 
			nowPlayVoice = This;
		}else{
			if(nowPlayVoice != This){ 
					wx.stopVoice({ 
					    localId: nowPlayVoice.parentNode.getAttribute('data-voice-locaid')
					}); 
					nowPlayVoice.className = 'iconfont icon-bofang1';
					nowPlayVoice = This;   
			}	
		}
			wx.downloadVoice({
			    serverId: This.parentNode.getAttribute('data-voice-url'),   
			    isShowProgressTips: 0, 
			    success: function (res) {
			        var localId = res.localId;
			        This.parentNode.setAttribute('data-voice-locaid',localId);   
			        wx.playVoice({ 
					    localId: localId   
					});
			        
			        wx.onVoicePlayEnd({
			            success: function (res) {
			                This.className = 'iconfont icon-bofang1';
			                if(This.index < (aVoice.length-1)){
			                	goOn(aVoice[This.index+1],aVoice);  
			                }   
			            }
			        });  
			    }
			});
	}else{
		This.className = 'iconfont icon-bofang1';  
		wx.pauseVoice({
		    localId: This.parentNode.getAttribute('data-voice-locaid')
		});
	}
}



/**
 * 关注社群
 */
function attention(){
	var attentionBtn = document.querySelector('header button');
	if(attentionBtn){
	attentionBtn.addEventListener('tap',function(){
		var This = this;
		mui.post(nowPage,{
			doing:'attentionGroup',
			groupid:This.getAttribute('data-group-id') 
		},function(data){
			if(data == 1){
				This.parentNode.removeChild(This);  
				mui.toast('操作成功');  
			}else{
				mui.alert('关注失败！'); 
			}
		},'text'
	);
	});
	}
}


//显示隐藏社群控制面板
function showGroupManage(){
	mui('#showHiddenOther')[0].addEventListener('tap',function(){
		mui('#group_manage_box').popover('show');
	});
	
	if(mui('#hidden_group_manage_box')[0]){
		mui('#hidden_group_manage_box')[0].addEventListener('tap',function(){
			mui('#group_manage_box').popover('hide');
		});
	}
}

//开启关闭全员禁言
function openCloseBannerTalk(){
	var controlBtn = document.querySelector('.banned-talk-status');
	
	if(controlBtn){
		controlBtn.parentNode.addEventListener('tap',function(){
			var This = this;
			var nowStatus = this.getAttribute('data-banned-talk');
			mui.post(nowPage,{
					doing:'controlTalk',
					talkStatus:nowStatus,
					groupId : this.getAttribute('data-groupid')
				},function(data){
					if(data == 1){
						if(nowStatus == 1){
							This.setAttribute('data-banned-talk',0);
							controlBtn.innerHTML = '开启';
						}else{
							This.setAttribute('data-banned-talk',1);
							controlBtn.innerHTML = '关闭';
						}
						mui('#group_manage_box').popover('hide');
					}else{
						mui.toast('设置失败',{ duration:'short'}); 
					}
				},'text'
			);
		});
	}	
}

//进入社群初始禁言状态
function initTalk(){
	if(document.body.getAttribute('data-banned-talk') == 1){
		if(nowUser != document.body.getAttribute('data-owner')){
			mui('#text-border')[0].value = '全员禁言中';
			mui('#text-border')[0].style.color = '#ccc'; 
			mui('#text-border')[0].disabled = 'disabled';
			mui('#showHiddenVoice')[0].disabled = 'disabled';
			mui('#showHiddenPic')[0].disabled = 'disabled';
			mui('.send-btn')[0].disabled = 'disabled';
		}
	}
}

function checkBannedTalk(){
	mui.post(nowPage,{
			doing:'checkBannedTalk',
			groupId : document.body.getAttribute('data-groupid')
		},function(data){
			if(data == 1){
				if(nowUser != document.body.getAttribute('data-owner')){
					mui('#text-border')[0].blur();
					mui('#text-border')[0].value = '全员禁言中';
					mui('#text-border')[0].style.color = '#ccc';
					mui('#text-border')[0].disabled = 'disabled';
					mui('#showHiddenVoice')[0].disabled = 'disabled';
					mui('#showHiddenPic')[0].disabled = 'disabled';
					mui('.send-btn')[0].disabled = 'disabled';
					mui('#recode')[0].style.display = 'none'; 
					mui('#pic-face-red')[0].style.display = 'none';
					wx.ready(function(){
						wx.stopRecord();
					});
					window.isBannedTale = true;
				}
			}
		},'text'
	);
}



mui('nav')[0].addEventListener('tap',function(){
	checkBannedTalk();
});











