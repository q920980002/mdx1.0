<?php

namespace common\service\sms;

use common\service\PublicService;
use Yii;
use yii\log;
use yii\base\Exception;
/**
 * Class Yunpian
 * @package common\components\sms
 */
class Yunpian implements SmsInterface
{
    private $_domain;
    private $_key;

    public function __construct($url, $key)
    {
        $this->_domain = $url;
        $this->_key = $key;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function accountInfo()
    {
        $req_url = 'user/get.json';
        $info = $this->_requestUrl($req_url);
        return $info['user'];
    }

    /**
     * 修改账户信息
     * @param string $user 紧急联系人
     * @param string $mobile 紧急联系人
     * @param int $alarm 短信余额提醒阈值。一天只提示一次
     * @return bool
     */
    public function accountModify($user = '', $mobile = '', $alarm = 100)
    {
        $req_url = 'user/set.json';
        $data = array(
            'emergency_contact' => $user,
            'emergency_mobile' => $mobile,
            'alarm_balance' => $alarm
        );
        $this->_requestUrl($req_url, $data);
        return true;
    }

    /**
     * 取默认模板
     * @param int $tpl_id 模板id，64位长整形。指定id时返回id对应的默认模板。未指定时返回所有默认模板
     * @return mixed
     */
    public function getDefaultSmsTemplate($tpl_id = 0)
    {
        $req_url = 'tpl/get_default.json';
        $data = array('tpl_id' => $tpl_id);
        $template = $this->_requestUrl($req_url, $data)['template'];
        return $this->_tplAnalysis($template, $tpl_id ? false : true);
    }

    /**
     * 添加模板
     * @param string $tpl_content 模板内容，必须以带符号【】的签名结尾
     * @param int $notify 审核结果短信通知的方式:0表示需要通知,默认;1表示仅审核不通过时通知;2表示仅审核通过时通知;3表示不需要通知
     * @return mixed
     */
    public function addSmsTemplate($tpl_content, $notify = 3)
    {
        $req_url = 'tpl/add.json';
        $data = array(
            'tpl_content' => sprintf('%s【明德信】', $tpl_content),
            'notify_type' => $notify
        );
        $template = $this->_requestUrl($req_url, $data)['template'];
        return $this->_tplAnalysis($template);
    }

    /**
     * 取模板
     * @param int $tpl_id 模板id，64位长整形。指定id时返回id对应的模板。未指定时返回所有模板
     * @return mixed
     */
    public function getSmsTemplate($tpl_id = 0)
    {
        $req_url = 'get.json';
        $data = array('tpl_id' => $tpl_id);
        $template = $this->_requestUrl($req_url, $data)['template'];
        return $this->_tplAnalysis($template, $tpl_id ? false : true);
    }

    /**
     * 修改模板
     * @param int $tpl_id 模板id，64位长整形
     * @param string $tpl_content 模板内容，必须以带符号【】的签名结尾
     * @return mixed
     */
    public function tplModify($tpl_id, $tpl_content)
    {
        $req_url = 'tpl/update.json';
        $data = array(
            'tpl_id' => $tpl_id,
            'tpl_content' => $tpl_content
        );
        $template = $this->_requestUrl($req_url, $data)['template'];
        return $this->_tplAnalysis($template);
    }

    /**
     * 删除模板
     * @param int $tpl_id 模板id，64位长整形
     * @return mixed
     */
    public function tplDelete($tpl_id)
    {
        $req_url = 'tpl/del.json';
        $data = array('tpl_id' => $tpl_id);
        if ($this->_requestUrl($req_url, $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 发短信
     * @param array|int $mobile 接收的手机号;发送多个手机号请以逗号分隔，一次不要超过100条
     * @param string $text 短信内容
     * @return mixed
     */
    public function send($mobile, $text)
    {
        $req_url = 'sms/send.json';
        if (is_array($mobile)) {
            $groups = array_chunk($mobile, 100);
            $res = array();
            foreach ($groups as $group) {
                $mobiles = join(',', $group);
                $data = array(
                    'mobile' => $mobiles,
                    'text' => $text
                );
                $res += $this->_requestUrl($req_url, $data)['result'];
            }
        } else {
            $data = array(
                'mobile' => $mobile,
                'text' => $text
            );
            $res = $this->_requestUrl($req_url, $data)['result'];
        }
        return $res;
    }

    /**
     * 模板接口发短信（不推荐使用）
     * @param int $mobile 接收的手机号
     * @param int $tpl_id 模板id
     * @param string $tpl_value 变量名和变量值对。如果你的变量名或者变量值中带有#&=%中的任意一个特殊符号，请先分别进行urlencode编码后再传递
     * @return mixed
     */
    public function sendTpl($mobile, $tpl_id, $tpl_value)
    {
        $req_url = 'sms/tpl_send.json';
        $data = array(
            'mobile' => $mobile,
            'tpl_id' => $tpl_id,
            'tpl_value' => $tpl_value
        );
        return $this->_requestUrl($req_url, $data)['result'];
    }

    /**
     * 获取72小时内状态报告数据
     * @param int $page_size 每页个数，最大100个，默认20个
     * @return array
     */
    public function smsStatus($page_size = 20)
    {
        $req_url = 'sms/pull_status.json';
        $data = array('page_size' => $page_size);
        $res = $this->_requestUrl($req_url, $data)['sms_status'];
        return $this->_smsStatusAnalysis($res);
    }

    /**
     * 获取72小时内回复短信数据
     * @param int $page_size 每页个数，最大100个，默认20个
     * @return mixed
     */
    public function smsReply($page_size = 20)
    {
        $req_url = 'sms/pull_status.json';
        $data = array('page_size' => $page_size);
        $res = $this->_requestUrl($req_url, $data)['sms_reply'];
        return array(
            'mobile' => $res['mobile'],
            'time' => $res['reply_time'],
            'content' => $res['text']
        );
    }

    /**
     * 接收云片实时推送的状态报告
     * @param $data string
     * @return bool|mixed
     */
    public function receiveSmsStatusReport($data)
    {
        $report_data = json_decode(urldecode($data), true);
        if (!$report_data) {
            return false;
        }
        return $this->_smsStatusAnalysis($report_data);
    }

    /**
     * 接收云片实时推送的回复短信，单个手机数据
     * @param $data string
     * @return bool|mixed
     */
    public function receiveSmsReplyReport($data)
    {
        $report_data = json_decode(urldecode($data), true);
        if (!$report_data) {
            return false;
        }
        return array(
            'id' => $report_data['id'],
            'mobile' => $report_data['mobile'],
            'time' => $report_data['reply_time'],
            'text' => $report_data['text']
        );
    }

    /**
     * 查屏蔽词
     * @param $text string
     * @return mixed
     */
    public function getBlackWords($text)
    {
        $req_url = 'sms/get_black_word.json';
        $data = array('text' => $text);
        $stop_words = $this->_requestUrl($req_url, $data)['result']['black_word'];
        return array('black_word' => explode(',', $stop_words));
    }

    /**
     * 查回复的短信
     * @param string $start_time 短信回复开始时间
     * @param string $end_time 短信回复结束时间
     * @param int $page_num 页码，从1开始
     * @param int $page_size 每页个数，最大100个
     * @param string $mobile 填写时只查该手机号的回复，不填时查所有的回复
     * @return mixed
     */
    public function getSmsReply($start_time, $end_time = '', $page_num = 1, $page_size = 20, $mobile = '')
    {
        $req_url = 'sms/get_reply.json';
        $data = array(
            'start_time' => $start_time,
            'end_time' => $end_time ?: date('Y-m-d h:i:s'),
            'page_num' => $page_num,
            'page_size' => $page_size,
            'mobile' => $mobile
        );
        $res = $this->_requestUrl($req_url, $data)['sms_reply'];
        $reply = array();
        foreach ($res as $rpl) {
            $rpl_sms = array('text' => $res['text'], 'time' => $res['reply_time']);
            if (!$mobile) {
                $rpl_sms['mobile'] = array('mobile' => $res['mobile']);
            }
            $reply[] = $rpl_sms;
        }
        return $reply;
    }

    /**
     * 发语音验证码，通过电话直呼到用户手机并语音播放验证码，默认最多播放2次
     * @param int $mobile 接收的手机号、固话（需加区号）或400电话
     * @param int $code 验证码，支持4~6位阿拉伯数字
     * @return mixed
     */
    public function sendVoice($mobile, $code)
    {
        $req_url = 'voice/send.json';
        $data = array(
            'mobile' => $mobile,
            'code' => $code
        );
        return $this->_requestUrl($req_url, $data)['result'];
    }

    /**
     * 模版接口返回数据分析
     * @param array $template 结果数据
     * @param bool $is_arr
     * @return array
     */
    private function _tplAnalysis($template, $is_arr = false)
    {
        if (!$is_arr) {
            if ($template['check_status'] == 'SUCCESS') {
                $res = array('status' => '1', 'content' => $template['tpl_content']);
            } elseif ($template['check_status'] == 'CHECKING') {
                $res = array('status' => '0', 'reason' => '审核中');
            } else {
                $res = array('status' => '0', 'reason' => $template['reason']);
            }
        } else {
            foreach ($template as $tpl) {
                if ($template['check_status'] == 'SUCCESS') {
                    $succ[$tpl['tpl_id']] = $tpl['tpl_content'];
                } elseif ($template['check_status'] == 'CHECKING') {
                    $fail[$tpl['tpl_id']] = '审核中';
                } else {
                    $fail[$tpl['tpl_id']] = $tpl['reason'];
                }
            }
            $res = array(
                array('status' => '1', 'content' => $succ),
                array('status' => '0', 'content' => $fail),
            );
        }
        return $res;
    }

    /**
     * 状态结果数据分析
     * @param array $reports 状态结果数据
     * @return array
     */
    private function _smsStatusAnalysis($reports)
    {
        foreach ($reports as $rpt) {
            if ($rpt['report_status'] == 'SUCCESS') {
                $succ[$rpt['sid']] = ['time' => $rpt['user_receive_time'], 'mobile' => $rpt['mobile']];
            } elseif ($rpt['report_status'] == 'FAIL') {
                $fail[$rpt['sid']] = ['time' => $rpt['user_receive_time'], 'error_msg' => $rpt['error_msg'], 'mobile' => $rpt['mobile']];
            } else {
                $unknown[$rpt['sid']] = $rpt;
            }
        }
        return array('success' => $succ, 'fail' => $fail, 'unknown' => $unknown);
    }

    /**
     * 统一短信请求访问函数
     * @param $req_url
     * @param array $data
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    private function _requestUrl($req_url, $data = [], $method = 'POST')
    {
        foreach ($data as $k => $v) {
            if (empty($v)) {
                unset($data[$k]);
            }
        }


        $url = $this->_domain . $req_url;
        $req_data = $data + array('apikey' => $this->_key);
        $res = json_decode(PublicService::curl($url, $req_data, $method), true);
        $code = $res['code'];
        if ($code == 0) { //正确返回。可以从api返回的对应字段中取数据。
            return $res;
        } else {
            //code > 0: 调用API时发生错误，需要开发者进行相应的处理。
            //-50 < code <= -1: 权限验证失败，需要开发者进行相应的处理。
            //code <= -50: 系统内部错误，请联系技术支持，调查问题原因并获得解决方案。
            if ($code > 0) {
                $msg = '调用API时发生错误';
                $level = log\Logger::LEVEL_ERROR;
            } elseif ($code > -50 && $code < 0) {
                $msg = '权限验证失败';
                $level = log\Logger::LEVEL_WARNING;
            } else {
                $msg = '系统内部错误';
                $level = log\Logger::LEVEL_ERROR;
            }

            PublicService::log(sprintf('短信发送出现异常，请检查。异常返回码:%d;错误类型:%s;异常信息:%s，%s',
                $code, $msg, $res['msg'], $res['detail']), $level);
            throw new Exception(sprintf('短信发送出现异常，请检查。异常返回码：%d异常信息：%s，%s',
                $code, $res['msg'], $res['detail']));
        }
    }
}
