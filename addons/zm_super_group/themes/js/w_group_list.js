setOwner(document.getElementById('add_owner'),$('.user-header')[0]);
checkData(document.add_group); 
checkData(document.update_group);
setOwner(document.getElementById('update_owner'),$('.user-header')[1]);
findOneGroup();
setRecommend(); 
setBoutique();


/**
 * 设置精品社群
 * @returns
 */
function setBoutique(){
	var aBou = $('.setBoutique');
	var nowPage = document.getElementById('now-page').value;
	for(var i=0;i<aBou.length;i++){
		aBou[i].addEventListener('click',function(){
			var This = this;
			var xhr = new XMLHttpRequest();
			xhr.open('post',nowPage,true);
			xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xhr.send('doing=setBoutique&groupid='+this.getAttribute('data-id')+'&nowstatus='+this.getAttribute('data-is-boutique'));
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					var d = xhr.responseText;
					if(d == 2){
						if(This.getAttribute('data-is-boutique') == 0){
							This.className = 'btn btn-danger setBoutique';
							This.setAttribute('data-is-boutique',1);
						}else{
							This.className = 'btn btn-default setBoutique';
							This.setAttribute('data-is-boutique',0); 
						}
					}else{
						alert('设置失败！');
					}
				}
			}
		});
	}
}



/**
 * 设置每日推荐
 * @returns
 */
function setRecommend(){
	var aRec = $('.setRecommend');
	var nowPage = document.getElementById('now-page').value;
	for(var i=0;i<aRec.length;i++){
		aRec[i].addEventListener('click',function(){
			var This = this;
			var xhr = new XMLHttpRequest();
			xhr.open('post',nowPage,true);
			xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xhr.send('doing=setRecommend&groupid='+this.getAttribute('data-id')+'&nowstatus='+this.getAttribute('data-is-recommend'));
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					var d = xhr.responseText;
					if(d == 1){
						alert('每日推荐设置已经上限');
					}
					if(d == 2){
						if(This.getAttribute('data-is-recommend') == 0){
							This.className = 'btn btn-primary setRecommend';
							This.setAttribute('data-is-recommend',1);
						}else{
							This.className = 'btn btn-default setRecommend'; 
							This.setAttribute('data-is-recommend',0); 
						}
					}
					if(d == 0){
						alert('设置失败！'); 
					}
				}
			}
		});
	}
}



/**
 * 获取一个社群资料
 * @returns
 */
function findOneGroup(){
	var aUpdateBtn = $('.btn-update');
	var nowPage = document.getElementById('now-page').value;
	for(var i=0;i<aUpdateBtn.length;i++){
		aUpdateBtn[i].addEventListener('click',function(){
			var xhr = new XMLHttpRequest();
			xhr.open('post',nowPage,true);
			xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xhr.send('doing=oneGroup&groupid='+this.getAttribute('data-groupid'));
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					var d = JSON.parse(xhr.responseText);
					setGroupData(d);
				}
			}
		});
	}	
}


/**
 * 设置社群数据
 * @param data
 * @returns
 */
function setGroupData(data){
	var oFm = document.update_group;
	oFm.price.parentNode.style.display = 'none';
	oFm.price.value = '';
	var userHeader = $('.user-header')[1];
	var oUpdateGroup = document.getElementById('update-group');
	var aSelectClass = oFm.class_id.getElementsByTagName('option');
	var aSelectEnter = oFm.enter.getElementsByTagName('option');

	oFm.title.value = data.title; 
	oFm.name.value = data.name;
	oFm.owner.value = data.owner;
	oFm.original_owner.value = data.owner; 
	userHeader.innerHTML = '<dl><dt><img src='+data.ownerHeader+' /></dt><dd>'+data.ownerNickname+'</dd></dl>'; 
	for(var i=0;i<aSelectClass.length;i++){
		if(aSelectClass[i].value == data.class_id){
			aSelectClass[i].selected = 'selected';
		}
	}
	
	for(var i=0;i<aSelectEnter.length;i++){
		if(aSelectEnter[i].value == data.enter){
			aSelectEnter[i].selected = 'selected'; 
		}
		if(data.enter == '3'){
			oFm.price.parentNode.style.display = 'block';
			oFm.price.value = data.price;
		}
	}
	
	oFm.groupid.value = data.group_id;
	oFm.brief.value = data.brief;
	oFm.cover.value = data.cover; 
	oFm.cover.parentNode.parentNode.getElementsByTagName('img')[0].src = data.img_dir + data.cover;
	oFm.header.value = data.header;
	oFm.header.parentNode.parentNode.getElementsByTagName('img')[0].src = data.img_dir + data.header;
}



/**
 * 添加社群数据验证 
 * @returns
 */
function checkData(oFm){  
	
	if(oFm.enter.value == '3'){
		oFm.price.parentNode.style.display = 'block';
	}
	
	oFm.enter.onchange = function(){
		if(this.value == '3'){
			oFm.price.parentNode.style.display = 'block'; 
		}else{
			oFm.price.parentNode.style.display = 'none';
		}
	}
	
	oFm.onsubmit = function(){
		if(oFm.title.value == '' || /^ +$/.test(oFm.title.value)){
			alert('社群标题不能为空！');	 
			return false; 
		}
		
		if(oFm.title.value.length < 4){
			alert('社群标题长度不能小于4位！');    
			return false;
		}
		
		if(oFm.title.value.length > 30){
			alert('社群标题长度不能大于30位！');
			return false;
		}
		
		if(oFm.name.value == '' || /^ +$/.test(oFm.name.value)){
			alert('社群名称不能为空');	
			return false; 
		}
		
		if(lenFor(oFm.name.value) > 30){
			alert('社群名称长度不能大于30字节！');
			return false;
		}
		
		if(oFm.owner.value == ''){
			alert('请设置一位群主！');
			return false;
		}
		
		if(oFm.enter.value == '3'){
			if(!/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(oFm.price.value) || oFm.price.value == '0'){
				alert('请设置正确的入群费用！');
				return false;
			} 
		}
		
		
		if(oFm.brief.value.length > 255){
			alert('社群简介长度不能大于255位！');
			return false;
		}
		
		if(oFm.cover.value == ''){
			alert('请设置社群封面');
			return false;
		}
		
		if(oFm.header.value == ''){
			alert('请设置社群头像');
			return false;
		}
		
		return true;
	}
}


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
 * 设置群主
 * @returns
 */
function setOwner(oOwner,userHeader){
	var oBtn = oOwner.getElementsByTagName('button')[0];
	var oInput = oOwner.getElementsByTagName('input')[0];
	var nowPage = document.getElementById('now-page').value;
	var aUser = '';
	
	oBtn.addEventListener('click',function(){
		if(oInput.value == ''){
			alert('搜索关键词不能为空！');
			return false;
		}
		
		var xhr = new XMLHttpRequest();
		xhr.open('post',nowPage,true);
		xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xhr.send('doing=searchUser&nickname='+oInput.value);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				var d = JSON.parse(xhr.responseText);
				if(d == 0){
					alert('未找到搜索用户！');
				}else{
					userHeader.innerHTML = userHTML(d);
					setOwner(userHeader.getElementsByTagName('dl'));
				}
			}
		}
		
	});
	
	
	function userHTML(data){
		var html = '';
		data.forEach(function(item){
			html += `<dl data-userid="${item.userid}">
				    	<dt><img src="${item.header}" /></dt>
				    	<dd>${item.nickname}</dd>
			    	</dl>`;
		});
		return html;
	}	
	function setOwner(dataList){
		for(var i=0;i<dataList.length;i++){
			dataList[i].onclick = function(){
				for(var j=0;j<dataList.length;j++){
					dataList[j].style.borderColor = '#fff';
				}
				this.style.borderColor = '#428BCA';
				oOwner.getElementsByTagName('input')[1].value = this.getAttribute('data-userid');
			}
		}
	}
}








