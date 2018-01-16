var LouieScroll = mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.005,
	 indicators: true,
});



focusText(); 



/**
 * 选中文本框
 */
function focusText(){
	var oTextBorder = document.querySelector('#text-border');
	var oMuiContentH = document.body.scrollHeight;
	
	oTextBorder.addEventListener('focus',function(ev){
		myTimer = setTimeout(function(){ 
			document.body.scrollTop = document.body.scrollHeight;        
		},300);
		this.nextElementSibling.style.display = 'block'; 
	}); 
	oTextBorder.addEventListener('blur',function(){  
		 document.body.scrollTop = oMuiContentH;
		 clearTimeout(myTimer); 
		if(this.value != ''){
			this.nextElementSibling.style.display = 'block';
			this.style.cssText = 'width:8rem;';  
		}else{
			this.style.cssText = '10.333333rem;'; 
			this.nextElementSibling.style.display = 'none';
		}
	});
}



  






	 

