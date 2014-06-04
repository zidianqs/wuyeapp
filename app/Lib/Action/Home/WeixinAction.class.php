<?php
class WeixinAction extends Action
{
    private $token;
    private $fun;
    private $data = array();
    public function index()
    {
        $this->token = $this->_get('token');
        $weixin = new Wechat($this->token);
        $data = $weixin->request();
        $this->data = $weixin->request();
        $this->my = C('site_my');
        $to = $data['ToUserName'];
        // 畅e居的请求
        if ($to == 'gh_ebb1d4c09ded') {
            require('WeixinChangeju.class.php');
            list($content, $type) = WeixinChangeju::handler($data);
            // list($content, $type) = $this->changeju($data);
        }
        else {
            list($content, $type) = $this->reply($data);
        }
        Log::write(dump($content, false), Log::INFO);
        $weixin->response($content, $type);
    }
    private function changeju($data)
    {
        $from = $data['FromUserName'];
        $create_time = $data['CreateTime'];
        // 接受图片，TODO：判断ToUserName是否是畅e居的公众号
        if ('image' == $data['MsgType']) {
            $pic_url = $data['PicUrl'];
            $url = C('CHANGEJU_API_HOST')."api/upload?from=" . $from . "&create_t=" 
                . $create_time . "&pic_url=" . $pic_url . "&msg_type=image";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return array('图片发布成功！', 'text');
        }
        else if ('text' == $data['MsgType']) {
            $content = $data['Content'];
            if($content == 't') {
                require('WeixinChangeju.class.php');
                return WeixinChangeju::handler();
            }
            else if ($content == '区域') {
                $url = C('CHANGEJU_API_HOST').'api/hotarea';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                curl_close($ch);
                $rts = null;
                if (!empty($data)){
                    $rts = json_decode($data);
                    $return = array();
                    foreach ($rts as $k => $v) {
                        $return[] = array($v, $k, "http://image.imethodz.com/big/house/0a8ec00a-75bc-4f26-a818-be7cc302ceaf.jpg", C('CHANGEJU_API_HOST')."api/area/" .$k ."/1");
                    }
                }
                return array($return, 'news');
            }
            else if ($content == '发布') {
                $return = array();
                $return[] = array("轻松发布房源", "只需几步填写表单，然后微信发送房源图片即可轻松发布房源", "", C('CHANGEJU_API_HOST')."api/pubpage?wechat_id=" . $from);
                return array($return, 'news');
            }
            else if (strpos($content, "找房") === 0) {
                $content = str_replace("找房", "", $content);
                $content = urlencode($content);
                $url = C('CHANGEJU_API_HOST').'api/search?key=' . $content;
                Log::write($url);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                Log::write("houses:" . $data);
                curl_close($ch);
                $rts = null;
                if (!empty($data)){
                    $rts = json_decode($data);
                    $return = array();
                    foreach ($rts as $k => $v) {
                        list($pic, $longRentId) = split (' ', $v);
                        $return[] = array($k, $k, "http://image.imethodz.com/big/house/" . $pic, C('CHANGEJU_HOST')."changzu/" . $longRentId);
                    }
                }
                return array($return, 'news');
            }
            else if (strpos($content, "发布") === 0) {
                $from = $data['FromUserName'];
                $to = $data['ToUserName'];
                $create_time = $data['CreateTime'];
                $content = str_replace("发布", "", $content);
                $content = urlencode($content);
                $url = C('CHANGEJU_API_HOST')."api/pub?from=" . $from . "&create_t=" 
                    . $create_time . "&content=" . $content . "&msg_type=text";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                curl_close($ch);
                return array('房源发布成功！', 'text');
            }
        }
    }

    private function reply($data)
    {
        // 菜单点击事件
        if ('CLICK' == $data['Event']) {
            $data['Content'] = $data['EventKey'];
        }
        // 语音
        if ('voice' == $data['MsgType']) {
            $data['Content'] = $data['Recognition'];
        }
        // 关注和取消关注，发送欢迎信息
        if ('subscribe' == $data['Event']) {
            $this->requestdata('follownum');
            $data = M('Areply')->field('home,keyword,content')->where(array('token' => $this->token))->find();
            if ($data['keyword'] == '首页' || $data['keyword'] == 'home') {
                return $this->shouye();
            }
            if ($data['home'] == 1) {
                $like['keyword'] = array('like', ('%' . $data['keyword']) . '%');
                $like['token'] = $this->token;
                $back = M('Img')->field('id,text,pic,url,title')->limit(9)->order('id desc')->where($like)->select();
                foreach ($back as $keya => $infot) {
                    if ($infot['url'] != false) {
                        $url = $this->getFuncLink($infot['url']);
                    } else {
                        $url = rtrim(C('site_url'), '/') . U('Wap/Index/content', array('token' => $this->token, 'id' => $infot['id'], 'wecha_id' => $this->data['FromUserName']));
                    }
                    $return[] = array($infot['title'], $infot['text'], $infot['pic'], $url);
                }
                return array($return, 'news');
            } else {
                return array($data['content'], 'text');
            }
        } elseif ('unsubscribe' == $data['Event']) {
            $this->requestdata('unfollownum');
        }
        $Pin = new GetPin();
        $key = $data['Content'];
        $open = M('Token_open')->where(array('token' => $this->_get('token')))->find();
        $this->fun = $open['queryname'];
        $datafun = explode(',', $open['queryname']); // 提供的功能
        $tags = $this->get_tags($key); //中文分词
        $back = explode(',', $tags);
        // 判断用户输入的关键词是否包含在开通的功能里
        foreach ($back as $keydata => $data) {
            Log::write("data:" . $data, Log::INFO);
            $string = $Pin->Pinyin($data);
            Log::write("Pinyin:" . $string, Log::INFO);
            if (in_array($string, $datafun) && $string) {
                $check = $this->user('connectnum'); //用于判断请求次数是否超过额度
                if ($string == 'fujin') {
                    Log::write("request fujin", Log::INFO);
                    $this->recordLastRequest($key);
                }
                $this->requestdata('textnum'); //统计textnum类型的访问次数
                if ($check['connectnum'] != 1) {
                    $return = C('connectout');
                    continue;
                }
                unset($back[$keydata]); //释放变量，比如用户输入“附近”，那么unset之后附近变量就没有了
                // TODO: 某些函数没有
                eval(('$return= $this->' . $string) . '($back);'); //通过函数名来调用函数
                continue;
            }
        }
        if (!empty($return)) {
            if (is_array($return)) {
                return $return;
            } else {
                return array($return, 'text');
            }
        } 
        else {
            //微信push了location信息
            if ($this->data['Location_X']) {
                Log::write("Weixin push Location info", Log::INFO);
                $this->recordLastRequest(($this->data['Location_Y'] . ',') . $this->data['Location_X'], 'location');
                return $this->map($this->data['Location_X'], $this->data['Location_Y']);
            }
            //转换key
            $key = $this->transKey($key);
            switch ($key) {
            case 'home':
                return $this->home();
                break;
            case '地图': //查看公司静态地图
                return $this->companyMap();
                break;
            case '街景':
                return $this->jiejing();
                break;
            case 'LBS':
                $user_request_model = M('User_request');
                $loctionInfo = $user_request_model->where(array('token' => $this->_get('token'), 'msgtype' => 'location', 'uid' => $this->data['FromUserName']))->find();
                // 下面代码的意思是：如果本次请求距离上次发送本人的位置不超过1分钟，那么直接回复，不需要再次发送位置
                if ($loctionInfo && intval($loctionInfo['time'] > time() - 60)) {
                    $latLng = explode(',', $loctionInfo['keyword']);
                    return $this->map($latLng[1], $latLng[0]);
                }
                return array('请发送您所在的位置', 'text');
                break;
            case 'help':
                return $this->help();
                break;
            case '相册':
                return $this->xiangce();
                break;
            case '留言':
                $pro = M('reply_info')->where(array('infotype' => 'Liuyan', 'token' => $this->token))->find();
                return array(array(array($pro['title'], strip_tags(htmlspecialchars_decode($pro['info'])), $pro['picurl'], (((C('site_url') . '/index.php?g=Wap&m=Liuyan&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName'])), 'news');
                break;
            case '全景':
                $pro = M('reply_info')->where(array('infotype' => 'panorama', 'token' => $this->token))->find();
                // 如果用户配置了全景回复
                if ($pro) {
                    return array(array(array($pro['title'], strip_tags(htmlspecialchars_decode($pro['info'])), $pro['picurl'], ((((C('site_url') . '/index.php?g=Wap&m=Panorama&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com')), 'news');
                }
                // 如果用户没有配置全景回复 
                else {
                    return array(array(array('360°全景看车看房', '通过该功能可以实现3D全景看车看房', rtrim(C('site_url'), '/') . '/tpl/User/default/common/images/panorama/360view.jpg', ((((C('site_url') . '/index.php?g=Wap&m=Panorama&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com')), 'news');
                }
                break;
            case '微房产':
                $Estate = M('Estate')->where(array('token' => $this->token))->find();
                return array(array(array($Estate['title'], str_replace('&nbsp;', '', strip_tags(htmlspecialchars_decode($Estate['estate_desc']))), $Estate['cover'], ((((((C('site_url') . '/index.php?g=Wap&m=Estate&a=index&&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $Estate['id']) . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            default: // 自定义的关键词自动回复
                Log::write($key);
                $check = $this->user('diynum', $key);
                if ($check['diynum'] != 1) {
                    return array(C('connectout'), 'text');
                } else {
                    return $this->keyword($key);
                }
            }
        }
    } 
    
    /* 转换key */
    // TODO: 以一种可配置的方式来配置key的转换
    private function transKey($key) {
        //多个分支机构的时候，可以查看最近的，可以选择多种方式过去公司所在地
        if (!strcasecmp($key, 'lbs') || $key == '最近的' || (!(strpos($key, '开车去') === FALSE) || !(strpos($key, '坐公交') === FALSE)) || !(strpos($key, '步行去') === FALSE)) {
            $this->recordLastRequest($key);
            $key = "LBS";
        }
        else if ($key == '首页' || $key == '主页' || !strcasecmp($key, 'home')) {
            $key = 'home';
        }
        else if ($key == '帮助' || !strcasecmp($key, 'help')) {
            $key = 'help';
        }
        return $key;
    }
    /* 默认只显示第一个相册 */
    // TODO: 显示多个相册缩略图
    public function xiangce()
    {
        $photos = M('Photo')->where(array('token' => $this->token, 'status' => 1))->select();
        if ($photos) {
            $return = array();
            foreach ($photos as $photo) {
                $data['title'] = $photo['title'];
                $data['keyword'] = $photo['info'];
                $data['url'] = rtrim(C('site_url'), '/') . U('Wap/Photo/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName']));
                $data['picurl'] = $photo['picurl'] ? $photo['picurl'] : rtrim(C('site_url'), '/') . '/tpl/static/images/yj.jpg';
                $return[] = array($data['title'], $data['keyword'], $data['picurl'], $data['url']);
            }
        } 
        return array($return, 'news');
        // $photo = M('Photo')->where(array('token' => $this->token, 'status' => 1))->find();
        // $data['title'] = $photo['title'];
        // $data['keyword'] = $photo['info'];
        // $data['url'] = rtrim(C('site_url'), '/') . U('Wap/Photo/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName']));
        // $data['picurl'] = $photo['picurl'] ? $photo['picurl'] : rtrim(C('site_url'), '/') . '/tpl/static/images/yj.jpg';
        // return array(array(array($data['title'], $data['keyword'], $data['picurl'], $data['url'])), 'news');
    }
    /* 关键词“地图” */
    public function companyMap()
    {
        import('Home.Action.MapAction');
        $mapAction = new MapAction();
        return $mapAction->staticCompanyMap();
    }
    /* 关键词“街景” */
    public function jiejing()
    {
        import('Home.Action.MapAction');
        $mapAction = new MapAction();
        return $mapAction->jiejing();
    }
    /* 自定义关键词回复 */
    public function keyword($key)
    {
        Log::write("关键词匹配自动回复:" . $key);
        $like['keyword'] = array('like', ('%' . $key) . '%');
        $like['token'] = $this->token;
        $data = M('keyword')->where($like)->order('id desc')->find();
        if ($data != false) {
            switch ($data['module']) {
            case 'Img':
                $this->requestdata('imgnum');
                $img_db = M('Img');
                $back = $img_db->field('id,text,pic,url,title')->limit(9)->order('id desc')->where($like)->select();
                $idsWhere = 'id in (';
                $comma = '';
                foreach ($back as $keya => $infot) {
                    $idsWhere .= $comma . $infot['id'];
                    $comma = ',';
                    if ($infot['url'] != false) {
                        if (!(strpos($infot['url'], 'http') === FALSE)) {
                            $url = html_entity_decode($infot['url']);
                        } else {
                            $url = $this->getFuncLink($infot['url']);
                        }
                    } else {
                        $url = rtrim(C('site_url'), '/') . U('Wap/Index/content', array('token' => $this->token, 'id' => $infot['id'], 'wecha_id' => $this->data['FromUserName']));
                    }
                    Log::write($infot['title'] . $infot['text']. $infot['pic']. $url, Log::INFO);
                    $return[] = array($infot['title'], $infot['text'], $infot['pic'], $url);
                }
                $idsWhere .= ')';
                if ($back) {
                    $img_db->where($idsWhere)->setInc('click');
                }
                return array($return, 'news');
                break;
            case 'Host':
                $this->requestdata('other');
                $host = M('Host')->where(array('id' => $data['pid']))->find();
                return array(array(array($host['name'], $host['info'], $host['ppicurl'], ((((((C('site_url') . '/index.php?g=Wap&m=Host&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $data['pid']) . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            case 'Estate':
                $this->requestdata('other');
                $Estate = M('Estate')->where(array('id' => $data['pid']))->find();
                return array(array(array($Estate['title'], $Estate['estate_desc'], $Estate['cover'], ((((C('site_url') . '/index.php?g=Wap&m=Estate&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com'), array('楼盘介绍', $Estate['estate_desc'], $Estate['house_banner'], ((((((C('site_url') . '/index.php?g=Wap&m=Estate&a=index&&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $data['pid']) . '&sgssz=mp.weixin.qq.com'), array('专家点评', $Estate['estate_desc'], $Estate['cover'], ((((((C('site_url') . '/index.php?g=Wap&m=Estate&a=impress&&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $data['pid']) . '&sgssz=mp.weixin.qq.com'), array('楼盘3D全景', $Estate['estate_desc'], $Estate['banner'], ((((((C('site_url') . '/index.php?g=Wap&m=Panorama&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $data['pid']) . '&sgssz=mp.weixin.qq.com'), array('楼盘动态', $Estate['estate_desc'], $Estate['house_banner'], ((((((((C('site_url') . '/index.php?g=Wap&m=Index&a=lists&classid=') . $data['classify_id']) . '&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $data['pid']) . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            case 'Reservation':
                if ($key == '维修') {
                    $this->requestdata('other');
                    $rt = M('Reservation')->where(array('id' => $data['pid']))->find();
                    return array(array(array($rt['title'], $rt['info'], $rt['picurl'], ((((((C('site_url') . '/index.php?g=Wap&m=Reservation&a=index&rid=') . $data['pid']) . '&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com')), 'news');
                    break;
                }
                else if ($key == '快递') {
                    $this->requestdata('other');
                    $rt = M('Reservation')->where(array('id' => $data['pid']))->find();
                    return array(array(array($rt['title'], $rt['info'], $rt['picurl'], ((((((C('site_url') . '/index.php?g=Wap&m=Reservation&a=kuaidi&rid=') . $data['pid']) . '&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com')), 'news');
                    break;
                }
            case 'Huodong':
                $this->requestdata('other');
                $rts = M('Huodong')->where(array('token' => $this->token))->select();
                if ($rts) {
                    $return = array();
                    foreach ($rts as $rt) {
                        $return[] = array($rt['title'], $rt['info'], $rt['picurl'], ((((((C('site_url') . '/index.php?g=Wap&m=Reservation&a=huodong&rid=') . $rt['id']) . '&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com');
                    }
                }
                return array($return, 'news');
                break;
            case 'Dingzhi':
                $this->requestdata('other');
                $rts = M('Dingzhi')->where(array('token' => $this->token))->select();
                if ($rts) {
                    $return = array();
                    foreach ($rts as $rt) {
                        $return[] = array($rt['title'], $rt['info'], $rt['picurl'], ((((((C('site_url') . '/index.php?g=Wap&m=Reservation&a=dingzhi&rid=') . $rt['id']) . '&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com');
                    }
                }
                return array($return, 'news');
                break;
            case 'Text':
                $this->requestdata('textnum');
                $info = M($data['module'])->order('id desc')->find($data['pid']);
                return array(htmlspecialchars_decode(str_replace('{wechat_id}', $this->data['FromUserName'], $info['text'])), 'text');
                break;
            case 'Product':
                $this->requestdata('other');
                $infos = M('Product')->limit(9)->order('id desc')->where($like)->select();
                if ($infos) {
                    $return = array();
                    foreach ($infos as $info) {
                        $return[] = array($info['name'], strip_tags(htmlspecialchars_decode($info['intro'])), $info['logourl'], ((((((C('site_url') . '/index.php?g=Wap&m=Product&a=product&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&id=') . $info['id']) . '&sgssz=mp.weixin.qq.com');
                    }
                }
                return array($return, 'news');
                break;
            case 'Selfform':
                $this->requestdata('other');
                $pro = M('Selfform')->where(array('id' => $data['pid']))->find();
                return array(array(array($pro['name'], strip_tags(htmlspecialchars_decode($pro['intro'])), $pro['logourl'], ((((((C('site_url') . '/index.php?g=Wap&m=Selfform&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&id=') . $data['pid']) . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            case 'Panorama':
                $this->requestdata('other');
                $pro = M('Panorama')->where(array('id' => $data['pid']))->find();
                return array(array(array($pro['name'], strip_tags(htmlspecialchars_decode($pro['intro'])), $pro['frontpic'], ((((((C('site_url') . '/index.php?g=Wap&m=Panorama&a=item&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&id=') . $data['pid']) . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            case 'Weidiaoyan':
                $this->requestdata('other');
                $pro = M('Weidiaoyan')->where(array('id' => $data['pid']))->find();
                return array(array(array($pro['name'], strip_tags(htmlspecialchars_decode($pro['intro'])), $pro['logourl'], ((((((C('site_url') . '/index.php?g=Wap&m=Weidiaoyan&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&id=') . $data['pid']) . '&sgssz=mp.weixin.qq.com')), 'news');
                break;
            default:
                $this->requestdata('videonum');
                $info = M($data['module'])->order('id desc')->find($data['pid']);
                return array(array($info['title'], $info['keyword'], $info['musicurl'], $info['hqmusicurl']), 'music');
            }
        }
    }
    
    public function home()
    {
        return $this->shouye();
    }
    public function shouye($name)
    {
        $home = M('Home')->where(array('token' => $this->token))->find();
        if ($home == false) {
            return array('商家未做首页配置，请稍后再试', 'text');
        } else {
            $imgurl = $home['picurl'];
            if ($home['apiurl'] == false) {
                if (!$home['advancetpl']) {
                    $url = ((((rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Index&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com';
                } else {
                    $url = ((((rtrim(C('site_url'), '/') . '/cms/index.php?token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&sgssz=mp.weixin.qq.com';
                }
            } else {
                $url = $home['apiurl'];
            }
        }
        Log::write("Home page:" . $url, Log::INFO);
        return array(array(array($home['title'], $home['info'], $imgurl, $url)), 'news');
    }
    /* 接受用户发送的“附近”指令，1.提示输入【附近+关键词】；2.存储用户输入的关键词nearby_user */
    public function fujin($keyword)
    {
        $keyword = implode('', $keyword);
        if ($keyword == false) {
            return (($this->my . '很难过,无法识别主人的指令,正确使用方法是:输入【附近+关键词】当') . $this->my) . '提醒您输入地理位置的时候就OK啦';
        }
        $data = array();
        $data['time'] = time();
        $data['token'] = $this->_get('token');
        $data['keyword'] = $keyword;
        $data['uid'] = $this->data['FromUserName'];
        $re = M('Nearby_user');
        $user = $re->where(array('token' => $this->_get('token'), 'uid' => $data['uid']))->find();
        if ($user == false) {
            $re->data($data)->add();
        } else {
            $id['id'] = $user['id'];
            $re->where($id)->save($data);
        }
        return ('主人【' . $this->my) . '】已经接收到你的指令n请发送您的地理位置给我哈';
    }
    /* 保存或更新用户最近的一次请求,text, location ... */
    public function recordLastRequest($key, $msgtype = 'text')
    {
        $rdata = array();
        $rdata['time'] = time();
        $rdata['token'] = $this->_get('token');
        $rdata['keyword'] = $key;
        $rdata['msgtype'] = $msgtype;
        $rdata['uid'] = $this->data['FromUserName'];
        $user_request_model = M('User_request');
        $user_request_row = $user_request_model->where(array('token' => $this->_get('token'), 'msgtype' => $msgtype, 'uid' => $rdata['uid']))->find();
        if (!$user_request_row) {
            $user_request_model->add($rdata);
        } else {
            $rid['id'] = $user_request_row['id'];
            $user_request_model->where($rid)->save($rdata);
        }
    }
    public function map($x, $y)
    {
        $user_request_model = M('User_request');
        $user_request_row = $user_request_model->where(array('token' => $this->_get('token'), 'msgtype' => 'text', 'uid' => $this->data['FromUserName']))->find();
        // 情况一：查找附近的公交、餐馆之类
        if (!(strpos($user_request_row['keyword'], '附近') === FALSE)) {
            $user = M('Nearby_user')->where(array('token' => $this->_get('token'), 'uid' => $this->data['FromUserName']))->find();
            $keyword = $user['keyword'];
            $radius = 2000;
            $str = file_get_contents((((((C('site_url') . '/map.php?keyword=') . urlencode($keyword)) . '&x=') . $x) . '&y=') . $y);
            $array = json_decode($str);
            $map = array();
            foreach ($array as $key => $vo) {
                $map[] = array($vo->title, $key, rtrim(C('site_url'), '/') . '/tpl/static/images/home.jpg', $vo->url);
            }
            return array($map, 'news');
        }
        // 情况二：如果设置了公司的地址（经纬度），那么用户输入自身的经纬度后可以指引用户如何到达公司
        else {
            import('Home.Action.MapAction');
            $mapAction = new MapAction();
            if ((!(strpos($user_request_row['keyword'], '开车去') === FALSE) || !(strpos($user_request_row['keyword'], '坐公交') === FALSE)) || !(strpos($user_request_row['keyword'], '步行去') === FALSE)) {
                if (!(strpos($user_request_row['keyword'], '步行去') === FALSE)) {
                    $companyid = str_replace('步行去', '', $user_request_row['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->walk($x, $y, $companyid);
                }
                if (!(strpos($user_request_row['keyword'], '开车去') === FALSE)) {
                    $companyid = str_replace('开车去', '', $user_request_row['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->drive($x, $y, $companyid);
                }
                if (!(strpos($user_request_row['keyword'], '坐公交') === FALSE)) {
                    $companyid = str_replace('坐公交', '', $user_request_row['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->bus($x, $y, $companyid);
                }
            }
            // 公司存在多个分支机构，用户输入“最近的”或者“lbs”，那么返回最近的机构，用户还需要输入一次位置
            else {
                switch ($user_request_row['keyword']) {
                case '最近的':
                    return $mapAction->nearest($x, $y);
                    break;
                }
            }
        }
    }
    /* 调用twototwo公交查询接口，返回公交路线 */
    // TODO: 替换原来的key
    public function gongjiao($data)
    {
        $data = array_merge($data);
        if (count($data) != 2) {
            $this->error_msg();
            return false;
        }
        $json = file_get_contents((('http://www.twototwo.cn/bus/Service.aspx?format=json&action=QueryBusByLine&key=c3e2c03e-4a93-41f0-8ebe-dbadd7ea7858&zone=' . $data[0]) . '&line=') . $data[1]);
        $data = json_decode($json);
        $xianlu = $data->Response->Head->XianLu;
        $xdata = get_object_vars($xianlu->ShouMoBanShiJian);
        $xdata = $xdata['#cdata-section'];
        $piaojia = get_object_vars($xianlu->PiaoJia);
        $xdata = ($xdata . '') . $piaojia['#cdata-section'];
        $main = $data->Response->Main->Item->FangXiang;
        $xianlu = $main[0]->ZhanDian;
        $str = $xdata;
        $str .= '' . '【本公交途经】';
        for ($i = 0; $i < count($xianlu); $i++) {
            $str .= ('' . $i) . trim($xianlu[$i]->ZhanDianMingCheng);
        }
        return $str;
    }
    public function huoche($data, $time = '')
    {
        $data = array_merge($data);
        $data[2] = date('Y', time()) . $time;
        if (count($data) != 3) {
            $this->error_msg(($data[0] . '至') . $data[1]);
            return false;
        }
        $time = empty($time) ? date('Y-m-d', time()) : date('Y-', time()) . $time;
        $json = file_get_contents(((((('http://www.twototwo.cn/train/Service.aspx?format=json&action=QueryTrainScheduleByTwoStation&key=c3e2c03e-4a93-41f0-8ebe-dbadd7ea7858&startStation=' . $data[0]) . '&arriveStation=') . $data[1]) . '&startDate=') . $data[2]) . '&ignoreStartDate=0&like=1&more=0');
        if ($json) {
            $data = json_decode($json);
            $main = $data->Response->Main->Item;
            if (count($main) > 10) {
                $conunt = 10;
            } else {
                $conunt = count($main);
            }
            for ($i = 0; $i < $conunt; $i++) {
                $str .= ((((((((('n 【编号】' . $main[$i]->CheCiMingCheng) . 'n 【类型】') . $main[$i]->CheXingMingCheng) . 'n【发车时间】:　') . $time) . ' ') . $main[$i]->FaShi) . 'n【耗时】') . $main[$i]->LiShi) . ' 小时';
                $str .= 'n----------------------';
            }
        } else {
            $str = ((('没有找到 ' . $name) . ' 至 ') . $toname) . ' 的列车';
        }
        return $str;
    }
    public function fistMe($data)
    {
        if ('event' == $data['MsgType'] && 'subscribe' == $data['Event']) {
            return $this->help();
        }
    }
    public function help()
    {
        $data = M('Areply')->where(array('token' => $this->token))->find();
        if ($data['content'] == "") {
            $data['content'] = "亲，尚未定义帮助信息~";
        }
        return array(preg_replace('/(1512)|(15)|(12)/', 'n', $data['content']), 'text');
    }
    public function error_msg($data)
    {
        return ('没有找到' . $data) . '相关的数据';
    }
    /* 查看用户的等级，请求次数是否到期 */
    public function user($action, $keyword = '')
    {
        $user = M('Wxuser')->field('uid')->where(array('token' => $this->token))->find();
        $usersdata = M('Users');
        $dataarray = array('id' => $user['uid']);
        $users = $usersdata->field('gid,diynum,connectnum,activitynum,viptime')->where(array('id' => $user['uid']))->find();
        $group = M('User_group')->where(array('id' => $users['gid']))->find();
        if ($users['diynum'] < $group['diynum']) {
            $data['diynum'] = 1;
            if ($action == 'diynum') {
                $usersdata->where($dataarray)->setInc('diynum');
            }
        }
        if ($users['connectnum'] < $group['connectnum']) {
            $data['connectnum'] = 1;
            if ($action == 'connectnum') {
                $usersdata->where($dataarray)->setInc('connectnum');
            }
        }
        if ($users['viptime'] > time()) {
            $data['viptime'] = 1;
        }
        return $data;
    }
    /* 记录每天各种类型数据的访问次数 */
    public function requestdata($field)
    {
        $data['year'] = date('Y');
        $data['month'] = date('m');
        $data['day'] = date('d');
        $data['token'] = $this->token;
        $mysql = M('Requestdata');
        $check = $mysql->field('id')->where($data)->find();
        if ($check == false) {
            $data['time'] = time();
            $data[$field] = 1;
            $mysql->add($data);
        } else {
            $mysql->where($data)->setInc($field);
        }
    }
    /* 简易中文分词，http://www.xunsearch.com/scws/ */
    public function get_tags($title, $num = 10)
    {
        vendor('Pscws.Pscws4', '', '.class.php');
        $pscws = new PSCWS4();
        $pscws->set_dict(CONF_PATH . 'etc/dict.utf8.xdb');
        $pscws->set_rule(CONF_PATH . 'etc/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops($num);
        $pscws->close();
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        return implode(',', $tags);
    }
    // TODO: 下面的函数也许可以删掉
    public function getFuncLink($u)
    {
        error_log("getFuncLink");
        $urlInfos = explode(' ', $u);
        switch ($urlInfos[0]) {
        default:
            $url = str_replace('{wechat_id}', $this->data['FromUserName'], $urlInfos[0]);
            break;
        case '刮刮卡':
            $Lottery = M('Lottery')->where(array('token' => $this->token, 'type' => 2, 'status' => 1))->order('id DESC')->find();
            $url = C('site_url') . U('Wap/Guajiang/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $Lottery['id']));
            break;
        case '大转盘':
            $Lottery = M('Lottery')->where(array('token' => $this->token, 'type' => 1, 'status' => 1))->order('id DESC')->find();
            $url = C('site_url') . U('Wap/Lottery/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $Lottery['id']));
            break;
        case '商家订单':
            $url = ((((((C('site_url') . '/index.php?g=Wap&m=Host&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName']) . '&hid=') . $urlInfos[1]) . '&sgssz=mp.weixin.qq.com';
            break;
        case '万能表单':
            $url = C('site_url') . U('Wap/Selfform/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $urlInfos[1]));
            break;
        case '微调研':
            $url = C('site_url') . U('Wap/Weidiaoyan/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $urlInfos[1]));
            break;
        case '会员卡':
            $url = C('site_url') . U('Wap/Card/vip', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName']));
            break;
        case '首页':
            $url = (((rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Index&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName'];
            break;
        case '团购':
            $url = (((rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Groupon&a=grouponIndex&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName'];
            break;
        case '商城':
            $url = (((rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Product&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName'];
            break;
        case '订餐':
            $url = (((rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Product&a=dining&dining=1&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName'];
            break;
        case '相册':
            $url = (((rtrim(C('site_url'), '/') . '/index.php?g=Wap&m=Photo&a=index&token=') . $this->token) . '&wecha_id=') . $this->data['FromUserName'];
            break;
        case '网站分类':
            $url = C('site_url') . U('Wap/Index/lists', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'classid' => $urlInfos[1]));
            break;
        case 'LBS信息':
            if ($urlInfos[1]) {
                $url = C('site_url') . U('Wap/Company/map', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'companyid' => $urlInfos[1]));
            } else {
                $url = C('site_url') . U('Wap/Company/map', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName']));
            }
            break;
        case 'DIY宣传页':
            $url = (C('site_url') . '/index.php/show/') . $this->token;
            break;
        case '婚庆喜帖':
            $url = C('site_url') . U('Wap/Wedding/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $urlInfos[1]));
            break;
        case '投票':
            $url = C('site_url') . U('Wap/Vote/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $urlInfos[1]));
            break;
        case '喜帖':
            $url = C('site_url') . U('Wap/Wedding/index', array('token' => $this->token, 'wecha_id' => $this->data['FromUserName'], 'id' => $urlInfos[1]));
            break;
        }
        return $url;
    }
}

?>