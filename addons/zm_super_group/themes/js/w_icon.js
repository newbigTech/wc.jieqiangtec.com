var init = $('.louie-switch')[0].getAttribute('data-status');
var nowPage = document.getElementById('now-page').value;


if(init == 'on'){
	$('.louie-switch')[0].style.borderColor = '#007AFF';
	$('.louie-switch-bg')[0].style.left = '0'; 
}else{
	$('.louie-switch')[0].style.borderColor = '#ccc';
	$('.louie-switch-bg')[0].style.left = '-34.5px';
}


$('.louie-switch-btn')[0].addEventListener('click',function(){    
	$('.louie-switch-bg')[0].style.transition = $('.louie-switch-bg')[0].style.MozTransition = $('.louie-switch-bg')[0].style.WebkitTransition = $('.louie-switch-bg')[0].style.OTransition = '.2s';
	var xhr = new XMLHttpRequest();
	xhr.open('post',nowPage,true);
	xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xhr.send('doing=changeIcon&status='+init);
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			var d = xhr.responseText;
			if(d == 1){
				if(init == 'off'){
					init = 'on';
					$('.louie-switch')[0].style.borderColor = '#007AFF';
					$('.louie-switch-bg')[0].style.left = '0'; 	
				}else{
					init = 'off';
					$('.louie-switch')[0].style.borderColor = '#ccc';
					$('.louie-switch-bg')[0].style.left = '-34.5px';
				}
			}
		}
	}
});