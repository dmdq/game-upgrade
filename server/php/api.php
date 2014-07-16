<?
@require_once(dirname(__FILE__).'/config.php');
@require_once(dirname(__FILE__).'/action.php');
@require_once(dirname(__FILE__).'/FileUtil.php');
@require_once(dirname(__FILE__).'/pclzip.lib.php');

if(isset($_REQUEST["action"])){
	$action = $_REQUEST["action"];
	if($action=="register"){
		if(isset($_REQUEST["user"])&&isset($_REQUEST["password"])){
			$user = $_REQUEST["user"];
			$password = $_REQUEST["password"];
			$user_data_file = dirname(__FILE__).'/user.db';
			$flag = false;
			if(@!file_exists($user_data_file)){
				file_put_contents($user_data_file,json_encode(array($user=>array("user"=>$user,"password"=>base64_encode(strrev($password))))));
				$flag = true;
			}else{
				$user_file_content = file_get_contents($user_data_file);
				$user_data = json_decode($user_file_content);
				if(@$user_data->$user){
					echo '{code : -101}';//已经存在该用户
					return;
				}else{
					$flag = true;
					$user_data->$user = array("user"=>$user,"password"=>base64_encode(strrev($password)));
					file_put_contents($user_data_file,json_encode($user_data));
				}				
			}
			if($flag){
				$expire = time() + 3600; // 设置1小时的有效期
				setcookie ("game-version-user", $user, $expire,'/'); // 设置一个名字为var_name的cookie，并制定了有效期
				echo json_encode(array("code"=>1));
			}
			return;
		}
	}elseif($action=="login"){
		if(isset($_REQUEST["user"])&&isset($_REQUEST["password"])){
			$user_data_file = dirname(__FILE__).'/user.db';
			$code = 1;//成功
			if(file_exists($user_data_file)){
				$user = $_REQUEST["user"];
				$password = $_REQUEST["password"];
				$user_file_content = file_get_contents($user_data_file);
				$user_data = json_decode($user_file_content);
				if(@$user_data->$user){
					$_data = $user_data->$user;
					if(base64_encode(strrev($password))==$_data->password){
						$expire = time() + 3600; // 设置1小时的有效期
						setcookie ("game-version-user", $user, $expire,'/'); // 设置一个名字为var_name的cookie，并制定了有效期
						$code = 1;
					}else{
						//密码不正确
						$code = -102;
					}
				}else{
					//用户不存在
					$code = -103;
				}
			}else{
				//不存在数据库表
				$code = -104;
			}
			echo json_encode(array("code"=>$code));
			return;
		}
	}elseif("logout"==$action){
		setcookie("game-version-user","",time()-3600,'/');
		echo json_encode(array("code"=>1));
	}elseif("dropfile"==$action){
		if(isset($_REQUEST["filename"])){
			$filename = $_REQUEST["filename"];
			$file = ROOT_PATH.$filename;
			if(file_exists($file)){
				@unlink($file);
				echo 1;
			}else{
				echo 0;
			}
		}
	}
}

if(isset($_REQUEST["phpinfo"])){
	phpinfo();
	return;
}

if(isset($_REQUEST["user"])&&isset($_REQUEST["version"])&&isset($_REQUEST["app"])){
	//上传者的名称
	$user = $_REQUEST["user"];
	//当前客户应用名称
	$app = $_REQUEST["app"];
	//当前客户端资源版本
	$client_version = $_REQUEST["version"];
	//当前客户端资源索引文件
	$version_file = VERSION_PATH."$user/$app/version.json";
	
	if(!@file_exists($version_file)){
		//未发现客户端版本资源映射表
		echo '{"msg":"Not found version.json"}';
		return ;
	}
	//索引内容
	$verson_content = file_get_contents($version_file);
	
	//解析成对象格式
	$verson_content = json_decode($verson_content);
	/**
	 * 如果是压缩包格式
	 */
	if(!endWith($client_version,".zip")){
		//不支持的版本类型
		echo '{"msg":"File type not allowed"}';
		return;
	}
	//版本历史列表
	$version_list = array();
	$version_list = $verson_content;
	
//	if(startswith($client_version,"apk")){//安卓平台
//		$version_list = $verson_content->apk;
//	}elseif(startswith($client_version,"ipa")){//ios平台
//		$version_list = $verson_content->ipa;
//	}
	/**
	 * 检查是否已经生产版本记录
	 */
	if(empty($version_list)){
		echo '{"msg":"Not found version list"}';
		return;
	}
	//当前最新的版本
	$version_latest = current($version_list);
	//版本对比检测
	if($version_latest==$client_version){
		echo "{}";//客户端资源已经是最新,不需要更新
	}else{
		//检测是否已经生成过对应下载
		$upgrade_version_file = UPGRADE_PATH."$user/$app/upgrade.json";
		if(@!file_exists($upgrade_version_file)){
			@file_put_contents($upgrade_version_file,"{}");
		}
		//更新的内容
		$upgrade_version_content = file_get_contents($upgrade_version_file);
		$upgrade_version_content =  json_decode($upgrade_version_content,true);
		$upgrade_version_key = "$client_version => $version_latest";
		if(@$upgrade_version_content[$upgrade_version_key]){
			//待升级的文件
			$upgrade_file = $upgrade_version_content[$upgrade_version_key];
			$info["url"] = home_url().UPGRADE_BASE."$user/$app/$upgrade_file.zip";
			$info["version"] = $version_latest;
			$info["size"] = getRealSize(filesize(UPGRADE_PATH."$user/$app/$upgrade_file.zip"));
			echo stripslashes(json_encode($info));
			return;
		}
		
		
		//需要下载更新
		$upgrade_version_list = array();
		//循环所有版本库
		foreach ( $version_list as $version_item ) {
       		if($version_item==$client_version){//客户端版本与当前版本相同时候不需要再更新
       			break;
       		}
       		//添加需要更新的版本库
       		array_unshift($upgrade_version_list,$version_item);
		}	
		
		//所需要更新的版本数量
		$need_upgrade_num = count($upgrade_version_list);
		
		if($need_upgrade_num>0){
			if($need_upgrade_num==1){//如果只需要更新一个版本,直接下载更新包
				//TODO
				$version  = $upgrade_version_list[0];
				$info["url"] = home_url().VERSION_BASE."$user/$app/".$version;
				$info["version"] = $version;
				$info["size"] = getRealSize(filesize(VERSION_PATH."$user/$app/".$version));
				echo stripslashes(json_encode($info));
			}else{
				//清空临时文件夹
				@FileUtil::unlinkDir(EXTRACT_PATH,false);   
				
				//服务器处理,将多个版本包解压并且合并,重新组装后发送新的地址给客户端
				foreach ( $upgrade_version_list as $upgrade_version ) {
					
					//需要更新的版本绝对路径
       				$ugrade_version_file = VERSION_PATH."$user/$app/$upgrade_version";
       				if(!@file_exists($ugrade_version_file)){
       					continue;
       				}
//       				echo "unzip: $upgrade_version \n";
       				$archive = new PclZip($ugrade_version_file);
       				//解压到临时文件夹
       				//删除根和文件名同名的根目录
       				//设置解压后的权限
       				 if ($archive->extract(PCLZIP_OPT_PATH, EXTRACT_PATH,
//       				 					PCLZIP_OPT_REMOVE_PATH,substr($upgrade_version,0,(strlen($upgrade_version)-4))
       				 					    PCLZIP_OPT_REMOVE_PATH,EXTRACT_PATH
       				 					   // PCLZIP_OPT_BY_PREG,'/^[^__MACOSX]*$/'//过滤Mac文件夹
       				 					   // PCLZIP_OPT_BY_PREG,'/^.DS_Store*$/'//过滤Mac文件夹
       				 					   ) == 0) {
					    die("Error : ".$archive->errorInfo(true));
					 }
				}
				
				//新的压缩的名称
				$zip_file = EXTRACT_PATH."/pack.zip";
				//解压成功,重新打包.
				$pack_archive = new PclZip($zip_file);
				$temp_file_list = implode(",",getFileList(EXTRACT_PATH));
				$v_list = $pack_archive->create($temp_file_list,
												PCLZIP_OPT_REMOVE_PATH, EXTRACT_PATH
//												PCLZIP_OPT_REMOVE_ALL_PATH
												);
				if ($v_list == 0) {
					die("Error : ".$pack_archive->errorInfo(true));
				}else{
					//新的文件的MD5
					$file_md5 = md5_file($zip_file);
					//待更新的文件名
					$upgrade_file = UPGRADE_PATH."$user/$app/$file_md5.zip";
					if(@!file_exists($upgrade_file)&&file_exists($zip_file)){
						copy($zip_file,UPGRADE_PATH."$user/$app/$file_md5.zip");
						$upgrade_version_content[$upgrade_version_key] = $file_md5;
						file_put_contents($upgrade_version_file,indent(json_encode($upgrade_version_content)));
					}
					$version  = $upgrade_version_list[0];
					$info["url"] = home_url().UPGRADE_BASE."$user/$app/$file_md5.zip";
					$info["version"] = $version_latest;
					$info["size"] = getRealSize(filesize(UPGRADE_PATH."$user/$app/$file_md5.zip"));
					echo stripslashes(json_encode($info));
				}
			}
		}
	}
}
