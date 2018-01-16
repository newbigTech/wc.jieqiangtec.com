mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false,
});

LUploading(); 
function LUploading(){
	var nowPage = document.querySelector('#now-page').value;
	var start = 0;
	var end = 20;
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
			doing:'findDealRecord',
			start:start,
			end:end
		},function(data){
			if(data){
				mui('#deal-list')[0].innerHTML += recordHTML(data);
				This.endPullupToRefresh(false);
				start += 20; 
			}else{
				This.endPullupToRefresh(true);
			}
		},'json'
	);
	}

	function recordHTML(data){
		var html = '';
		data.forEach(function(item){
			html += `<li><span class="time">${item.time}</span><span class="money">${item.price}</span></li>`;
		});
		return html;
	}
}
