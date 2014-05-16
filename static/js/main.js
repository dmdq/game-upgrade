/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */


function logout(){
	$.get("server/php/api.php",{action:"logout"},function(data){
		data = eval("("+data+")");
		if(data.code==1){
			location.href = "login.html";
		}
	});
}
/**
 * 初始化url参数
 */
function init_url(){
	var url = window.location.href;
	var index = url.indexOf("#");
	if(-1!=index){
		var paramesMap = url.substring(index+1).split("&");
		var args = {};
		for(var parameIndex in paramesMap){
			
			var parames = paramesMap[parameIndex];
			
			var parame =  parames.split("=");
			
			if(parame.length > 0){
				args[parame[0]] = parame.length==2?parame[1]:"";
			}
			if(args["app"]){
				var app = args["app"];
				$("#app").val(app);
				$("#app").css("display","inline")
			}
			
		}
	}	
}

$(function () {
//	init_url();
	//输入框默认事件
//	$('input:text').each(function(){
//		var txt = $(this).val();
//		$(this).focus(function(){
//			if(txt === $(this).val()) $(this).val("");
//		}).blur(function(){
//			if($(this).val() == "") $(this).val(txt);
//		});
//	});	
    'use strict';
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: './server/php/'
    });
    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    // Demo settings:
    $('#fileupload').fileupload('option', {
    	url: './server/php/index.php',
//        url: './server/php/index.php?platform='+$("#platform_list").val()+"&version="+$("#version_list").val(),
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        maxFileSize: 5000000 * 1024,
//        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|zip)$/i
        acceptFileTypes: /(\.|\/)(zip)$/i
    });
    
    load_exists_dirs();
    
    if (window.location.hostname === 'blueimp.github.io') {
      
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: 'http://jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
    	load_exists_files();
    }

});


function select_dir(){
	var dir = $("#dirs").val();
	$("#app_dir").val(dir);
}

function load_exists_dirs(){
	$("#dirs").css("display","none");
	
	 $('#fileupload').addClass('fileupload-processing');
	    $.ajax({
	        // Uncomment the following to send cross-domain cookies:
	        //xhrFields: {withCredentials: true},
	        url: $('#fileupload').fileupload('option', 'url')+"?dirs=dirs"+"&user="+$.trim($("#user").val()),
	        dataType: 'json',
	        context: $('#fileupload')[0]
	    }).always(function () {
	        $(this).removeClass('fileupload-processing');
	        
	    }).done(function (dirs) {
	    	if(dirs.length>0){
	    		$("#dirs").css("display","inline");
	    		var options ;
	    	 	for(i in dirs){ 
	    	 		var dir = dirs[i];
	    	 		options += "<option>"+dir+"</option>"
		    	} 
	    	 	$("#dirs").html(options);
	    	}
	    });	
}

function load_exists_files(){
    // Load existing files:
	$("table .files").html("");
	var app_dir = $.trim($("#app_dir").val());
	if(app_dir==""){
		return;
	}
	$("#app").val(app_dir);
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url')+"?app="+app_dir+"&user="+$.trim($("#user").val()),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
        load_exists_dirs();
    });
}



