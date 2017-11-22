<?php
session_start(); //开启试试
ignore_user_abort(true);
require 'Model.class.php';

class ChatServer1 extends Model{
	
	private $chatList=array();
	private $user;
    

	public function __construct()
	{
	   $this->action();
	}
	
	
	/**
	 * 入口
	 */
	private function action()
	{
	   switch($_POST['action'])
	   {
	   	  case 'checknick':
	   	  	$this->checkNick();
	   	  	break;
	   	  case 'addchat':
	   	  	$this->addChat();
	   	  	break;
	   	  case 'list':
	   	  	$this->personList();
	   	  	break;
	   	  case 'message':
	   	  	$this->getMessage();
	   	  	break;
	   	  case 'user':
	   	  	$this->getSessionUser();
	   	  	break;
	   	  case 'allchat':
	   	  	$this->allChat();
	   	  	break;
	   	  case 'exit':
	   	  	$this->userExit();
	   	  	break;
	   	  default:
	   	  		echo "<script type='text/javasript'>alert('非法操作!');</script>";
	   }
	}
	
	/**
	 * 检查名称是否存在
	 */
	private function checkNick($name)
	{
	   $clientName=$name;
	   $sql="select * from persons where nickname='$name' limit 1;";
       $object=parent::getOne($sql);
       return is_object($object)?true:false;
	}
	
	/**
	 * 加入聊天室
	 */
	private function addChat()
	{
	    $name=trim($_POST['nickname']);
	    //昵称不存在，则创建
	    if(!$this->checkNick($name))
	    {
	    	$headpic='/img/head/'.mt_rand(2013,2024).'.jpg';
	    	$summary='这个人很懒，什么也没留下!';
	    	$status=1;
	    	$sql="insert into persons(nickname,headimg,summary,status)values('$name','$headpic','$summary','$status')";
	    	$nums=parent::cud($sql);
	    	if($nums>0)
	    	{
	    		$sql="select * from persons where nickname='$name' limit 1;";
                $user=parent::getOne($sql);
                $users=array('id'=>$user->id,'nickname'=>$user->nickname,'status'=>$user->status);
                $_SESSION['users']=$users;
                $this->user=$user;
                $sql="insert into message(sender,receiver,content,addtime,status)values('1','3','$user->nickname,加入聊天室!',now(),'1');";
                parent::cud($sql);
		    	echo json_encode(array('status'=>1,'message'=>'加入成功！','data'=>$users));
		        exit();
	    	}
	    	else 
	    	{
	    	    echo json_encode(array('status'=>3,'message'=>'加入失败！'));
		        exit();
	    	}
	    }
	    else 
	    {
	    	//查询昵称存在，且状态离线，那么准许登录
	    	$sql="select * from persons where nickname='$name' and status=0 limit 1;";
	        $user=parent::getOne($sql);
            if(is_object($user))
            {
                //改变该用户状态为在线
                $sql="update persons set status=1 where nickname='$name' limit 1;";
                if(parent::cud($sql)>0)
                {
                   $users=array('id'=>$user->id,'nickname'=>$user->nickname,'status'=>$user->status);
	    	       $_SESSION['users']=$users;
	    	       $sql="insert into message(sender,receiver,content,addtime,status)values('1','3','$user->nickname,加入聊天室!',now(),'1');";
	    	       parent::cud($sql);    	   
                   echo json_encode(array('status'=>1,'message'=>'加入成功！','data'=>$users));
                   exit();
                }
                else
                {
                   echo json_encode(array('status'=>0,'message'=>'加入失败！'));
                   exit();
                }	
            }
            else 
            {
            	//已经在线，那么提示已经在线
		        echo json_encode(array('status'=>0,'message'=>'此昵称已经存在!'));
		        exit();
            }
	    }
	}
	
	/**
	 * 获取群员列表
	 */
	private function personList()
	{
        $sql="select * from persons where 1=1 and status=1;";
        $list=parent::getAll($sql);
        if(!empty($list))
        {
            echo json_encode(array('status'=>1,'message'=>'成功获取列表','chatlist'=>$list));
		    exit();
        }
        else
        {
            echo json_encode(array('status'=>0,'message'=>'获取列表失败'));
		    exit();
        }
	}
	
	/**
	 * 获取群聊消息
	 */
	private function getMessage()
	{
	   $sql="select m.id as mid,p1.nickname as sname,p1.headimg as simg,p1.id as p1id,p2.nickname as p2name,p2.id as p2id,m.content as content,m.addtime as addtime from message as m,persons as p1,persons p2 where m.sender=p1.id and m.receiver=p2.id and m.status=1 and m.receiver=3 order by m.id asc;";
	   $list=parent::getAll($sql);
	   if(!empty($list))
        {
            echo json_encode(array('status'=>1,'message'=>'成功获取信息列表','msglist'=>$list));
		    exit();
        }
        else
        {
            echo json_encode(array('status'=>0,'message'=>'获取信息失败'));
		    exit();
        }
	}
	
	/**
	 * 群聊
	 */
	private function allChat()
	{
	    $sender=trim($_POST['sid']);
	    $receiver=trim($_POST['rid']);
	    $content=htmlspecialchars(trim($_POST['content']));
	    $content=Tool::filterWord($content,'./word.txt');
	    $sql="insert into message(sender,receiver,content,addtime,status)values('$sender','$receiver','$content',now(),'1');";
	    if(parent::cud($sql)>0)
	    {
	        echo json_encode(array('status'=>1,'message'=>'发送成功!'));
		    exit();
	    }
	    else
	    {
	       echo json_encode(array('status'=>0,'message'=>'内容发送失败'));
		   exit();
	    } 
	    
	}
	
	/**
	 *获取session
	 */
	private function getSessionUser()
	{
	     if(isset($_SESSION['users']))
	     {
	        echo json_encode(array('status'=>1,'message'=>'用户存在!','data'=>$_SESSION['users']));
		    
	        exit();
	     }
	     else 
	     {
	        echo json_encode(array('status'=>0,'message'=>'用户不存在!'));
		    exit();
	     }
	}
	
	/**
	 *用户离线
	 */
	private function userExit()
	{
		        //改变该用户状态为离线
	            $user=$_SESSION['users']['nickname'];
                $sql="update persons set status=1 where nickname='$user' limit 0;";
                if(parent::cud($sql)>0)
                {
	    	       $sql="insert into message(sender,receiver,content,addtime,status)values('1','3','$user->nickname,离开了聊天室!',now(),'1');";
	    	       parent::cud($sql);    
	    	       session_destroy();	   
                   exit();
                }
                else
                {
                   echo json_encode(array('status'=>0,'message'=>'加入失败！'));
                   exit();
                }	
	}
	
}

new ChatServer1();