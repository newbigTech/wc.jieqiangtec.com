document.getElementById("group-share-status").addEventListener("toggle",function(event){
	var status = event.detail.isActive;
	status = status ? 'true' : 'false';
	console.log(status);
	mui.post(document.body.getAttribute('data-url'),{
		doing:'changeShareStatus',
		status:status
	},function(data){
		console.log(data); 
	},'text'
);
})

mui('#send-set')[0].addEventListener('tap',function(){
	if(mui('#award-ratio')[0].value == ''){
		mui.alert('奖励百分比不能为空！'); 
		return false; 
	}  
	mui.post(document.body.getAttribute('data-url'),{
		doing:'setAwardRatio',
		ratio:mui('#award-ratio')[0].value
	},function(data){
		if(data == 1){
			mui.alert('设置成功！');
		}else{
			mui.alert('设置失败！');
		}
	},'text')
});