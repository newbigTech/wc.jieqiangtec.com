var aDelete = $('.louie-delete');
var nowPage = $('#now-page')[0].value; 

for(var i=0;i<aDelete.length;i++){
	aDelete[i].addEventListener('click',function(){
		if(confirm('您确定要删除该数据吗？')){
			var xhr = new XMLHttpRequest();
			var This = this;
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					var d = xhr.responseText;
					if(d == 1){
						console.log(This.parentNode.parentNode.parentNode.removeChild(This.parentNode.parentNode));
					}else{
						alert('删除失败！');  
					}
				}
			}
			
			xhr.open('post',nowPage,true);
			xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xhr.send('doing=deleteMsg&deleteid='+this.getAttribute('data-msg-id'));
		}
	});
}