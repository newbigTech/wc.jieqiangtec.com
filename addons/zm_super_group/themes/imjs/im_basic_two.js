/**
 * 唤出用户控制面板
 */
function callUserControl(){
	var aUserHeader = mui('.other .header');
	for(var i=0;i<aUserHeader.length;i++){
		aUserHeader[i].addEventListener('tap',function(){
			mui('#user-control')[0].setAttribute('data-userid',this.parentNode.getAttribute('data-userid'));
			mui('#user-control')[0].setAttribute('data-nickname',this.parentNode.children[1].childNodes[0].textContent);
			mui('#user-control').popover('show'); 
		}); 
	}
}

/**
 * 艾特群友前
 */
function eith(){
	if(mui('#eith-option')[0]){
		mui('#eith-option')[0].addEventListener('tap',function(){
			mui('#user-control').popover('hide'); 
			var objNickname = mui('#user-control')[0].getAttribute('data-nickname');
			var objUserid = mui('#user-control')[0].getAttribute('data-userid');
			mui('.eith-status-text')[0].innerHTML = '@'+ objNickname;
			mui('#eith-status')[0].style.display = 'block';
			mui('#text-border')[0].setAttribute('data-userid',objUserid); 
		})
	}
	
	mui.init({
		gestureConfig:{
			   longtap: true,
			  }
	});
	
	mui('#eith-status')[0].addEventListener('longtap',function(){
		closeEith();
	});
}

/**
 * 取消艾特 
 */
function closeEith(){ 
	mui('.eith-status-text')[0].innerHTML = '';
	mui('#text-border')[0].setAttribute('data-userid',0);
	mui('#eith-status')[0].style.display = 'none';
}

/**
 * 当前用户被艾特记录
 */
function nowUserEith(){
	var start = 0;
	var end = 1;
	ajaxRequest(); 
	function ajaxRequest(){
			mui.post(nowPage,{
				doing:'findNoReadyEith',
				groupId:selToID,
				userId:nowUser,
				start:start,
				end:end
			},function(data){
				if(data != 0){
					mui('.eith-box-header')[0].src = data.header;
					mui('.eith-box-text')[0].innerHTML = '@你：' + data.content;
					mui('#eith-box')[0].style.display = 'block';
				}else{
					mui('#eith-box')[0].style.display = 'none'; 
				}
			},'json'
		);
	}
	
	mui('.eith-icon')[0].addEventListener('tap',ajaxRequest); 
}




