upLoading();


/**
 * 上拉加载
 * @returns
 */
function upLoading(){
	var start = 8;
	var end = 8;
	var nowPage = document.getElementById('now-page').value;
	var groupUrl = document.getElementById('group-url').value;

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
					doing:'ajaxFindBrowseRecord',
					start:start,
					end:end,
					requestType : 'ajax'
				},function(data){
					if(data == 0){
						This.endPullupToRefresh(true);
					}else{
						mui('#browse-list')[0].innerHTML += recordHTML(data);
						This.endPullupToRefresh(false);
						start += 8;
						louieAHref(mui('.mui-content a')); 
					}	
				},'json'
			);
	}

	function recordHTML(data){
		var html = '';
		data.forEach(function(item){
			html += `<figure>
						<a href="${groupUrl}&groupid=${item.id}"> 
						<img src="${item.header}" />
						<figcaption class="title">${item.title}</figcaption>
						<figcaption class="name">${item.name}</figcaption>
						</a>
					</figure>
					<div class="spacing-line"></div>`;   
		});
		return html;
	}
}




