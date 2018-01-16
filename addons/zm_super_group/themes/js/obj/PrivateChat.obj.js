function PrivateChat(){
	
}

//从社群聊天页面跳转至私聊页面
PrivateChat.prototype.roomGoPrivate = function(user1,user2){
	window.location.href = nowPage+'&chatType=private&user1='+user1+'&user2='+user2;
}