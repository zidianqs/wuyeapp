<?php

class ReservationAction extends UserAction
{
    public function index()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用预约管理,请充值后再使用', U('Home/Index/price'));
        }
        $data = M('Reservation');
        $where = array('token' => session('token'));
        $reslist = $data->where($where)->select();
        $this->assign('reslist', $reslist);
        $this->display();
    }
    public function huodong()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用预约管理,请充值后再使用', U('Home/Index/price'));
        }
        $data = M('Huodong');
        $where = array('token' => session('token'));
        $reslist = $data->where($where)->select();
        $this->assign('reslist', $reslist);
        $this->display();
    }
    public function dingzhi()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用定制服务,请充值后再使用', U('Home/Index/price'));
        }
        $data = M('Dingzhi');
        $where = array('token' => session('token'));
        $reslist = $data->where($where)->select();
        $this->assign('reslist', $reslist);
        $this->display();
    }
    public function add()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用预约管理,请充值后再使用', U('Home/Index/price'));
        }
        if (IS_POST) {
            $data = D('Reservation');
            $_POST['token'] = session('token');
            if ($data->create() != false) {
                if ($id = $data->data($_POST)->add()) {
                    $data1['pid'] = $id;
                    $data1['module'] = 'Reservation';
                    $data1['token'] = session('token');
                    $data1['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->add($data1);
                    $this->success('添加成功', U('Reservation/index', array('token' => session('token'))));
                } else {
                    $this->error('服务器繁忙,请稍候再试');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $this->display();
        }
    }
    public function addHuodong()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用活动管理,请充值后再使用', U('Home/Index/price'));
        }
        if (IS_POST) {
            $data = D('Huodong');
            $_POST['token'] = session('token');
            if ($data->create() != false) {
                if ($id = $data->data($_POST)->add()) {
                    $data1['pid'] = $id;
                    $data1['module'] = 'Huodong';
                    $data1['token'] = session('token');
                    $data1['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->add($data1);
                    $this->success('添加成功', U('Reservation/huodong', array('token' => session('token'))));
                } else {
                    $this->error('服务器繁忙,请稍候再试');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $this->display();
        }
    }
    public function addDingzhi()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用活动管理,请充值后再使用', U('Home/Index/price'));
        }
        if (IS_POST) {
            $data = D('Dingzhi');
            $_POST['token'] = session('token');
            if ($data->create() != false) {
                if ($id = $data->data($_POST)->add()) {
                    $data1['pid'] = $id;
                    $data1['module'] = 'Dingzhi';
                    $data1['token'] = session('token');
                    $data1['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->add($data1);
                    $this->success('添加成功', U('Reservation/dingzhi', array('token' => session('token'))));
                } else {
                    $this->error('服务器繁忙,请稍候再试');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $this->display();
        }
    }
    public function edit()
    {
        if (IS_POST) {
            $data = D('Reservation');
            $where = array('id' => $_REQUEST['id'], 'token' => session('token'));
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            if ($data->create()) {
                if ($data->where($where)->save($_POST)) {
                    $data1['pid'] = $_REQUEST['id'];
                    $data1['module'] = 'Reservation';
                    $data1['token'] = session('token');
                    $da['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->where($data1)->save($da);
                    $this->success('修改成功', U('Reservation/index', array('token' => session('token'))));
                } else {
                    $this->error('操作失败');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $id = $this->_get('id');
            $where = array('id' => $id, 'token' => session('token'));
            $data = M('Reservation');
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            $reslist = $data->where($where)->find();
            $this->assign('reslist', $reslist);
            $this->display('add');
        }
    }
    public function editHuodong()
    {
        if (IS_POST) {
            $data = D('Huodong');
            $where = array('id' => $_REQUEST['id'], 'token' => session('token'));
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            if ($data->create()) {
                if ($data->where($where)->save($_POST)) {
                    $data1['pid'] = $_REQUEST['id'];
                    $data1['module'] = 'Huodong';
                    $data1['token'] = session('token');
                    $da['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->where($data1)->save($da);
                    $this->success('修改成功', U('Reservation/huodong', array('token' => session('token'))));
                } else {
                    $this->error('操作失败');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $id = $this->_get('id');
            $where = array('id' => $id, 'token' => session('token'));
            $data = M('Huodong');
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            $reslist = $data->where($where)->find();
            $this->assign('reslist', $reslist);
            $this->display('addHuodong');
        }
    }

    public function editDingzhi()
    {
        if (IS_POST) {
            $data = D('Dingzhi');
            $where = array('id' => $_REQUEST['id'], 'token' => session('token'));
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            if ($data->create()) {
                if ($data->where($where)->save($_POST)) {
                    $data1['pid'] = $_REQUEST['id'];
                    $data1['module'] = 'Dingzhi';
                    $data1['token'] = session('token');
                    $da['keyword'] = trim($_POST['keyword']);
                    M('Keyword')->where($data1)->save($da);
                    $this->success('修改成功', U('Reservation/dingzhi', array('token' => session('token'))));
                } else {
                    $this->error('操作失败');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $id = $this->_get('id');
            $where = array('id' => $id, 'token' => session('token'));
            $data = M('Dingzhi');
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            $reslist = $data->where($where)->find();
            $this->assign('reslist', $reslist);
            $this->display('addDingzhi');
        }
    }

    public function del()
    {
        $id = (int) $this->_get('id');
        $res = M('Reservation');
        $find = array('id' => $id, 'token' => $this->_get('token'));
        $result = $res->where($find)->find();
        if ($result) {
            $res->where('id=' . $result['id'])->delete();
            $where = array('pid' => $result['id'], 'module' => 'Reservation', 'token' => session('token'));
            M('Keyword')->where($where)->delete();
            $this->success('删除成功', U('Reservation/index', array('token' => session('token'))));
            die;
        } else {
            $this->error('非法操作！');
            die;
        }
    }
    public function delHuodong()
    {
        $id = (int) $this->_get('id');
        $res = M('Huodong');
        $find = array('id' => $id, 'token' => $this->_get('token'));
        $result = $res->where($find)->find();
        if ($result) {
            $res->where('id=' . $result['id'])->delete();
            $where = array('pid' => $result['id'], 'module' => 'Huodong', 'token' => session('token'));
            M('Keyword')->where($where)->delete();
            $where = array('rid' => $result['id'], 'token'=> session('token'));
            M('Huodongbook')->where($where)->delete();
            $this->success('删除成功', U('Reservation/huodong', array('token' => session('token'))));
            die;
        } else {
            $this->error('非法操作！');
            die;
        }
    }
    public function delDingzhi()
    {
        $id = (int) $this->_get('id');
        $res = M('Dingzhi');
        $find = array('id' => $id, 'token' => $this->_get('token'));
        $result = $res->where($find)->find();
        if ($result) {
            $res->where('id=' . $result['id'])->delete();
            $where = array('pid' => $result['id'], 'module' => 'Dingzhi', 'token' => session('token'));
            M('Keyword')->where($where)->delete();
            $where = array('rid' => $result['id'], 'token'=> session('token'));
            M('Dingzhibook')->where($where)->delete();
            $this->success('删除成功', U('Reservation/dingzhi', array('token' => session('token'))));
            die;
        } else {
            $this->error('非法操作！');
            die;
        }
    }
    public function manage()
    {
        $t_reservebook = M('Reservebook');
        $rid = $this->_get('id');
        $where = array('token' => session('token'), 'rid' => $rid);
        $books = $t_reservebook->where($where)->select();
        $this->assign('books', $books);
        $this->assign('count', $t_reservebook->count());
        $this->assign('ok_count', $t_reservebook->where('remate=1')->count());
        $this->assign('lose_count', $t_reservebook->where('remate=2')->count());
        $this->assign('call_count', $t_reservebook->where('remate=0')->count());
        $this->display();
    }
    public function manageHuodong()
    {
        $t_reservebook = M('Huodongbook');
        $rid = $this->_get('id');
        $where = array('token' => session('token'), 'rid' => $rid);
        $books = $t_reservebook->where($where)->select();
        $this->assign('books', $books);
        $this->assign('count', $t_reservebook->count());
        $this->display();
    }
    public function manageDingzhi()
    {
        $t_reservebook = M('Dingzhibook');
        $rid = $this->_get('id');
        $where = array('token' => session('token'), 'rid' => $rid);
        $books = $t_reservebook->where($where)->select();
        $this->assign('books', $books);
        $this->assign('count', $t_reservebook->count());
        $this->display();
    }
    public function reservation_uinfo()
    {
        $id = $this->_get('id');
        $token = $this->_get('token');
        $where = array('id' => $id, 'token' => $token);
        $t_reservebook = M('Reservebook');
        $userinfo = $t_reservebook->where($where)->find();
        $this->assign('userinfo', $userinfo);
        if (IS_POST) {
            $id = $this->_post('id');
            $token = session('token');
            $where = array('id' => $id, 'token' => $token);
            $ok = $t_reservebook->where($where)->save($_POST);
            if ($ok) {
                $this->assign('ok', 1);
            } else {
                $this->assign('ok', 2);
            }
        }
        $this->display();
    }
    public function manage_del()
    {
        $id = $this->_get('id');
        $t_reservebook = M('Reservebook');
        $where = array('id' => $id, 'token' => $this->_get('token'));
        $check = $t_reservebook->where($where)->find();
        if (!empty($check)) {
            $t_reservebook->where(array('id' => $check['id']))->delete();
            $this->success('删除成功', U('Reservation/index', array('token' => session('token'))));
            die;
        } else {
            $this->error('非法操作！');
            die;
        }
    }
    public function total()
    {
        $this->display();
    }
}
?>