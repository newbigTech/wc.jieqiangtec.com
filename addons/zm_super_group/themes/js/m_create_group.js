document.addEventListener('touchstart',function(ev){
	ev.preventDefault();
}); 
mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false,
}); 

var oFm = document.createGroup;
var nowPage = document.body.getAttribute('data-url');  

setHeader();
selectEnter();
setCover();


//设置社群头像
function setHeader(){
	oFm.header_file.addEventListener('change',function(){
		var xhr = new XMLHttpRequest();
		xhr.onload = function(){
			var d = JSON.parse(xhr.responseText);
			oFm.header.value = d.dataUrl;
			mui('#header-pic')[0].src = d.imgUrl; 
		}
		xhr.open('post',nowPage,true);
		xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
		var oFormData = new FormData();
		oFormData.append('img[]',this.files[0]);
		oFormData.append('doing','uploadImg'); 
		xhr.send(oFormData); 
	});
}

/**
 * 选择入群方式
 */
function selectEnter(){
	mui('.group-enter')[0].addEventListener('tap',function(ev){
		var nowClick = ev.target;
		if(nowClick.innerHTML == '付费' || nowClick.value == '3'){
			oFm.price.parentNode.style.display = 'block';
		}
		if(nowClick.innerHTML == '无验证' || nowClick.value == '2'){
			oFm.price.parentNode.style.display = 'none';
			oFm.price.value = '';
		}
	});  
}


/**
 * 设置群封面
 */
function setCover(){
	oFm.cover_contral.addEventListener('change',function(){
		var xhr = new XMLHttpRequest();
		xhr.onload = function(){
			var d = JSON.parse(xhr.responseText); 
			oFm.cover.value = d.dataUrl;
			mui('#cover-pic')[0].src = d.imgUrl;
		}
		xhr.open('post',nowPage,true);
		xhr.setRequestHeader('X-Request-With','XMLHttpRequest');
		var oFormData = new FormData();
		oFormData.append('img[]',this.files[0]);
		oFormData.append('doing','uploadImg');
		xhr.send(oFormData); 
	});
}





var oCreate = document.querySelector('.create-btn');  
oCreate.addEventListener('tap',function(){
	var This = this;  
	if(oFm.header.value == ''){
		mui.alert('请设置社群头像');
		return false;
	} 
	
	if(oFm.name.value == '' || /^ +$/.test(oFm.name.value)){
		mui.alert('社群名称不能为空');	
		return false; 
	}
	
	if(lenFor(oFm.name.value) > 30){
		mui.alert('社群名称长度不能大于30字节！');
		return false;
	}
	
	
	if(oFm.title.value == '' || /^ +$/.test(oFm.title.value)){
		mui.alert('社群标题不能为空！');	 
		return false; 
	}
	
	if(oFm.title.value.length < 4){
		mui.alert('社群标题长度不能小于4位！');    
		return false;
	}
	
	if(oFm.title.value.length > 25){
		mui.alert('社群标题长度不能大于25位！');  
		return false; 
	}
	

	if(oFm.enter.value == '3'){
		if(!/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(oFm.price.value) || oFm.price.value == '0'){
			mui.alert('请设置正确的入群费用！');
			return false; 
		}
	} 
	
	if(oFm.cover.value == ''){
		mui.alert('请设置社群封面');
		return false;
	}
		
	if(oFm.brief.value.length > 255){
		mui.alert('社群简介长度不能大于255位！');
		return false;
	}
	
	this.disabled = 'disabled';
	this.innerHTML = '创建社群中...'; 
	var mask = mui.createMask();
	mask.show();
	var xhr = new XMLHttpRequest();
	xhr.open('post',nowPage,true);
	xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');  
	var oFormData = new FormData(oFm);  
	oFormData.append('doing','addGroup');
	xhr.send(oFormData);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			var data = xhr.responseText;
			if(data == 1){
				This.disabled = '';
				This.innerHTML = '创建'; 
				mask.close();
				clearForm(); 
				mui.alert('创建社群成功,请等待审核结果！','提示','确定',function(){
					window.location.href = This.getAttribute('data-succ-url'); 
				}); 
			}else{
				mui.alert('创建社群失败！'); 
				This.disabled = ''; 
				This.innerHTML = '创建'; 
				mask.close();
			}
		}
	}
	
});




/**
 * 返回字符串字节长度
 * @param str
 * @returns
 */
function lenFor(str){
	　　var byteLen=0,len=str.length;
	　　if(str){
	　　　　for(var i=0; i<len; i++){
	　　　　　　if(str.charCodeAt(i)>255){
	　　　　　　　　byteLen += 3;
	　　　　　　}
	　　　　　　else{
	　　　　　　　　byteLen++;
	　　　　　　}
	　　　　}
	　　　　return byteLen;
	　　}
	　　else{
	　　　　return 0;
	　　}
	}


/**
 * 清空表单所有value值
 */
function clearForm(){
	var oSystemImg = document.querySelector('#system-img-url').value; 
	oFm.title.value = oFm.name.value = oFm.cover.value = oFm.header.value = oFm.class_id.value = oFm.enter.value = oFm.price.value = oFm.brief.value = '';
	mui('#header-pic')[0].src = oSystemImg+'nopic-small.jpg';
	mui('#cover-pic')[0].src = oSystemImg+'cover-no.jpg';     
}
