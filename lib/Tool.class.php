<?php

class Tool{

	/**
	 * 操作成功
	 * @param string $message 消息
	 * @param URL $url 跳转地址
	 */
    public static function alertLocation($title,$message,$url)
    {
        if(!empty($message))
        {
        	echo "<script src='".TEMP_ADMIN_URL."/vendor/jquery/jquery.min.js'></script><script src='".TEMP_PLUGINS_URL."/layer/layer.js'></script><script type='text/javascript'>layer.open({title:'$title',content:'$message',btn:['确定'],yes:function(index,layero){location.href='$url';}});</script>";
	        exit();
        }
        else
        {
            header("Location:".$url);
    		exit();
        }
    }
    
    /**
     * 操作失败
     * @param string $message
     */
    public static function alertBack($title,$message)
    {
        echo "<script src='".TEMP_ADMIN_URL."/vendor/jquery/jquery.min.js'></script><script src='".TEMP_PLUGINS_URL."/layer/layer.js'></script><script type='text/javascript'>layer.open({title:'$title',content:'$message',btn:['确定'],yes:function(index,layero){history.back()}});</script>";
        exit();
    }
    
     /**
     * 操作失败
     * @param string $message
     */
    public static function alertUploadBack($path)
    {
        echo "<script type='text/javascript'>opener.document.add.thumbnail.value='$path';</script>";
        echo "<script type='text/javascript'>opener.document.add.pic.style.display='block';</script>";
        echo "<script type='text/javascript'>opener.document.add.pic.src='$path';</script>";
        echo "<script type='text/javascript'>window.close();</script>";
        exit();
    }
    
    /**
     * 转移html特殊字符串
     * @param mixed $data 需要转义的字符
     * @return mixed $str 转义后的字符
     */
    public static function htmlString($data)
    {
       $str='';
       if(is_array($data))
       {
          foreach($data as $key=>$value)
          {
             $str[$key]=Tool::htmlString($value);//递归
          }
       }
       else if(is_object($data))
       {
         $str=new stdClass();
         foreach($data as $key=>$value)
         {
             $str->$key=Tool::htmlString($value);//递归
         }
       }
       else
       {
          $str=htmlspecialchars($data);
       }
       
       return $str;
    }
    
    /**
     * 数据转义
     * @param unknown_type $data
     */
    public static function mysqlString($data)
    {
    	//如果PHP引擎有自动开启过滤功能，则直接返回数据
       //!GMQG?mysqli_real_escape_string($data):$data;
    }
    
/**
     * 获取客户端IP地址
     * @return string ip address
     */
    public static function getCustomerIp()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }
    
    /**
     *json格式
     * @return header('Content-Type:application/json');
     */
    public static function jsonType()
    {
       return header('Content-Type:application/json');
    }
    
    /**
     * 清理session
     */
    public static function cleanSession()
    {
        if(session_start())
        {
            session_destroy();
        }
    }
    
    /**
     * 检查session
     */
    public static function checkSession()
    {
        if(!isset($_SESSION['admin']))
        {
           Tool::alertLocation(null,null,'login.php');
        }
    }
    
    /**
     * 过滤敏感字符
     * @param $str
     * @param $filter
     */
    public static function filterWord($str,$filter)
    {
        if(!($words=file_get_contents($filter)))
        {
           die('读取过滤词汇失败!');
        }
        $str=strtolower($str);
        $words=explode('|',$words);
        for($i=0;$i<count($words);$i++)
        {
          $str=str_replace($words[$i],"***",$str);
        }
        
        return $str;      
    }
}