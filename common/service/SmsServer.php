<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/13
 * Time: 上午11:04
 */
namespace common\service;

use yii\base\Exception;

class SmsServer{

    const SMS_REGISTER_CODE = '【明德信】亲爱的%s，您的验证码是 %s 。如非本人操作，请忽略本短信';
    const SMS_INCOME = '【明德信】尊敬的用户您投资的壹计划第%s期收益已经到账，详情请到个人中心查看。如有疑问，请致电客服电话400-062-9239。';
    const SMS_RESET_PASSWORD = '【明德信】尊敬的用户，您正在进行修改登录密码操作，请不要把验证码泄露给任何人。您的验证码是 %s 。如非本人操作，请忽略此短信。';



    /**
     * @var string 短信商
     */
    private $provider = 'yunpian';

    /**
     * @var array 服务商接口列表
     */
    private $_providerMap = [

        'yunpian' => [
            'class'=>'common\service\sms\Yunpian',
            'url'  =>'http://yunpian.com/v1/',
            'key'  =>'ca8190fdb73c1d798854a161feacf48a'
        ],
    ];

    /**
     * @var server
     */
    private $_server;

    /**
     * @param $phone
     * @param $msg
     * @return mixed
     */
    public function send($phone,$msg){
        try{
            $provider = $this->_providerMap[$this->provider];
            $this->_server = new $provider['class']($provider['url'], $provider['key']);
            if($this->_server->send($phone, $msg)){
                return ['code'=>1,'msg'=>'短信发送成功'];
            }
        }catch (Exception $e){
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
        return ['code'=>0,'msg'=>'短信发送失败'];

    }


    /**
     * 注册
     * @param $phone
     * @param $code
     * @return mixed
     */
    public function sendRegisterCodeSms($phone,$code){

        $msg = sprintf(self::SMS_REGISTER_CODE, "用户", $code);
        /** @var TYPE_NAME $msg */
        return $this->send($phone, $msg);

    }







}