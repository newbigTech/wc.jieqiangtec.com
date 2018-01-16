mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false,
});

var systemImg = document.querySelector('#system-img').value;

LUploading(); 
function LUploading(){
	var nowPage = document.querySelector('#now-page').value;
	var start = 0;
	var end = 10;
	mui.init({
		  pullRefresh : {
		    container:mui('.mui-content'),
		    up : {
		      height:50,
		      auto:true,
		      contentrefresh : "正在加载...",
		      contentnomore:'没有更多数据了',
		      callback :upLoading 
		    }
		  }
	});

	function upLoading(){
		var This = this;
		mui.post(nowPage,{
			doing:'findBought',
			start:start,
			end:end
		},function(data){
			if(data != 0){
				mui('#bought-list')[0].innerHTML += recordHTML(data); 
				This.endPullupToRefresh(false);
				start += 10; 
				louieAHref(mui('#bought-list a'));
			}else{
				This.endPullupToRefresh(true);
				if(start == 0){
					mui('.mui-pull-caption-nomore')[0].innerHTML = '';	
					mui('.mui-scroll')[0].innerHTML = '<img src="'+systemImg+'wu.png" style="display:block;width:3rem;height:3rem;margin:5rem auto 0 auto;" />';
					mui('.mui-scroll')[0].innerHTML += '<h2 style="font-size:.6rem;color:#888888;font-weight:normal;text-align:center;line-height:1rem;">暂无购买记录</h2>';
				   }
			}
		},'json'
	);
	}

	function recordHTML(data){
		var html = '';
		data.forEach(function(item){
			html += `<dl>
						<a href="${item.url}">
				    	<dt>
							<img src="${item.header}" />
							<h2 class="title">${item.title}</h2>
							<h2 class="name">${item.name}</h2>
						</dt> 
						<dd>
							<h3 class="price">实付：<span>￥${item.price}</span></h3>
							<h3 class="time">购买时间：${item.time}</h3>
						</dd>
						</a>
					</dl>`;
		});
		return html;
	}
}