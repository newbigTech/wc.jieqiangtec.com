var oFm = document.im_set;

oFm.upload_key.onchange = function(){
	var oRequestUrl = document.getElementById('request-url').value;
	var xhr = new XMLHttpRequest();
	xhr.onload = function(){
		oFm.im_key.value = JSON.parse(this.responseText);
	}
	xhr.open('post',oRequestUrl,true);
	xhr.setRequestHeader('X-Requset-With','XMLHttpRequest');
	var oFormData = new FormData();
	oFormData.append('myFile',oFm.upload_key.files[0]);
	oFormData.append('checkAjaxUploadKey','1');
	xhr.send(oFormData);
}

oFm.upload_tool.onchange = function(){
	var oRequestUrl = document.getElementById('request-url').value;
	var xhr = new XMLHttpRequest();
	xhr.onload = function(){
		var d = JSON.parse(this.responseText); 
		if(d == 1){
			alert('上传成功！');
		}else{
			alert('上传失败！');
		}
	}
	xhr.open('post',oRequestUrl,true);
	xhr.setRequestHeader('X-Requset-With','XMLHttpRequest');
	var oFormData = new FormData();
	oFormData.append('myFile',oFm.upload_tool.files[0]);
	oFormData.append('checkAjaxUploadTool','1');
	xhr.send(oFormData);
}

oFm.onsubmit = function(){
	
	if(oFm.im_appid.value == ''){
		alert('应用appid不能为空！');
		return false;
	}
	
	if(oFm.im_account.value == ''){
		alert('管理账户名不能为空！');
		return false;
	}
	
	if(oFm.im_key.value == ''){
		alert('请上传云通信私钥！');
		return false;
	}
	
	return true;
}

