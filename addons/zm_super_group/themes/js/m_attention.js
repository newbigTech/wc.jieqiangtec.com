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
			doing:'findAttentionGroup',  
			start:start,
			end:end
		},function(data){ 
			if(data.length > 0){
				mui('.attention-list')[0].innerHTML += recordHTML(data);
				This.endPullupToRefresh(false);   
				start += 20;  
				var cancelBtn = mui('.mui-btn-danger');
				for(var i=0;i<cancelBtn.length;i++){
					cancelBtn[i].addEventListener('tap',function(){
						var This = this;
						mui.post(nowPage,{
							doing:'cancelAttention',
							attentionId:This.getAttribute('data-id')
						},function(data){ 
							if(data == 1){
								This.parentNode.parentNode.removeChild(This.parentNode);
							}else{
								mui.alert('取消关注失败！');
							}
						},'text'
					);
					});
				}
			}else{ 
				This.endPullupToRefresh(true);
			}
		},'json'
	);
	}

	function recordHTML(data){
		var html = '';
		data.forEach(function(item){
			html += `<figure>
					<img src="${item.header}" />
						<figcaption class="title">${item.title}</figcaption>
						<figcaption class="name">${item.name}</figcaption> 
						<button class="mui-btn-danger" data-id="${item.id}">取消关注</button>
					</figure>
					<div class="spacing-line"></div>`;
		});
		return html;
	}
}