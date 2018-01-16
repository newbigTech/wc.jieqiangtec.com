LUploading(); 
function LUploading(){
	var nowPage = document.querySelector('#now-page').value; 
	var start = 0;
	var end = 10;
	mui.init({
		  pullRefresh : {
		    container:mui('.mui-content'), 
		    up : {
		      height:2000,
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
			doing:'findMeGroup', 
			start:start,
			end:end
		},function(data){
			if(data){
				mui('#list-loop')[0].innerHTML += recordHTML(data);  
				This.endPullupToRefresh(false);
				start += 10;  
			}else{
				This.endPullupToRefresh(true);
			}
			louieAHref(mui('.mui-scroll a'));
		},'json'
	);
	}

	function recordHTML(data){
		var html = '';
		data.forEach(function(item){  
			html += `<figure>
					<a href="${item.url}"> 
					<img src="${item.header}" />
						<figcaption class="title">${item.title}</figcaption>
						<figcaption class="brief">${item.name}</figcaption>
						<figcaption class="count-price">
							<span class="count">${item.browse_count}人浏览</span>
							<span class="price">${item.strStatus}</span>
						</figcaption>
					</a> 
					</figure>`; 
		});
		return html;
	}
}