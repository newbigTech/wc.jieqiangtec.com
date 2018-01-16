var nowPage = document.getElementById('now-page').value;
groupInfo();
changeGroupStatus();





/**
 * 审核社群
 * @returns
 */
function changeGroupStatus(){
	var aPass = $('.pass');
	var aDown = $('.down');
	if(aPass){
		for(var i=0;i<aPass.length;i++){
			aPass[i].addEventListener('click',function(){
				var This = this;
				checkGroup(This,'pass');
			});
			
			aDown[i].addEventListener('click',function(){
				var This = this;
				checkGroup(This,'down');
			});
		}
	}
	
	
	/**
	 * 审核社群
	 * @param This
	 * @param result
	 * @returns
	 */
	function checkGroup(This,result){
		var xhr =new XMLHttpRequest();
		xhr.open('post',nowPage,true);
		xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xhr.send('doing=checkGroup&result='+result+'&groupid='+This.getAttribute('data-id'));
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				var data = xhr.responseText;
				if(data == 1){
					This.parentNode.parentNode.parentNode.removeChild(This.parentNode.parentNode);
				}
			}
		}
	}
}



/**
 * 查看一条社群数据
 * @returns
 */
function groupInfo(){
	var systemImgUrl = document.getElementById('system-img-url').value;
	var groupInfoContent = document.getElementById('group-info-content');
	var aGroupInfo = $('.group-info');
	if(aGroupInfo){
		for(var i=0;i<aGroupInfo.length;i++){
			aGroupInfo[i].addEventListener('click',function(){
				var xhr = new XMLHttpRequest();
				xhr.open('post',nowPage,true);
				xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
				xhr.send('doing=findOneGroup&groupid='+this.getAttribute('data-id'));
				xhr.onreadystatechange = function(){
					if(xhr.readyState == 4){
						groupInfoContent.innerHTML = oneGroupHTML(JSON.parse(xhr.responseText));
					}
				}
			});
		}
	}
	
	function oneGroupHTML(data){
		var html = `<dl>
						<dt>标题</dt>
						<dd>${data.title}</dd>
						<dt>名称</dt>
						<dd>${data.name}</dd>
						<dt>头像</dt>
						<dd><img src="${data.img_dir}${data.header}" class="group-header" /></dd>
						<dt>封面</dt>
						<dd><img src="${data.img_dir}${data.cover}" class="group-cover" /></dd>
						<dt>类别</dt>
						<dd>${data.className}</dd>
						<dt>入群方式</dt>
						<dd>${data.str_enter}</dd>
						${data.str_price}
						<dt>简介</dt>
						<dd>${data.brief}</dd>
					</dl>`;
		    return html;
	}
}