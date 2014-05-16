<?php

 if(!isset($_COOKIE['game-version-user'])){
 	header("location:login.html");	
	 exit;
 }
?>
<!DOCTYPE HTML>
<!--
/*
 * jQuery File Upload Plugin Demo 9.0.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
-->
<html>
<head>
<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
<meta charset="utf-8">
<title>游戏版本升级</title>
<meta name="description" content="File Upload widget with multiple file selection, drag&amp;drop support, progress bars, validation and preview images, audio and video for jQuery. Supports cross-domain, chunked and resumable file uploads and client-side image resizing. Works with any server-side platform (PHP, Python, Ruby on Rails, Java, Node.js, Go etc.) that supports standard HTML form file uploads.">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap styles -->
<link rel="stylesheet" href="static/css/bootstrap.min.css">
<!-- Generic page styles -->
<link rel="stylesheet" href="static/css/style.css">
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="static/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="static/css/jquery.fileupload.css">
<link rel="stylesheet" href="static/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="static/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="static/css/jquery.fileupload-ui-noscript.css"></noscript>
<script type="text/javascript">
Date.prototype.format = function(format) {  
    /* 
     * eg:format="yyyy-MM-dd hh:mm:ss"; 
     */  
    var o = {  
        "M+" : this.getMonth() + 1, // month  
        "d+" : this.getDate(), // day  
        "h+" : this.getHours(), // hour  
        "m+" : this.getMinutes(), // minute  
        "s+" : this.getSeconds(), // second  
        "q+" : Math.floor((this.getMonth() + 3) / 3), // quarter  
        "S" : this.getMilliseconds()  
        // millisecond  
    };  
  
    if (/(y+)/.test(format)) {  
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4  
                        - RegExp.$1.length));  
    }  
  
    for (var k in o) {  
        if (new RegExp("(" + k + ")").test(format)) {  
            format = format.replace(RegExp.$1, RegExp.$1.length == 1  
                            ? o[k]  
                            : ("00" + o[k]).substr(("" + o[k]).length));  
        }  
    }  
    return format;  
};

	function msgbox($msg){
		alert($msg);
	}
	String.prototype.endWith=function(str){
		if(str==null||str==""||this.length==0||str.length>this.length)
		  return false;
		if(this.substring(this.length-str.length)==str)
		  return true;
		else
		  return false;
		return true;
		}
		String.prototype.startWith=function(str){
		if(str==null||str==""||this.length==0||str.length>this.length)
		  return false;
		if(this.substr(0,str.length)==str)
		  return true;
		else
		  return false;
		return true;
		}
		
		function build_url(){
			//输入的版本
			var inputVersion = $.trim($("#inputVersion").val());
			
			var user = $.trim($("#user").val());
			
			var app = $.trim($("#app").val());
			
			if(user==""){
				msgbox("用户异常");
				return;
			}
			
			if(app==""){
				msgbox("应用名称不能为空");
				return;
			}
			if(inputVersion==""){
				msgbox("输入的版本不能为空");			
				return;
			}
			var isZip = inputVersion.endWith(".zip");
			if(!isZip){
				msgbox("请输入以.zip结尾的版本号");
				return;
			}
			window.open("./server/php/api.php?version="+inputVersion+"&app="+app+"&user="+user);
		}
</script>
</head>
<body>

<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-fixed-top .navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="javascript:void">客户端资源上传</a>
          
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
            	<!-- 
                <li><a href="#">捐赠</a></li>
                -->
            </ul>
        </div>
    </div>
</div>


<div class="container">
    <!-- The file upload form used as target for the file upload widget -->
    	<div class="user_info">
        欢迎您:<span class="user_name"><?php echo $_COOKIE['game-version-user']?></span> 
        <a href="javascript:void(0);" onclick="logout();">注销</a>
        </div>
        <div class="panel panel-default">
        	<div class="panel-heading">
        		上传格式说明
        	</div>
        	<ol class="upload_notice"> 
	        	<li>上传的文件只允许.zip为后缀的有效压缩文件</li>
	        	<li>
	        		上传的文件命名规范为:平台-版本号.zip<br/>
	        		如:安卓下 apk-v1.0.0.zip 苹果为:ipa-v1.0.0.zip
	        	</li>
        	</ol>
        	<input type="text"  id="inputVersion" class="inputVersion" value="输入当前版本"/>
        	<button class="btn build_version" onclick="build_url();">生成地址</button>
        	<br/>
        	<br/>
        </div>
    <div class="choose_dir">
    		<select id="dirs" onchange="select_dir();">
    		
    		</select>
	        选择文件夹:<input type="text" id="app_dir" class="inputVersion" value="default">
	        <button class="btn load_exists_files" onclick="load_exists_files();">确定</button>
    </div>
    <form id="fileupload" method="POST" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="#"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                  <span class="">
                    <input type="hidden" name="user"  id="user" value="<?php echo $_COOKIE['game-version-user']?>" />
                    <input type="hidden" name="app"  id="app" value="default"/>
                </span>
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>添加文件</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>开始上传</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>取消上传</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>删除文件</span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped panel panel-default"><tbody class="files"></tbody></table>
    </form>
</div>
<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>开始</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>取消</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">错误</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <span class="date">{%=new Date(file.last*1000).format("yyyy-MM-dd hh:mm:ss")%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>删除</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>取消</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<script src="static/js/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="static/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="static/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="static/js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="static/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="static/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="static/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="static/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="static/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="static/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="static/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="static/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="static/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="static/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="static/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="static/js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="static/js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
</body> 
</html>
