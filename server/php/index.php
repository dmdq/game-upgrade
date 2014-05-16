<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
@ require_once (dirname(__FILE__) . '/config.php');
@ require_once (dirname(__FILE__) . '/action.php');
error_reporting(E_ALL | E_STRICT);
$app ="default";
$user = "root";
if(!isset($_REQUEST["app"])){
//	return;
}else{
	//上传的应用名称
	$app = $_REQUEST["app"];
}
if(!isset($_REQUEST["user"])){
//	return;
}else{
	//上传的用户名
	$user = $_REQUEST["user"];
}
if(isset($_REQUEST["dirs"])){
	$parent_dir_path = VERSION_PATH.'/'.$user.'/';
	$dirs = array();
	if(is_dir($parent_dir_path)){
		$dir_list = scandir($parent_dir_path);
		foreach($dir_list as $dir){
			if($dir!="."&&$dir!=".."){
				array_push($dirs,$dir);
			}
		}		
	}
	echo json_encode($dirs);
	exit;
}

if (@ !file_exists(dirname(VERSION_PATH))) {
	@ mkdir(dirname(VERSION_PATH), 0755,true);
}
$version_path = VERSION_PATH.'/'.$user.'/'.$app.'/';
$upgrade_path = UPGRADE_PATH.'/'.$user.'/'.$app.'/';
$extract_path = EXTRACT_PATH.'/'.$user.'/'.$app.'/';

if (@ !file_exists($version_path)) {
	@ mkdir($version_path, 0755,true);
}
if (@ !file_exists($upgrade_path)) {
	@ mkdir($upgrade_path, 0755,true);
}
if (@ !file_exists($upgrade_path)) {
	@ mkdir($extract_path, 0755,true);
}
@ require_once ('UploadHandler.php');
$options = array (
	'upload_dir' => $version_path,
	'upload_url' => home_url() . VERSION_BASE."/$user/$app/",
	'app_name' => $app,
	'user_name' => $user
);
$upload_handler = new UploadHandler($options);