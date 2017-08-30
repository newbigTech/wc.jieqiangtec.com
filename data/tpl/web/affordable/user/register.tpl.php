<?php defined('IN_IA') or exit('Access Denied');?>﻿<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<script>
	$('#form1').submit(function(){
		if($.trim($(':text[name="username"]').val()) == '') {
			util.message('没有输入用户名.', '', 'error');
			return false;
		}
		if($('#password').val() == '') {
			util.message('没有输入密码.', '', 'error');
			return false;
		}
		if($('#password').val() != $('#repassword').val()) {
			util.message('两次输入的密码不一致.', '', 'error');
			return false;
		}
		<?php  if($_W['setting']['register']['code']) { ?>
		if($.trim($(':text[name="code"]').val()) == '') {
			util.message('没有输入验证码.', '', 'error');
			return false;
		}
		<?php  } ?>
	});
	var h = document.documentElement.clientHeight;
	$(".login").css('min-height',h);
</script>
<div class="head">
    <link href="./resource/affordable/css/popnews.css" rel="stylesheet" />
    <link href="./resource/affordable/css/iconfont.css" rel="stylesheet" />
    <link href="./resource/affordable/css/style.css" rel="stylesheet" />
    <script src="./resource/affordable/css/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<form  class="form-box" method="post" role="form">    <div class="new-bg"></div>
    <div class="xiaokeduo-login-container" style="top:40%">
        <div class="qr-code">
            <div class="qr-code-content">
                <h3 class="qr-code-title"><?php  echo $_W['setting']['copyright']['notice'];?></h3>
                <img class="qr-code-img" src="<?php  if(!empty($_W['setting']['copyright']['qrcode'])) { ?><?php  echo tomedia($_W['setting']['copyright']['qrcode'])?><?php  } else { ?><?php  } ?>" width="145" height="145" />
                <p class="text">扫一扫加入官方公众号立即体验</p>
            </div>
        </div>
        <div class="login-content">
           
		   <div class="logo text-center">
			<a href="javascript:void(0);"><img  style="width:235px;height:41px;" src="<?php  if(!empty($_W['setting']['copyright']['flogo2'])) { ?><?php  echo tomedia($_W['setting']['copyright']['flogo2'])?><?php  } else { ?>./resource\affordable\images/logo-new1.png<?php  } ?>" width="220px" height="50px"></a>
		</div>
		   
		   
            <div class="form-group">
				<form action="" class="we7-form" method="post" role="form" id="form1">
					<div class="form-group input-group">
						<span id="message" class="text-danger"></span>
					</div>
					<div class="form-group input-group">
						<div class="input-group-addon"><img src="./resource/images/icon-user.png" alt="" /></div>
						<input name="username" type="text" class="form-control" placeholder="请输入用户名">
					</div>
					<div class="form-group input-group">
						<div class="input-group-addon"><img src="./resource/images/icon-pass.png" alt="" /></div>
						<input name="password" type="password" id="password" class="form-control col-sm-10" placeholder="请输入不少于8位的密码">
					</div>
					<div class="form-group input-group">
						<div class="input-group-addon"><img src="./resource/images/icon-pass.png" alt="" /></div>
						<input name="password " type="password" id="repassword" class="form-control col-sm-10" placeholder="请再次输入不少于8位的密码">
					</div>	
			     	<?php  if($_W['setting']['register']['code']) { ?>
					<div class="form-group input-group">
					<div class="input-group-addon"><img src="./resource/images/icon-code.png" alt="" /></div>
						<input name="code" type="text" class="form-control" placeholder="请输入验证码">
						<a href="javascript:;" class="input-group-btn imgverify"><img src="<?php  echo url('utility/code');?>" class="img-rounded" onclick="this.src='<?php  echo url('utility/code');?>' + Math.random();" /></a>			
					</div>
					<?php  } ?>					
					<div class="login-submit text-center">
						<input type="submit" name="submit" value="注册" class="btn btn-primary" />
						<input name="token" value="<?php  echo $_W['token'];?>" type="hidden" />
					</div>
					</div>				
                        <div class="other-operations">
                        <p class="fr">我已有账号? <a href="<?php  echo url('user/login');?>">立即登录</a></p>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
    <div class="subfooter">
        <p><small class="text-muted"><font color="#FFFFFF" size="2.5"><?php  echo $_W['setting']['copyright']['footerright'];?></small></p>
        <p><small class="text-muted"><font color="#NaNNaNNaN"><?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by v<?php echo IMS_VERSION;?> &copy; 2014-2015 <?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?></small></p>
		<p><small class="text-muted"><font color="#NaNNaNNaN"><?php  echo $_W['setting']['copyright']['icp'];?></small></p>
    </div>
    </div>
</body>
</html>

