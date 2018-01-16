mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false,
});


mui.init();
slideAuto(); 
hotScroll();
groupListScroll();
headerNavScroll(); 
clickSearchPage();



/**
 * [headerNav 头部滚动导航]
 * @return {[type]} [description]
 */
function headerNavScroll(){
	mui('#header-scroll').scroll({
	 scrollY: false, //是否竖向滚动 
	 scrollX: true, //是否横向滚动 
	 startX: 0, //初始化时滚动至x
	 startY: 0, //初始化时滚动至y
	 indicators: false, //是否显示滚动条
	 deceleration:0.0008, //阻尼系数,系数越小滑动越灵敏
	 bounce: true //是否启用回弹
	});
	
	var aNav = document.querySelectorAll('#header-scroll nav a');
	
	
	for(var i=0;i<aNav.length;i++){	
		aNav[i].addEventListener('tap',function(ev){
			for(var j=0;j<aNav.length;j++){
				aNav[j].className = '';
			}
			this.className = 'active';	 
		});
	}		
}



/**
 * 首页幻灯自动播放
 */
function slideAuto(){
	var gallery = mui('.mui-slider');
	gallery.slider({
	  interval:3000//自动轮播周期，若为0则不自动播放，默认为0；
	});
}


/**
 * 热门社群滚动
 */
function hotScroll(){ 
	mui('#hot-scroll').scroll({
	 scrollY: false, //是否竖向滚动
	 scrollX: true, //是否横向滚动
	 startX: 0, //初始化时滚动至x
	 startY: 0, //初始化时滚动至y
	 indicators: false, //是否显示滚动条
	 deceleration:0.0009, //阻尼系数,系数越小滑动越灵敏
	 bounce: true //是否启用回弹
	});
}


/**
 * 社群分类列表，滚动+上拉刷新  
 */
function groupListScroll(){ 
	//document.querySelector('#group-list').style.height = (window.screen.height - document.querySelector('nav.mui-bar-tab').offsetHeight) + 'px';   
	var scroll = mui('#group-list').scroll({
		 scrollY: true, 
		 scrollX: false, 
		 startX: 0, 
		 startY: 0, 
		 indicators: true, 
		 deceleration:0.1, 
		 bounce: true  
		});
	
	mui('#group-list')[0].start = 8;
	mui('#group-list')[0].end = 8;
	mui('#group-list')[0].addEventListener('scroll',function(){
		if((mui('#group-list .group-list')[0].scrollHeight - this.offsetHeight) == Math.abs(scroll.y)){
			if(this.uoLoading){
				if(document.querySelector('head style') == null){
					mui('#group-list')[0].oStyle = document.createElement('style');
				}else{
					mui('#group-list')[0].oStyle = document.querySelector('head style');   
				}
				mui('#group-list')[0].oCss = "#group-list .bottom-space:last-child:before{content:'正在加载中...';text-indent:0;background-image:none;}";
				mui('#group-list')[0].oStyle.innerHTML = mui('#group-list')[0].oCss;
				document.head.appendChild(mui('#group-list')[0].oStyle); 
				loadingList(this.start,this.end); 
				this.start += 8;
			}
		}    
	});
}



var aHeaderNav = mui('#header-scroll nav a');
var oGroupList = mui('#group-list')[0];
var nowPgae =mui('#now-page')[0].value;

for(var i=0;i<aHeaderNav.length;i++){
	aHeaderNav[i].addEventListener('tap',function(){
		mui('#group-list')[0].start = 8;
		if(mui('#group-list')[0].oStyle){
			document.querySelector('head style').innerHTML = '';
		}
		if(oGroupList.getAttribute('data-list-id') == this.getAttribute('data-page')){
			return; 
		}
		oGroupList.uoLoading = false;
		mui('#group-list .group-list')[0].innerHTML = '<div class="loadingState">正在加载中...</div> ';
		
		if(this.getAttribute('data-page') != 'index'){
			mui('#group-list').scroll().scrollTo(0,0,30);    
			oGroupList.setAttribute('data-list-id',this.getAttribute('data-page'));
			oGroupList.style.display = 'block';  	
			var xhr = new XMLHttpRequest();
			xhr.open('post',nowPgae,true);
			xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xhr.send('doing=findGroupList&start=0&end=8&classid='+this.getAttribute('data-page')); 
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



function groupListHTML(data){ 
	var html = '<div class="top-space"></div>'; 
	data.forEach(function(item){
		html += `<figure>
				<a href="${item.href}">
				<img src="${item.img_dir}${item.header}" />
					<figcaption class="title">${item.title}</figcaption>
					<figcaption class="brief">${item.name}</figcaption>
					<figcaption class="count-price">
						<span class="count">${item.browse_count}人浏览<span>  
						<span class="price">${item.str_price}</span>  
					</figcaption>
				</a>
				</figure>`;
	});
	html += '<div class="bottom-space"></div>';
	return html;
}


function loadingList(start,end){
	var nowPage = mui('#now-page')[0].value;
	var xhr = new XMLHttpRequest();
	xhr.open('post',nowPage,true); 
	xhr.setRequestHeader('content-type','application/x-www-form-urlencoded'); 
	var requestStr = oGroupList.getAttribute('data-list-id');
	if(requestStr == 'free' || requestStr == 'lowPrice' || requestStr == 'ranking' || requestStr == 'boutique'){
		xhr.send('doing=findGroupList&start='+start+'&end='+end+'&classstr='+oGroupList.getAttribute('data-list-id'));
	}else{
		xhr.send('doing=findGroupList&start='+start+'&end='+end+'&classid='+oGroupList.getAttribute('data-list-id'));
	}
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4){
			var d = JSON.parse(xhr.responseText);
			if(d == 0){
				if(document.querySelector('head style') == null){
					mui('#group-list')[0].oStyle = document.createElement('style');
				}else{
					mui('#group-list')[0].oStyle = document.querySelector('head style');   
				}
				mui('#group-list')[0].oCss = "#group-list .bottom-space:last-child:before{content:'没有更多社群了';text-indent:0;background-image:none;}";  
				mui('#group-list')[0].oStyle.innerHTML = mui('#group-list')[0].oCss; 
				document.head.appendChild(mui('#group-list')[0].oStyle);    
			}else{
				mui('#group-list .group-list')[0].innerHTML += groupListHTML(d);
				louieAHref(mui('#group-list .group-list a'));
			}
		}
	}
}



/**
 * 点击链接到搜索页面
 */
function clickSearchPage(){
	var oSearch = document.querySelector('#search');
	oSearch.addEventListener('tap',function(){
		window.location.href = this.getAttribute('data-url'); 
	});
}

