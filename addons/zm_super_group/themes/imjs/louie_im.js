
var imInfo = JSON.parse(document.querySelector('#im_info').innerHTML);
var loginInfo = {
		'sdkAppID': imInfo.im_appid, //用户所属应用id,必填
        'accountType': imInfo.im_accountType, //用户所属应用帐号类型，必填
        'identifier': imInfo.identifier, //当前用户ID,必须是否字符串类型，必填
        'userSig': imInfo.userSig, //当前用户身份凭证，必须是字符串类型，必填
        'identifierNick': null, //当前用户昵称，不用填写，登录接口会返回用户的昵称，如果没有设置，则返回用户的id
        'headurl': null //当前用户默认头像，选填，如果设置过头像，则可以通过拉取个人资料接口来得到头像信息
}
 


var selType = webim.SESSION_TYPE.GROUP; //当前聊天类型 
var selToID = imInfo.groupId; //当前选中聊天id（当聊天类型为私聊时，该值为好友帐号，否则为群号）
var nowPage = document.querySelector('#now-page').value; 
var nowUser = document.querySelector('#now-user').value;
var nowNickname = document.querySelector('#now-nickname').value;
var nowHeader = document.querySelector('#now-header').value;
var imgDir = document.querySelector('#img-dir').value;  
var faceDir = document.querySelector('#face-dir').value; 
var groupPerson = document.body.getAttribute('data-person');  //当前社群人数
var newWidth,newHeight = null;   //发送图片时的新宽度，和新高度
var nowPlayVoice = null;  //当前播放语音的元素 
var nowVoiceWidth = document.querySelector('.voice .content');  
var roomType = document.body.getAttribute('data-room-type');

//监听连接状态回调变化事件
//var onConnNotify = function(resp) {
//    var info;
//    switch (resp.ErrorCode) {
//        case webim.CONNECTION_STATUS.ON:
//            webim.Log.warn('建立连接成功: ' + resp.ErrorInfo);
//            break;
//        case webim.CONNECTION_STATUS.OFF:
//            info = '连接已断开，无法收到新消息，请检查下你的网络是否正常: ' + resp.ErrorInfo;
//            // alert(info);
//            webim.Log.warn(info);
//            break;
//        case webim.CONNECTION_STATUS.RECONNECT:
//            info = '连接状态恢复正常: ' + resp.ErrorInfo;
//            // alert(info);
//            webim.Log.warn(info);
//            break;
//        default:
//            webim.Log.error('未知连接状态: =' + resp.ErrorInfo);
//            break;
//    }
//};


//监听连接状态回调变化事件
var onConnNotify = function(resp) {
    var info;
    switch (resp.ErrorCode) {
        case webim.CONNECTION_STATUS.ON:
            webim.Log.warn('建立连接成功: ' + resp.ErrorInfo);
            break;
        case webim.CONNECTION_STATUS.OFF:
            info = '连接已断开，无法收到新消息，请检查下你的网络是否正常: ' + resp.ErrorInfo;
            // alert(info);
            webim.Log.warn(info);
            break;
        case webim.CONNECTION_STATUS.RECONNECT:
            info = '连接状态恢复正常: ' + resp.ErrorInfo;
            // alert(info);
            webim.Log.warn(info);
            break;
        default:
            webim.Log.error('未知连接状态: =' + resp.ErrorInfo);
            break;
    }
};

//IE9(含)以下浏览器用到的jsonp回调函数
function jsonpCallback(rspData) {
    webim.setJsonpLastRspData(rspData); 
}

//监听事件
var listeners = {
    "onConnNotify": onConnNotify //监听连接状态回调变化事件,必填
        , 
    "jsonpCallback": jsonpCallback //IE9(含)以下浏览器用到的jsonp回调函数， 
        , 
    "onMsgNotify": OnMsgNotify //监听新消息(私聊，普通群(非直播聊天室)消息，全员推送消息)事件，必填
};  

//初始化时，其他对象，选填
var options = {
    'isAccessFormalEnv': true, //是否访问正式环境，默认访问正式，选填
    'isLogOn': false //是否开启控制台打印日志,默认开启，选填
}

 

//用户登录
webimLogin(); 

//初始化历史消息&下拉加载更多历史消息 
Lhistroy(); 
 
//发送文本消息
LsendTextMsg(); 

//显示隐藏图片、表情、红包按钮
showHiddenPic(); 

//显示隐藏语音
showHiddenRecode();

//发送语音
sendVoice();


//关注社群
attention();


//S发送各种类型消息
var aSendDoingBtn = document.querySelectorAll('#pic-face-red figure');
aSendDoingBtn[0].addEventListener('tap',function(){
	selectFace(this);
});

aSendDoingBtn[1].addEventListener('tap',function(){
	checkBannedTalk();
	closeEith();
	sendPic(this);
});

//发送红包
aSendDoingBtn[2].addEventListener('tap',function(){
	var sendRed = new SendRed(); 
	closeEith();
	sendRed.init();
});
//E发送各种类型

showGroupManage(); //点击显示社群控制面板

initTalk();  //初始禁言状态

openCloseBannerTalk();  //开启关闭全员禁言

eith(); //艾特群友

nowUserEith(); //当前用户是否有艾特消息 

//红包列表页面滚动
mui('#red-list-page').scroll({
	deceleration: 0.0006,
	indicators:false,
});

//点击私聊创建私聊页面
if(mui('#private-chat')[0]){
	mui('#private-chat')[0].addEventListener('tap',function(){
		var pc = new PrivateChat();
		pc.roomGoPrivate(nowUser,mui('#user-control')[0].getAttribute('data-userid'));
	});
}

mui('#chat')[0].addEventListener('touchstart',function(ev){
	ev.preventDefault();
});






















































