aNoHref();
aInput();


//阻止网页中所有a跳转
function aNoHref(){
	var aA = document.getElementsByTagName('a'); 
	for(var i=0;i<aA.length;i++){
		aA[i].addEventListener('tap',function(ev){
			ev.preventDefault();
			window.location.href = this.href;
			return false;
		});
	}
}


//重新获取a链接
function louieAHref(a){
	for(var i=0;i<a.length;i++){
		a[i].addEventListener('tap',function(){
			window.location.href = this.href;
		});
		a[i].style.display = 'block';
		a[i].style.height = '100%';
	}
}

function aInput(){
	var aIpt = document.querySelectorAll('input[type=text],textarea,input[type=number],input[type=search],select');
	var aIptFile = document.querySelectorAll('input[type=file]');
	for(var i=0;i<aIpt.length;i++){
		aIpt[i].addEventListener('touchstart',function(ev){  
			ev.stopPropagation();         
		});
	}
	for(var i=0;i<aIptFile.length;i++){
		aIptFile[i].addEventListener('tap',function(){
			this.click();   
		});
	}
}









