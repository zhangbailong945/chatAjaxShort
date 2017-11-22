<?php
require 'config.inc.php';
/**
 * mysql数据库连接类
 * @author loach
 *
 */
class Db{
   
	/**
	 * 返回数据库句柄
	 */
	public static function getDB()
	{
	   $mysqli=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_PORT);
	   if(mysqli_connect_errno())
	   {
	   	  echo "数据库连接失败，错误原因：".mysqli_connect_error();
	      exit();
	   }
	   $mysqli->set_charset(DB_CHAR);
	   return $mysqli;
	}
	
	/**
	 * 关闭数据库，释放资源
	 */
	public static function closeDB(&$result,&$db)
	{
	   if(is_object($result))
	   {
	      $result->free();
	      $result=null;
	   }
	   
	   if(is_object($db))
	   {
	      $db->close();
	      $db=null;
	   }
	}
}