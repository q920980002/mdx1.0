<?php



namespace common\service;




use Yii;
use yii\base\Exception;
use yii\log;


class PublicService
{
    /**
     * 数组转xml
     * @param array $arr 需转化的数组
     * @param int $dom
     * @param mixed $item
     * @return string
     */
    public static function arrToXml($arr, $dom = 0, $item = null)
    {


        if (!$dom) {
            $dom = new \DOMDocument("1.0", "UTF-8");
        }

        if (!$item) {
            $root = 'root';
            if (is_array(current($arr))) {
                $root = key($arr);
                $arr = current($arr);
            }
            $item = $dom->createElement($root);
            $dom->appendChild($item);
        }

        foreach ($arr as $key => $val) {
            $new_item = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($new_item);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $new_item->appendChild($text);
            } else {
                self::arrToXml($val, $dom, $new_item);
            }
        }
        return $dom->saveXML();
    }

    public static function xmlToArr($xml)
    {
        $arr = simplexml_load_string($xml);
        $new_arr = array();
        $arr = (array)$arr;
        foreach ($arr as $key => $val) {
            if (!is_string($val)) {
                $val = (array)$val;
            }
            $new_arr[$key] = $val;
        }
        return $new_arr;
    }

    /**
     * 单个curl远程访问请求
     * @param $url
     * @param $data
     * @param string $method
     * @return bool|mixed
     */
    public static function curl($url, $data, $method = 'POST')
    {
        if (is_array($data)) {
            $req_str = http_build_query($data);
        } else {
            $req_str = $data;
        }
        if ($method !== 'POST' && $data) {
            $url .= '?' . $req_str;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36');
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_str);
        }
        $res = curl_exec($ch);
        $resCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (($resCode > 399 && $resCode < 500) || $err_no = curl_errno($ch)) {
            Yii::getLogger()->log(sprintf("远程请求错误，返回代码：%s, 错误代码：%s", $resCode, $err_no),
                log\Logger::LEVEL_ERROR);
            self::log(sprintf("远程请求错误，返回代码：%s, 错误代码：%s", $resCode, $err_no));
            return false;
        }

        curl_close($ch);
        return $res;
    }

    public static function curlMulti($curls, $method = 'POST')
    {
        $handle = array();
        $mul_data = array();
        $mh = curl_multi_init();
        foreach ($curls as $i => $uri) {
            $ch = curl_init();
            if ($method != 'POST' && $uri['data']) {
                $uri .= '?' . $uri['data'];
            }
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36');
            if ($method == 'POST' && $uri['data']) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($uri['data']));
            }
            curl_multi_add_handle($mh, $ch);
            $handle[] = $ch;
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach ($handle as $i => $ch) {
            $content = curl_multi_getcontent($ch);
            $mul_data[$i] = (curl_errno($ch) == 0) ? $content : false;
        }

        foreach ($handle as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);
        return $mul_data;
    }

    public static function sendMail($mailto, $sub, $content, $from = '', $template = '')
    {
        if (empty($from)) {
            $from = Config::getConfig('1ludai')['mail']['kefu'];//默认用客服邮箱
        }
        //检查邮箱格式
        if (!self::_mailCheck($from)) {
            throw new Exception('发件人邮箱格式不匹配');
        }
        $header = sprintf("From: %s", $from);

        if (is_array($mailto)) {
            if (!isset($mailto['bc'])) {
                throw new Exception('无收件人邮箱地址');
            }
            $to = $mailto['to'];
            if (isset($mailto['bc'])) {
                if (!self::_mailCheck($mailto['bc'])) {
                    throw new Exception('抄送人邮箱格式不匹配');
                }
                $bc = $mailto['bc'];
                $header .= "\r\n" . sprintf("BC: %s", $bc);
            }
            if (isset($mailto['cc'])) {
                if (!self::_mailCheck($mailto['cc'])) {
                    throw new Exception('密送人邮箱格式不匹配');
                }
                $cc = $mailto['cc'];
                $header .= "\r\n" . sprintf("CC: %s", $cc);
            }
        } else {
            $to = $mailto;
        }
        if (!self::_mailCheck($to)) {
            throw new Exception('收件人邮箱格式不匹配');
        }

        //邮件模板为空
        if (empty($template)) {
            $msg = $content;
        } else {
            $msg = '';
        }
        mail($to, $sub, $msg, $header);
    }

    private function _mailCheck($mail)
    {
        if (preg_match("/^[0-9a-zA-Z]+[0-9a-zA-Z_]*@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $mail)) {
            return true;
        } else {
            return false;
        }
    }

    public static function log($log, $level = log\Logger::LEVEL_ERROR)
    {
        Yii::getLogger()->log($log, $level);
    }

    /**
     * 判断是否为邮箱, 短期内将删除该方法
     * @deprecated 请不要再使用该函数,应直接使用 filter_var($mail, FILTER_VALIDATE_MAIL);
     * @ref http://php.net/manual/en/function.filter-var.php
     * @param strign $mail
     * @return bool
     */
    public static function checkMail($mail)
    {
        return (boolean)filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     *
     * 判断是否为手机号
     * @param $phone
     * @return bool
     */
    public static function checkPhone($phone)
    {
        if (!preg_match('/^(1[3|5|7|8][0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$/', $phone)) {
            return false;
        }

        return true;
    }

    /**
     * 判断是否为金额, 2位小数, 不包含千分位,
     * @warning ".11" 将返回假
     * @param $money
     * @return int|boolean
     */
    public static function checkMoney($money)
    {
        return preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $money);
    }

    /**
     * MD5加密密码
     */
    public static function md5($password)
    {
        $crypt = md5($password);
        return substr(md5(substr($crypt, 4, 24) . substr($crypt, 0, 4)), 4, 24);
    }

    /**
     * 生成流水号
     */

    public static function createRequestNo()
    {
        //获取日期（方便统计）
        $date = date("Ymd");

        //获取当前时间微秒(确保唯一)
        list($usec, $sec) = explode(" ", microtime());

        $usec = substr((string)$usec, 2, 6);
        $current = $usec . (string)$sec;

        //生成随机数(确保唯一)
        $rand = mt_rand(10000000, 99999999);

        return $date . $rand . $current;
    }

}