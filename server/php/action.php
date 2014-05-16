<?php
 // 单位自动转换函数
    function getRealSize($size)
    { 
        $kb = 1024;         // Kilobyte
        $mb = 1024 * $kb;   // Megabyte
        $gb = 1024 * $mb;   // Gigabyte
        $tb = 1024 * $gb;   // Terabyte
        
        if($size < $kb)
        { 
            return $size." B";
        }
        else if($size < $mb)
        { 
            return round($size/$kb,2)." KB";
        }
        else if($size < $gb)
        { 
            return round($size/$mb,2)." MB";
        }
        else if($size < $tb)
        { 
            return round($size/$gb,2)." GB";
        }
        else
        { 
            return round($size/$tb,2)." TB";
        }
    }

 //上传的版本库
     function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    /**
     * 获取网址的根路径
     */
    function home_url(){
    	return dirname(dirname(get_full_url()));
    }
    
 function startswith($src,$match,$cases_ensitive=true){
		if($cases_ensitive){//大小写敏感型
			return strpos($src, $match) === 0;
		}else{//大小写不敏感型
			return stripos($src, $match) == 0;
		}
}


function endWith($src,$match,$cases_ensitive=true){
		if($cases_ensitive){//大小写敏感型
			return ($pos = strrpos($src, $match)) !== false && $pos == strlen($src) - strlen($match);
		}else{//大小写不敏感型
			return ($pos = strripos($src, $match)) !== false && $pos == strlen($src) - strlen($match);
		}
}



/**
 * 获取目录和文件
 */
function getFileList($directory) {        
    $files = array();        
    if(is_dir($directory)) {        
        if($dh = opendir($directory)) {        
            while(($file = readdir($dh)) !== false) {        
                if($file != '.' && $file != '..'&&$file!="__MACOSX") {      
                    $files[] = $directory.'/'.$file;        
                }        
            }    
            closedir($dh);        
        }        
    }        
    return $files;        
}        



function copy_dir($src,$dst) {  
    $dir = opendir($src);
    if(!file_exists($dst)){
	    @mkdir($dst);
    }
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                copy_dir($src . '/' . $file,$dst . '/' . $file);
				continue;
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}


    	/**
    	 * 删除文件夹下的所有文件,除了指定的文件外
    	 */
	function deldir($dir,$filter=array(),$drop_owner=false) {
	  //先删除目录下的文件：
		  $dh = opendir($dir);
		  $file_list = scandir($dir);
		  foreach ( $file_list as $file ) {
		  		if($file=="."||$file==".."){
		  			continue;
		  		}
       			if(is_array($filter)&&in_array(basename($file),$filter)){
       				continue;
       			}
       			if(is_dir($file)){
			      	$dir_item_num = count(scandir($file));
			      	if($dir_item_num>2){//判断文件夹是否为空
					  	$this->deldir($file,array(),true);
			      	}else{
			      		rmdir($file);
			      	}
       			}else{
	          		unlink($dir.'/'.$file);
       			}
		  }
		   closedir($dh);
		   if($drop_owner){
			  //删除当前文件夹：
		   		rmdir($dir);
		   }
	  }
	  
	  

/** 
 * Indents a flat JSON string to make it more human-readable. 
 * @param string $json The original JSON string to process. 
 * @return string Indented version of the original JSON string. 
 */
function indent($json) {

	$result = '';
	$pos = 0;
	$strLen = strlen($json);
	$indentStr = ' ';
	$newLine = "\n";
	$prevChar = '';
	$outOfQuotes = true;

	for ($i = 0; $i <= $strLen; $i++) {

		// Grab the next character in the string. 
		$char = substr($json, $i, 1);
		// Are we inside a quoted string? 
		if ($char == '"' && $prevChar != '\\') {
			$outOfQuotes = !$outOfQuotes;
			// If this character is the end of an element, 
			// output a new line and indent the next line. 
		} else
			if (($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos--;
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
		// Add the character to the result string. 
		$result .= $char;
		// If the last character was the beginning of an element, 
		// output a new line and indent the next line. 
		if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
			$result .= $newLine;
			if ($char == '{' || $char == '[') {
				$pos++;
			}
			for ($j = 0; $j < $pos; $j++) {
				$result .= $indentStr;
			}
		}
		$prevChar = $char;
	}
	return $result;
}

/**
 * 从数据库中加载所有数据库表名称 
 */
function loadtables() {
	$db = new DataBase();
	$sql = "SHOW TABLES;";
	$result = $db->query($sql);
	$ls_num = $db->num_rows($result);
	$tables = array ();
	if ($ls_num <= 0) {
		return $tables;
	}
	while ($row = $db->fetch_array($result, MYSQL_BOTH)) {
		$tableName = $row[0];
		if ($tableName == "upgrade")
			continue;
		array_push($tables, $tableName);
	}
	$db->close();
	return $tables;
}

/**
 * 获取某一张数据表的内容
 * @param $tableName 
 */
function loadtable($tableName) {
	$db = new DataBase();
	$sql = "SELECT * FROM $tableName ; ";
	$result = $db->query($sql);
	$ls_num = $db->num_rows($result);
	$values = array ();
	if ($ls_num <= 0) {
		return $values;
	}
	$get_type_sql = "select COLUMN_NAME as name,DATA_TYPE as type from information_schema.columns where table_schema='" . _DB_NAME . "' and table_name='$tableName';";
	$get_type = $db->get_all($get_type_sql);
	$type_map = array ();
	foreach ($get_type as $value) {
		$name = $value['name'];
		$type = $value['type'];
		$type_map[$name] = $type;
	}
	while ($row = $db->fetch_array($result)) {
		$r = array ();
		foreach ($row as $key => $value) {
			$c_type = $type_map[$key];
			if ($c_type == "int") {
				$value = intval($value);
			} else
				if ($c_type == "float") {
					$value = floatval($value);
				}
			$r[$key] = $value;
		}
		array_push($values, $r);
	}
	$db->close();
	return json_encode($values);
}
