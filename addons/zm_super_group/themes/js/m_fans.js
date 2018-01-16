upLoading();
mui('.mui-scroll-wrapper').scroll({
	deceleration: 0.0003,
	 indicators: false,
});


/**
 * 上拉加载
 * @returns
 */
function upLoading(){
	var start = 15;
	var end = 15;
	var nowPage = document.getElementById('now-page').value;

	mui.init({
		  pullRefresh : {
		    container:mui('.mui-content'),
		    up : {
		      height:50,
		      auto:false,
		      contentrefresh : "正在加载...",
		      contentnomore:'没有更多数据了',
		      callback : louie 
		    }
		  }
		});

	function louie(){
		var This = this;
		mui.post(nowPage,{
					doing:'ajaxFindFans',
					start:start,
					end:end,
					requestType:'ajax'
				},function(data){
					if(data == 0){
						This.endPullupToRefresh(true);
					}else{
						mui('#fans-list')[0].innerHTML += recordHTML(data);
						This.endPullupToRefresh(false);
						start += 15; 
					}	 
				},'json'
			);
	}

	function recordHTML(data){
		var html = '';
		data.forEach(function(item){
			html += `<figure>
					<img src="${item.header}" />
		    			<figcaption class="nickname">${item.nickname}</figcaption>
		    		</figure>`;
		});
		return html;
	}
}