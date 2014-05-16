<?php
 //网站的根路径
define('ROOT_PATH',dirname(dirname(dirname(__FILE__))));
 //上传的版本库
define('VERSION_BASE',"/resources/version/");
 //上传的版本库
define('VERSION_PATH',ROOT_PATH.VERSION_BASE);
//升级的版本库
define('UPGRADE_BASE',"/resources/upgrade/");
//升级的版本库
define('UPGRADE_PATH',ROOT_PATH.UPGRADE_BASE);
//解压的临时目录
define('EXTRACT_BASE',"/resources/temp/");
//解压的临时目录
define('EXTRACT_PATH',ROOT_PATH.EXTRACT_BASE);

