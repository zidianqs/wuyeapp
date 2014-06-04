<?php
// 3G
class ReservationAction extends BaseAction{

    public $token;
    public $wecha_id;
    public function _initialize() {
        parent::_initialize();
        $this->token=$this->_get('token');
        $this->wecha_id=$this->_get('wecha_id');
        $this->assign('token',$this->token);
        $this->assign('wecha_id',$this->wecha_id);
        //$get_ids = M('Estate')->where(array('token'=>$this->token))->field('res_id,classify_id')->find();
        //$this->assign('rid',$get_ids['res_id']);
    }

   public function index(){
       $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //echo '此功能只能在微信浏览器中使用';exit;
        }
        $data = M("Reservation");
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $rid         = (int)$this->_get('rid');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $where = array('token'=>$token);
        //$rid = M('Estate')->where($where)->getField('res_id');
        if($rid != ''){
             $this->assign('rid',$rid);
            $where2 = array('token'=>$token,'id'=>$rid);
            $reslist =  $data->where($where2)->find();
            if(empty($reslist)){
                $this->error('Sorry.请求错误！正在带您转到首页',U('Estate/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
                exit;
            }
        }
        $where3 = array('token'=>$token,'wecha_id'=>$wecha_id);
        $user = M('Userinfo')->where($where3)->field('truename,tel as user_tel')->find();
        if(!empty($user)){
            $reslist = array_merge($reslist,$user);
        }
        $this->assign('reslist',$reslist);
        $t_housetype = M('Estate_housetype');
        $housetype = $t_housetype->where($where)->order('sort desc')->field('id as hid,name')->select();
        $this->assign('housetype',$housetype);
        $where4 = array('token'=>$token,'wecha_id'=>$wecha_id);
        $count = M('Reservebook')->where($where4)->count();
        $this->assign('count',$count);
        $this->assign('type','Maintenance'); //默认是保修
        $this->display();

    }

    public function kuaidi(){
       $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //echo '此功能只能在微信浏览器中使用';exit;
        }
        $data = M("Reservation");
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $rid         = (int)$this->_get('rid');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $where = array('token'=>$token);
        //$rid = M('Estate')->where($where)->getField('res_id');
        if($rid != ''){
             $this->assign('rid',$rid);
            $where2 = array('token'=>$token,'id'=>$rid);
            $reslist =  $data->where($where2)->find();
            if(empty($reslist)){
                $this->error('Sorry.请求错误！正在带您转到首页',U('Estate/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
                exit;
            }
        }
        $where3 = array('token'=>$token,'wecha_id'=>$wecha_id);
        $user = M('Userinfo')->where($where3)->field('truename,tel as user_tel')->find();
        if(!empty($user)){
            $reslist = array_merge($reslist,$user);
        }
        $this->assign('reslist',$reslist);
        $t_housetype = M('Estate_housetype');
        $housetype = $t_housetype->where($where)->order('sort desc')->field('id as hid,name')->select();
        $this->assign('housetype',$housetype);
        $where4 = array('token'=>$token,'wecha_id'=>$wecha_id);
        $count = M('Reservebook')->where($where4)->count();
        $this->assign('count',$count);
        $this->assign('type','Delivery');//快递收发
        $this->display('index');

    }

    public function huodong(){
       $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //echo '此功能只能在微信浏览器中使用';exit;
        }
        $data = M("Huodong");
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $rid         = (int)$this->_get('rid');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $where = array('token'=>$token);
        //$rid = M('Estate')->where($where)->getField('res_id');
        if($rid != ''){
            $this->assign('rid',$rid);
            $where2 = array('token'=>$token,'id'=>$rid);
            $reslist =  $data->where($where2)->find();
            if(empty($reslist)){
                $this->error('Sorry.请求错误！正在带您转到首页',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
                exit;
            }
        }
        $this->assign('reslist',$reslist);
        $where4 = array('token'=>$token,'wecha_id'=>$wecha_id);
        $count = M('Huodongbook')->where($where4)->count();
        $this->assign('count',$count);
        $this->assign('rid', $rid);
        $this->display();

    }

    public function dingzhi(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
        }
        $data = M("Dingzhi");
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $rid         = (int)$this->_get('rid');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $where = array('token'=>$token);
        //$rid = M('Estate')->where($where)->getField('res_id');
        if($rid != ''){
            $this->assign('rid',$rid);
            $where2 = array('token'=>$token,'id'=>$rid);
            $reslist =  $data->where($where2)->find();
            if(empty($reslist)){
                $this->error('Sorry.请求错误！正在带您转到首页',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
                exit;
            }
        }
        $this->assign('reslist',$reslist);
        $where4 = array('token'=>$token,'wecha_id'=>$wecha_id);
        $count = M('Dingzhibook')->where($where4)->count();
        $this->assign('count',$count);
        $this->assign('rid', $rid);
        $this->display();

    }

    public function add(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $da['token']      = $this->_post('token');
        $da['wecha_id']   = $this->_post('wecha_id');
        $da['rid']        = (int)$this->_post('rid');
        $da['truename']   = $this->_post("truename");
        $da['dateline']   = $this->_post("dateline");
        $da['timepart']   = $this->_post("timepart");
        $da['info']       = $this->_post("info");
        $da['tel']        = $this->_post("tel");
        $da['type']       = $this->_post('type');
        $das['id']        = (int)$this->_post('id');
        //$da['fieldsigle'] =$this->_post('fieldsigle');
        $da['housetype']  = $this->_post('fielddownload');
        $da['booktime']   = time();
        $book   =   M('Reservebook');
        if($das['id'] != ''){
            $o = $book->where(array('id'=>$das['id']))->save($da);
            if($o){
                 $arr=array('errno'=>0,'msg'=>'修改成功','token'=>$token,'wecha_id'=>$wecha_id);
                echo json_encode($arr);
                exit;
            }else{
                 $arr=array('errno'=>1,'msg'=>'修改失败','token'=>$token,'wecha_id'=>$wecha_id);
                echo json_encode($arr);
                exit;
            }
        }
        $ok = $book->data($da)->add();
        if(!empty($ok)){
            $arr=array('errno'=>0,'msg'=>'恭喜预约成功','token'=>$token,'wecha_id'=>$wecha_id,'op'=>1);
            echo json_encode($arr);
            exit;
        }else{
             $arr=array('errno'=>1,'msg'=>'预约失败，请重新预约','token'=>$token,'wecha_id'=>$wecha_id);
            echo json_encode($arr);
            exit;
        }

    }

    public function addHuodong(){
        Log::write("addHuodong");
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $da['token']      = $this->_post('token');
        $da['wecha_id']   = $this->_post('wecha_id');
        $da['rid']        = (int)$this->_post('rid');
        Log::write("rid:", $da['rid']);
        $da['truename']   = $this->_post("truename");
        $da['remark']       = $this->_post("remark");
        $da['tel']        = $this->_post("tel");
        $da['booktime']   = time();
        $das['id']        = (int)$this->_post('id');
        $book   =   M('Huodongbook');
        if($das['id'] != ''){
            $o = $book->where(array('id'=>$das['id']))->save($da);
            if($o){
                 $arr=array('errno'=>0,'msg'=>'修改成功','token'=>$token,'wecha_id'=>$wecha_id, 'bid'=>$das['id']);
                echo json_encode($arr);
                exit;
            }else{
                 $arr=array('errno'=>1,'msg'=>'修改失败，或者信息没变','token'=>$token,'wecha_id'=>$wecha_id);
                echo json_encode($arr);
                exit;
            }
        }
        $ok = $book->data($da)->add();
        if(!empty($ok)){
            Log::write("id:" . $ok);
            $arr=array('errno'=>0,'msg'=>'恭喜报名成功','token'=>$token,'wecha_id'=>$wecha_id, 'bid'=>$ok, 'op'=>1);
            echo json_encode($arr);
            exit;
        }else{
             $arr=array('errno'=>1,'msg'=>'报名失败，请重新报名','token'=>$token,'wecha_id'=>$wecha_id);
            echo json_encode($arr);
            exit;
        }

    }

    public function addDingzhi(){
        Log::write("addDingzhi");
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $da['token']      = $this->_post('token');
        $da['wecha_id']   = $this->_post('wecha_id');
        $da['rid']        = (int)$this->_post('rid');
        $da['coname']   = $this->_post("coname");
        $da['contact']   = $this->_post("contact");
        $da['require']       = $this->_post("require");
        $da['tel']        = $this->_post("tel");
        $da['booktime']   = time();
        $das['id']        = (int)$this->_post('id');
        $book   =   M('Dingzhibook');
        if($das['id'] != ''){
            $o = $book->where(array('id'=>$das['id']))->save($da);
            if($o){
                 $arr=array('errno'=>0,'msg'=>'修改成功','token'=>$token,'wecha_id'=>$wecha_id, 'bid'=>$das['id']);
                echo json_encode($arr);
                exit;
            }else{
                 $arr=array('errno'=>1,'msg'=>'修改失败，或者信息没变','token'=>$token,'wecha_id'=>$wecha_id);
                echo json_encode($arr);
                exit;
            }
        }
        $ok = $book->data($da)->add();
        if(!empty($ok)){
            Log::write("id:" . $ok);
            $arr=array('errno'=>0,'msg'=>'提交成功','token'=>$token,'wecha_id'=>$wecha_id, 'bid'=>$ok, 'op'=>1);
            echo json_encode($arr);
            exit;
        }else{
             $arr=array('errno'=>1,'msg'=>'提交失败，请重新提交','token'=>$token,'wecha_id'=>$wecha_id);
            echo json_encode($arr);
            exit;
        }

    }


    public function mylist(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $book   =   M('Reservebook');
        $where = array('token'=>$token,'wecha_id'=>$wecha_id);
        $books  = $book->where($where)->select();
        $this->assign('books',$books);

        $data = M("Reservation");
        $where2 = array('token'=>$token);
        $rid = $data->where($where2)->getField('headpic');
        $rid = M('Estate')->where($where)->getField('res_id');
        if($rid != ''){
            $where3 = array('token'=>$token,'id'=>$rid);
            $headpic =  $data->where($where3)->getField('headpic');
        }
        $this->assign('headpic',$headpic);
        $this->display();
    }

    public function myHuodonglist(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $book   =   M('Huodongbook');
        $where = array('token'=>$token,'wecha_id'=>$wecha_id);
        $books  = $book->where($where)->select();
        $data = M("Huodong");
        foreach ($books as $k => $v) {
            $hid = $v['rid'];
            $where2 = array('token'=>$token,'id'=>$hid);
            $huodong =  $data->where($where2)->find();
            $books[$k] = array_merge($huodong, $v); //如果有相同key，后面的会覆盖前面的
        }
        $this->assign('books',$books);
        $this->display();
    }

    public function myDingzhilist(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $token      = $this->_get('token');
        $wecha_id   = $this->_get('wecha_id');
        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $book   =   M('Dingzhibook');
        $where = array('token'=>$token,'wecha_id'=>$wecha_id);
        $books  = $book->where($where)->select();
        $data = M("Dingzhi");
        foreach ($books as $k => $v) {
            $hid = $v['rid'];
            $where2 = array('token'=>$token,'id'=>$hid);
            $dingzhi =  $data->where($where2)->field('info,address,email,tel as co_tel,title')->find();
            $books[$k] = array_merge($dingzhi, $v); //如果有相同key，后面的会覆盖前面的
        }
        $this->assign('books',$books);
        $this->display();
    }

    public function edit(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $book = M('Reservebook');
        $id = (int)$this->_get('id');
        $token = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        $where = array('id'=>$id,'token'=>$token,'wecha_id'=>$wecha_id);
        $reslist = $book->where($where)->field('id,rid,token,wecha_id,truename,tel as user_tel,housetype,dateline,timepart,info as userinfo,type,booktime')->find();
        if(!empty($reslist)){
            $this->assign('reslist',$reslist);
            $this->assign('bid', $reslist['id']);
            $this->assign("rid", $reslist['rid']);
            $this->assign('type',$reslist['type']); //默认是保修
            $where = array('token'=>$token,'wecha_id'=>$wecha_id);
            $count = M('Reservebook')->where($where)->count();
            $this->assign('count',$count);
        }else{
            $this->error('操作错误',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
        }
        // $where4 = array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>'house_book');
        // $count = M('Reservebook')->where($where4)->count();
        // $this->assign('count',$count);
        $this->display('index');

    }

    public function editHuodong(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $book = M('Huodongbook');
        $id = (int)$this->_get('id');
        $token = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        $where = array('id'=>$id,'token'=>$token,'wecha_id'=>$wecha_id);
        $reslist = $book->where($where)->field('id,rid,token,wecha_id,truename,tel as user_tel,remark,booktime')->find();
        if(!empty($reslist)){
            $model = M("Huodong");
            $where = array('token'=>$token,'id'=>$reslist['rid']);
            $huodong = $model->where($where)->find();
            if($huodong) {
                $reslist = array_merge($huodong, $reslist);
            }
            $this->assign('reslist',$reslist);
            $where = array('token'=>$token,'wecha_id'=>$wecha_id);
            $count = $book->where($where)->count();
            $this->assign('count',$count);
            $this->assign('bid', $id);
            $this->assign("rid", $reslist['rid']);
            Log::write("bid:" . $id);
        }else{
            $this->error('操作错误',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
        }
        $this->display('huodong');
    }

    public function editDingzhi(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $book = M('Dingzhibook');
        $id = (int)$this->_get('id');
        $token = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        $where = array('id'=>$id,'token'=>$token,'wecha_id'=>$wecha_id);
        $reslist = $book->where($where)->field('id,rid,token,wecha_id,coname,contact,tel as user_tel,require,booktime')->find();
        if(!empty($reslist)){
            $model = M("Dingzhi");
            $where = array('token'=>$token,'id'=>$reslist['rid']);
            $dingzhi = $model->where($where)->find();
            if($dingzhi) {
                $reslist = array_merge($dingzhi, $reslist);
            }
            $this->assign('reslist',$reslist);
            $where = array('token'=>$token,'wecha_id'=>$wecha_id);
            $count = $book->where($where)->count();
            $this->assign('count',$count);
            $this->assign('bid', $id);
            $this->assign("rid", $reslist['rid']);
            Log::write("bid:" . $id);
        }else{
            $this->error('操作错误',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
        }
        $this->display('dingzhi');
    }


}?>