<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/20
 * Time: 下午1:49
 */
namespace frontend\service;

use common\chinapnr\chinaPnrPay;
use common\models\AccountAuth;
use common\models\Passport;
use common\models\PnrpayError;
use common\models\ProvinceCity;
use common\service\CheckService;
use yii\base\ErrorException;
use yii\base\Exception;

class ChinapnrService {


    public $pnrpayService;


    /**
     * 构造函数
     */
    public function __construct(){
        $this->pnrpayService = new chinaPnrPay();
    }

    /**
     * 实名认证
     * @param $account_id
     * @param $passport_id
     * @param $name
     * @param $idCardNo
     * @return array
     */
    public function authName($account_id,$passport_id,$name,$idCardNo){

        $passport = Passport::findOne(array('id'=>$passport_id));
        /**
         * 检查参数
         */
        $res = $this->_authCheck($account_id,$name,$idCardNo);
        if($res['code'] == 0){
            return $res;
        }

        //实名认证
        $this->_authName($account_id,$name,$idCardNo);

        //开通汇付账户
        return $this->_setUpChinapnrAccount($account_id,$passport->phone,$name,$idCardNo);


    }

    /**
     *
     * @param $account_id
     * @param $bankCode
     * @param $cardNo
     * @param $phone
     * @param $cityCode
     * @return array
     */
    public function bindBank($account_id,$bankCode,$cardNo,$phone,$cityCode){

        /**
         * 检查参数
         */
        $res = $this->_bindBankCheck($account_id,$bankCode,$cardNo,$phone,$cityCode);
        if($res['code'] == 0){
            return $res;
        }

        $accountauth = AccountAuth::find()->where(['account_id'=>$account_id])->one();
        //组合绑卡接口数据
        $dataR = array(
            'OperId' => $accountauth->auth_user_number,
            'LoginPwd' => $accountauth->auth_user_pwd,
            'CardNo' => $cardNo,
            'OpenAcctName' => $accountauth->name,
            'BankCode' => $bankCode,
            'CertId' => $accountauth->id_number,
            'UsrMp' => $phone,
        );


        $cityInfo = ProvinceCity::findOne(array('city_code' => $cityCode));
        $dataD = array(
            'OperId' => $accountauth->auth_user_number,
            'Password' => $accountauth->auth_user_pwd,
            'OpenAcctId' => $cardNo,
            'OpenAcctName' => $accountauth->name,
            'OpenBankId' => $bankCode,
            'OpenProvId' => $cityInfo->province_code,
            'OpenAreaId' => $cityInfo->city_code,
        );

        try{
            $this->pnrpayService->bindRechargeCard($dataR);
            $this->pnrpayService->bindCashCard($dataD);

            //添加绑卡数据
            $this->_addBindBank($account_id,$bankCode,$cardNo,$phone,$cityCode);

            return ['code'=>0,'msg'=>"绑卡成功!"];
        }catch (Exception $e){

            PnrPayError::addErrorRecord($account_id,$e->getMessage());
            return ['code'=>0,'msg'=>$e->getMessage()];
        }




    }

    private function _addBindBank(){




    }

    /**
     *
     * 汇付开户
     * @param $account_id
     * @param $phone
     * @param $name
     * @param $idCardNo
     * @return array
     */
    private function _setUpChinapnrAccount($account_id,$phone,$name,$idCardNo){


        //汇付操作员id
        $usrId = $this->_createUsrId($phone);
        //汇付操作员密码
        $pwd = md5($phone . rand(10, 99));
        //组合开户信息
        $data = array(
            'MerUsrId' => $usrId,
            'UsrMp' => $phone,
            'IdNo' => $idCardNo,
            'UsrName' => $name,
            'UsrPwd' => $pwd,
            'UsrShortName' => $name,
            'UsrId' => $usrId,
        );
        try {
            //调用开户接口
            $this->pnrpayService->createChinapnrAccount($data);

        }catch (Exception $e){
            PnrPayError::addErrorRecord($account_id,$e->getMessage());
            return ['code'=>0,'msg'=>$e->getMessage()];
        }

        $accountAuth = new AccountAuth();
        $accountAuth->status = 1;
        $accountAuth->account_id = $account_id;
        $accountAuth->name = $name;
        $accountAuth->id_number = $idCardNo;
        $accountAuth->auth_user_number = $usrId;
        $accountAuth->auth_user_pwd = $pwd;
        $accountAuth->auth_time = time();
        if($accountAuth->save()){
            return ['code'=>1,'msg'=>'认证成功!'];
        }else{
            return ['code'=>0,'msg'=>'系统错误,请重试'];
        }

    }
    /**
     * 实名认证
     * @param $account_id
     * @param $name
     * @param $idCardNo
     * @return array
     */
    private function _authName($account_id,$name,$idCardNo){

        $authdata = array(
            'IdNo' => $idCardNo,
            'IdName' => $name,
        );

        try{
            $this->pnrpayService->fastRealNameAuth($authdata);
        }catch (Exception $e) {
            PnrpayError::addErrorRecord($account_id,$e->getMessage());
            return ['code'=>0,'msg'=>$e->getMessage()];
        }

    }

    /**
     * 实名认证 检查参数
     * @param $account_id
     * @param $name
     * @param $idCardNo
     * @return array
     */
    private function _authCheck($account_id,$name,$idCardNo){


        $service = new CheckService();
        if(!$service->validation_filter_id_card($idCardNo)){
            return ["code"=>0,"msg"=>"请输入正确的身份证号码"];
        }

        if($this->_isUserAuth($account_id)){
            return ["code"=>0,"msg"=>"用户已通过实名认证"];
        }
        return ["code"=>1];

    }

    /**
     * 绑定银行卡参数检测
     * @param $account_id
     * @param $bankCode
     * @param $cardNo
     * @param $phone
     * @return array
     */
    private function _bindBankCheck($account_id,$bankCode,$cardNo,$phone){
        if(!$this->_isUserAuth($account_id)){
            return ["code"=>0,"msg"=>"用户未通过实名认证"];
        }

        return ["code"=>1];
    }

    /**
     * 用户是否已经实名认证
     * @param $account_id
     * @return array
     */
    private function _isUserAuth($account_id){
        $accountauth = AccountAuth::find()->where(['account_id'=>$account_id])->one();
        if(isset($accountauth)){
           return true;
        }
        return false;
    }


    /**
     * 创建汇付用户编号
     * @param $phone
     * @return string
     */
    private function _createUsrId($phone)
    {
        $dict = array('w', '3', 'y', 'b', 'g', '4', 't', 'x', 'h', 'j');
        $rand = mt_rand(1000, 9999);

        $res = ACCOUNT_USER_PREFIX;
        for ($i = 0; $i < strlen($phone); $i++) {
            $res .= $dict[$phone[$i]];
        }

        return $res . $rand;
    }



}