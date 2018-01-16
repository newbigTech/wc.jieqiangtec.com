mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false, 
});

var oFm = document.search;
var nowPage = document.body.getAttribute('data-now-page');

oFm.addEventListener('submit',function(ev){
	ev.preventDefault();
	if(oFm.keyword.value == ''){
		return false; 
	}
	mui.post(nowPage,{
		doing:'search',
		keyword:oFm.keyword.value 
	},function(data){
		if(data == 0){
			mui('.search-list')[0].innerHTML = '<h2 style="color:#666;text-align:center;">找不到搜索内容</h2>';
			oFm.keyword.blur();
		}else{
			mui('.search-list')[0].innerHTML = searchHTML(data);
			oFm.keyword.blur();
			louieAHref(mui('.mui-content a'));	
		}
	},'json'
	);
});

function searchHTML(data){
	var html = '';
	data.forEach(function(item){
		html += `<figure>
				<a href="${item.url}">
				<img src="${item.header}" />
					<figcaption class="title">${item.title}</figcaption>
					<figcaption class="brief">${item.name}</figcaption>
					<figcaption class="count-price">
						<span class="count">${item.browse_count}人浏览</span> 
						<span class="price">${item.enterType}</span>
					</figcaption>
				</a>
				</figure>`;
	});
	return html;
}