//sdk登录
function webimLogin() {
    webim.login(
        loginInfo, listeners, options,
        function (resp) {
            loginInfo.identifierNick = resp.identifierNick;//设置当前用户昵称
            loginInfo.headurl = resp.headurl;//设置当前用户头像
        },
        function (err) {
            alert(err.ErrorInfo);
        }
    );
}