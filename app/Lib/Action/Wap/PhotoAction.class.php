<?php
class PhotoAction extends BaseAction{
	/* 用户输入相册，系统回复默认相册，点击默认相册之后，打开相册的列表页面 */
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		// if(!strpos($agent,"icroMessenger")) {
		// 	echo '此功能只能在微信浏览器中使用';exit;
		// }
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}
		$photo=M('Photo')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		if($photo==false){ }
		$this->assign('photo',$photo);
		$this->display();
	}
	/* 用户点击某个相册之后，打开该相册的相片浏览页面 */
	public function plist(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		// if(!strpos($agent,"icroMessenger")) {
		// 	echo '此功能只能在微信浏览器中使用';exit;
		// }
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}
		$info=M('Photo')->field('title')->where(array('token'=>$token,'id'=>$this->_get('id')))->find();
		$photo_list=M('Photo_list')->where(array('token'=>$token,'pid'=>$this->_get('id'),'status'=>1))->select();
		//dump($photo);
		$this->assign('info',$info);
		$this->assign('photo',$photo_list);
		$this->display();
	}
}
?>