function Poster(mobile = true){
	this.page = document.querySelector('#now-page').value;
	if(mobile){
		this.groupId = document.body.getAttribute('data-groupid');
		this.poster = '';  //海报显示区域
	}else{
		this.fm = document.poster_form;   
		this.bgTimer = null;   //设置背景定时器
		this.header = null;   //头像
		this.themeImg = document.querySelector('#theme-img').value;
	}
}



/**
 * 获取一张海报
 */
Poster.prototype.find = function(){
	var This = this;
	mui.post(this.page,{ 
			doing:'findPoster',
			groupId:this.groupId  
		},function(data){
			This.showPoster(data);
		},'text'
	);
}

/**
 * 显示海报区域
 */
Poster.prototype.showPoster = function(data){
	if(this.poster == ''){
		this.poster = document.createElement('div');
		this.poster.id = 'poster-box';
		document.body.appendChild(this.poster); 
		this.poster.innerHTML = '<img src="'+data+'" class="poster" />';
	}
	this.poster.style.display = 'block';
	this.hidePoster();
}


/**
 * 隐藏海报
 */
Poster.prototype.hidePoster = function(){
	var This = this;
	if(this.poster){
		this.poster.querySelector('.poster').addEventListener('tap',function(ev){
			ev.stopPropagation();
		});
		this.poster.addEventListener('tap',function(){
			document.body.removeChild(This.poster);
		});
	}
}

/**
 * 设置海报背景
 */
Poster.prototype.setPosterBg = function(){
	var This = this;
	this.bgTimer = setInterval(function(){
		if(This.fm.poster_bg.value != ''){  
			$('#set-poster')[0].style.backgroundImage = 'url('+$('.img-thumbnail')[0].src+')';
		}else{
			$('#set-poster')[0].style.backgroundImage = '';
		}
	},300);
}

/**
 * 设置二维码
 */
Poster.prototype.setQrcode = function(){
	var px = 0;
	var py = 0;
	var This = this;
	 
	$('#p-qrcode')[0].onmousedown = function(ev){
		ev.preventDefault();
		
		if((this.offsetWidth - ev.layerX <= 30) && (this.offsetHeight - ev.layerY <=30)){
			this.style.cursor = 'se-resize';
			px = ev.layerX;
			py = ev.layerY;
			document.onmousemove = function(ev){ 
				var width = $('#p-qrcode')[0].offsetWidth + (ev.layerX - px);
				var height = $('#p-qrcode')[0].offsetHeight + (ev.layerX - px);
				if((width + $('#p-qrcode')[0].offsetLeft) >= $('#set-poster')[0].offsetWidth - 4){
					height = width = $('#set-poster')[0].offsetWidth - 4 - $('#p-qrcode')[0].offsetLeft;
				}
				if((height + $('#p-qrcode')[0].offsetTop) >= $('#set-poster')[0].offsetHeight - 4){
					height = width = $('#set-poster')[0].offsetHeight - 4 - $('#p-qrcode')[0].offsetTop;
				}
				$('#p-qrcode')[0].style.width = width + 'px';
				$('#p-qrcode')[0].style.height = height + 'px';
				This.fm.qr_z.value = width;
				px = ev.layerX;
				py = ev.layerY;
			}
			
			document.onmouseup = function(){
				$('#p-qrcode')[0].style.cursor = 'move';
				document.onmousemove = null;
			}
			
		}
		else{
			px = ev.layerX;
			py = ev.layerY;
			document.onmousemove = function(ev){
				ev.preventDefault();
				var left = $('#p-qrcode')[0].offsetLeft + ev.layerX - px;
				var top = $('#p-qrcode')[0].offsetTop + ev.layerY - py;
				
				if(left <= 0){
					left = 0;
				}
				if(top <= 0){
					top = 0;
				}
				if(left + $('#p-qrcode')[0].offsetWidth >= $('#set-poster')[0].offsetWidth){
					left = $('#set-poster')[0].offsetWidth - $('#p-qrcode')[0].offsetWidth-4; 
				}
				
				if(top + $('#p-qrcode')[0].offsetHeight >= $('#set-poster')[0].offsetHeight){
					top = $('#set-poster')[0].offsetHeight - $('#p-qrcode')[0].offsetHeight-4;
				}
				
				This.fm.qr_x.value = left;
				This.fm.qr_y.value = top;
				$('#p-qrcode')[0].style.left = left + 'px';
				$('#p-qrcode')[0].style.top = top + 'px';
			}
			document.onmouseup = function(){
				document.onmousemove = null;
				document.onmouseup = null;
			}
		}
			
	}
	
	$('#set-poster')[0].onmouseout = function(){  
		document.onmousemove = null;
		document.onmouseup = null;
	}
	
	$('#p-qrcode')[0].onmouseup = function(){
		this.style.cursor = 'move';
	}
}


/**
 * 设置头像
 */
Poster.prototype.setHeader = function(){
	var This = this;
	$('#poster-option button')[0].onclick = function(){
		if(this.getAttribute('data-status') == 0){
			This.header = document.createElement('img');
			This.header.src = This.themeImg+'poster-header.jpg';
			$('#set-poster')[0].appendChild(This.header);
			this.className = 'btn btn-success';
			this.setAttribute('data-status',1);
			console.log(This.change(This.header)); 
		}else{
			$('#set-poster')[0].removeChild(This.header);
			this.className = 'btn btn-default';
			this.setAttribute('data-status',0);
		}
	}
}


/**
 * 改变设置项大小
 */
Poster.prototype.change = function(obj){
	var px = 0;
	var py = 0;
	var This = this;

	
	obj.onmousedown = function(ev){
		ev.preventDefault();
		
		if((this.offsetWidth - ev.layerX <= 30) && (this.offsetHeight - ev.layerY <=30)){
			this.style.cursor = 'se-resize';
			px = ev.layerX;
			py = ev.layerY;
			document.onmousemove = function(ev){ 
				var width = obj.offsetWidth + (ev.layerX - px);
				var height = obj.offsetHeight + (ev.layerX - px);
				if((width + obj.offsetLeft) >= $('#set-poster')[0].offsetWidth - 4){
					height = width = $('#set-poster')[0].offsetWidth - 4 - obj.offsetLeft;
				}
				if((height + obj.offsetTop) >= obj.offsetHeight - 4){
					height = width = $('#set-poster')[0].offsetHeight - 4 - obj.offsetTop;
				}
				obj.style.width = width + 'px';
				obj.style.height = height + 'px';
				px = ev.layerX;
				py = ev.layerY;
			}
			
			document.onmouseup = function(){
				obj.style.cursor = 'move';
				document.onmousemove = null;
			}
		}else{
			px = ev.layerX;
			py = ev.layerY;
			document.onmousemove = function(ev){
				ev.preventDefault();
				var left = obj.offsetLeft + ev.layerX - px;
				var top = obj.offsetTop + ev.layerY - py;
				
				if(left <= 0){
					left = 0;
				}
				if(top <= 0){
					top = 0;
				}
				if(left + obj.offsetWidth >= $('#set-poster')[0].offsetWidth){
					left = $('#set-poster')[0].offsetWidth - obj.offsetWidth-4; 
				}
				
				if(top + obj.offsetHeight >= $('#set-poster')[0].offsetHeight){
					top = $('#set-poster')[0].offsetHeight - obj.offsetHeight-4;
				}
				
				This.fm.qr_x.value = left;
				This.fm.qr_y.value = top;
				obj.style.left = left + 'px';
				obj.style.top = top + 'px';
			}
			document.onmouseup = function(){
				document.onmousemove = null;
				document.onmouseup = null;
			}
		}
	}
	
	$('#set-poster')[0].onmouseout = function(){  
		document.onmousemove = null;
		document.onmouseup = null;
	}
	
	$('#p-qrcode')[0].onmouseup = function(){
		this.style.cursor = 'move';
	}
}











