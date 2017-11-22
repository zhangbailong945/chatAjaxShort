<?php
require 'Db.class.php';
require 'Tool.class.php';
/**
 * 数据访问层基类
 * @author loach
 *
 */
class Model{
    /**
     * 
     * 查询某个表的总记录数 模型
     * @param string $sql 查询语句
     * @return int count 总记录
     */
	protected function tableCount($sql)
	{
	   $db=Db::getDB();
	   $result=$db->query($sql);
	   $count=$result->fetch_row();
	   Db::closeDB($result, $db);
	   return $count[0];
	}
	
	/**
	 * 获取下一条记录的id 模型
	 * @param string $table 数据表
	 * @return int id 查询语句
	 */
	public function nextId($table)
	{
	   $sql="SHOW TABLE STATUS LIKE '$table'";
	   $object=$this->getOne($sql);
	   return $object->Auto_increment;
	}
	
	/**
	 * 查找单条数据 模型
	 * @param string $sql 查询语句
	 * 
	 */
	protected function getOne($sql)
	{
	   $db=Db::getDB();
	   $result=$db->query($sql);
	   $object=$result->fetch_object();
	   Db::closeDB($result, $db);
	   return Tool::htmlString($object);
	}
	
	/**
	 * 查找表中所有记录 模型
	 * @param string $sql 查询语句
	 */
	protected function getAll($sql)
	{
	   $db=Db::getDB();
	   $result=$db->query($sql);
	   $list=array();
	   while(!!$objects=$result->fetch_object())
	   {
	       $list[]=$objects;
	   }
	   Db::closeDB($result, $db);
	   return Tool::htmlString($list);
	}
	
	/**
	 * 增加、修改、删除模型
	 * @param string $sql 查询语句
	 * @return int affected_rows 受影响的行数
	 */
	protected function cud($sql)
	{
	   $db=Db::getDB();
	   $db->query($sql);
	   $affected_rows=$db->affected_rows;
	   Db::closeDB($result, $db);
	   return $affected_rows;
	}	
	
/**
     * 根据某个表的id 返回一条数据
     * @param string $sql
     * @return object
     */
    protected  function getTableById($sql)
    {
        $db=DB::getDB();
        $result=$db->query($sql);
        $object=$result->fetch_object();
        DB::closeDB($result, $db);
        return Tool::htmlString($object);
    }
    
    /**
     * 执行多条sql
     * @param string $sql
     */
    protected function updateMulti($sql)
    {
       $db=DB::getDB();
       $db->multi_query($sql);
       Db::closeDB($result=null,$db);
       return true;
    }

}