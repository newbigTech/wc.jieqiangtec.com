
var aA = document.querySelectorAll('#four-class a');
var oGroupList = mui('#group-list')[0];
var nowPage = mui('#now-page')[0].value;
for(var i=0;i<aA.length;i++){
	aA[i].addEventListener('tap',function(){
		mui('#group-list')[0].start = 8;
		mui('#group-list .group-list')[0].innerHTML = '<div class="loadingState">正在加载中...</div> ';
		
		if(this.getAttribute('data-page') != 'index'){
			oGroupList.setAttribute('data-list-id',this.getAttribute('data-page'));
			oGroupList.style.display = 'block';
			mui('#group-list').scroll().scrollTo(0,0,30);
			var xhr = new XMLHttpRequest();
			xhr.open('post',nowPgae,true);
			xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xhr.send('doing=findGroupList&start=0&end=8&classstr='+this.getAttribute('data-page')); 
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					var d = JSON.parse(xhr.responseText);
					if(d == 0){	
						mui('.loadingState')[0].style.textIndent = '0';
						mui('.loadingState')[0].style.backgroundImage = 'none';
						mui('.loadingState')[0].innerHTML = '没有更多社群了';  
					}else{ 
						mui('#group-list .group-list')[0].innerHTML = groupListHTML(d);
						oGroupList.uoLoading = true;
						louieAHref(mui('#group-list .group-list a'));
					}
				}   
			}
			
			
		}else{
			mui('.mui-content').scroll().scrollTo(0,0,30);    
			oGroupList.style.display = 'none';  
			oGroupList.setAttribute('data-list-id','index'); 
		}
	});
}





