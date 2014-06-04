<?php
class WeixinChangeju {
	public static function handler($data){
		// 接受图片，TODO：判断ToUserName是否是畅e居的公众号
		if ('image' == $data['MsgType']) {
			return WeixinChangeju::imageHandler($data);
		} else if ('text' == $data['MsgType'] || 'event' == $data['MsgType']) {
			return WeixinChangeju::textHandler($data);
		}
	}

	public static function imageHandler($data) {
		$from = $data['FromUserName'];
		$create_time = $data['CreateTime'];

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

	public static function textHandler($data) {
		$from = $data['FromUserName'];
		$create_time = $data['CreateTime'];

	    $content = 'text' == $data['MsgType'] ? $data['Content'] : $data['EventKey'];
	    Log::write($content, Log::INFO);
        if($content == 't') {
            $return = array();
            $return[] = array("测试", "测试", "", 'http://192.168.1.106:8080/changeju/pub');
            return array($return, 'news');
        } else if ($content == '区域') {
            $url = C('CHANGEJU_API_HOST').'api/hotarea';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            $rts = null;
            $return = array();

            $return[] = array('随便看看', '随便看看', "http://static.changeju.com/resources/themes/default/images/logo_c2d8a095.png", C('CHANGEJU_HOST').'changzu/');

            if (!empty($data)){
                $rts = json_decode($data);
                
                foreach ($rts as $k => $v) {
                    $return[] = array($v, $k, "http://image.imethodz.com/big/house/2014/05/5368a3ed63f8d.jpg", C('CHANGEJU_HOST').'changzu/s1,qy-'.$k);
                }
            }
            return array($return, 'news');
        } else if ($content == '地铁') {

            $url = C('CHANGEJU_API_HOST').'api/hotline';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            $rts = null;
            $return = array();

            if (!empty($data)){
                $rts = json_decode($data);
                
                foreach ($rts as $k => $v) {
                    $return[] = array($v, $k, "http://image.imethodz.com/big/house/2014/05/5368a3ed63f8d.jpg", C('CHANGEJU_HOST').'changzu/s1,qy-'.$k);
                }
            }

            $return[] = array('没有找到合适的区域？点击查看更多...', '随便看看', "http://static.changeju.com/resources/themes/default/images/logo_c2d8a095.png", C('CHANGEJU_HOST').'changzu/');

            return array($return, 'news');

        } else if ($content == '发布') {
            $return = array();
            $return[] = array("轻松发布房源", "只需几步填写表单,即可轻松发布房源", "", "http://www.changeju.com/pub?wechat_id=" . $from);
            return array($return, 'news');
        } else if (strpos($content, "找房") === 0) {
            $content = str_replace("找房", "", $content);
            $content = urlencode($content);
            Log::write('Keyword: '.$content);
            $url = C('CHANGEJU_HOST').'search?keywords=' . $content;
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
        } else if (strpos($content, "发布") === 0) {
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
        } else {
        	Log::write($content.' not binded!');
        	return array('正在建设中！', 'text');
        }
	}
}
?>