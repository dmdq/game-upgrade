$(function () {
	var version_login_user = $.cookie('version_login_user');
	var version_login_password = $.cookie('version_login_password');
	if(version_login_user){
		$("#user").val(version_login_user);
	}
	if(version_login_password){
		$("#password").val(version_login_password);
	}
});

function msgbox($msg){
	alert($msg);
}


function user(){
	var user = $.trim($("#user").val());
	var password = $.trim($("#password").val());
	if(user==""){
		msgbox("邮箱不能为空");
		return false;
	}
	var isEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(user);
	
	if (isEmail == false){
		msgbox("邮箱格式不正确");
		return false;
	}
	if(password==""){
		msgbox("密码不能为空");
		return false;
	}
	return {
		user	:	user,
		password	:	password
	};
}

function msg_code(code){
	if(code==0){
		msgbox("失败");
	}else if(-101==code){
		msgbox("已经存在该用户");
	}else if(-102==code){
		msgbox("密码错误");
	}else if(-103==code){
		msgbox("用户不存在");
	}else if(-104==code){
		msgbox("数据异常");
	}
	return code;
	
}
function login(){
	var user_data = user();
	if(user_data){
		user_data.action = "login";
		if($("#c2").attr("checked")){
			$.cookie('version_login_user', user_data.user, { expires: 7, path: '/' });
			$.cookie('version_login_password', user_data.password, { expires: 7, path: '/' });
		}else{
			$.cookie('version_login_user', null,{ path: '/' });
			$.cookie('version_login_password', null,{ path: '/' });
		}
		$.get('server/php/api.php',user_data,function(data){
			console.log(data);
			data = eval("("+data+")");
			if(data.code==1){
				location.href = "./index.php";					
			}else{
				msg_code(data.code);
			}
		});
	}
}

function register(){
	var user_data = user();
	if(user_data){
		user_data.action = "register";
		$.get('server/php/api.php',user_data,function(data){
			console.log(data);
			data = eval("("+data+")");
			if(data.code==1){
				location.href = "./index.php";					
			}else{
				msg_code(data.code);
			}
		});
	}
}