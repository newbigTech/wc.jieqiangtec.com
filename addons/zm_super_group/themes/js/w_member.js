var oFm = document.search_form;
var nowPage = $('#now-page')[0].value;
oFm.send_search.onclick = function(){
	if(oFm.keyword.value == ''){
		return false;
	}
	window.location.href = nowPage + '&search=1&keyword='+oFm.keyword.value;
}