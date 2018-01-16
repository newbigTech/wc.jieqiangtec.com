mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false,
});


var oFm = document.deposit;
var sendFormBtn = document.querySelector('nav');
var nowPage = document.querySelector('#now-page').value;
var charge = document.querySelector('#deposit-charge').value;
var limit = document.querySelector('#deposit-limit').value;

sendFormBtn.addEventListener('tap',function(){
	
	
	if(!/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(oFm.money.value) || oFm.money.value == '0'){
		mui.alert('请填写正确的提现金额');
		return false;
	}
	
	if(parseFloat(oFm.await.value) < parseFloat(limit)){
		mui.alert('提现金额满'+limit+'元后才可以提现！');
		return false; 
	}  
	
	if(oFm.money.value - (oFm.money.value*(charge / 100)) < 1){
		mui.alert('提现金额必须大于1元！'); 
		return false;
	} 
	
	if(parseFloat(oFm.money.value) > parseFloat(oFm.await.value)){
		mui.alert('余额不足！');   
		return false;
	}
	
	
	mui.post(nowPage,{
				doing:'deposit',
				depositMoney:oFm.money.value  
			},function(data){
				if(data.status == 1){
					mui.alert('申请提现成功，请耐心等待处理结果！','提示','确定',function(){
						window.location.reload(); 
					});
				}
				if(data.status == 0){
					mui.alert('余额不足！'); 
				}
			},'json'
			); 
});
