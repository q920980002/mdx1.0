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
use common\models\PnrpayError;
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



    public function authName($account_id,$name,$idCardNo){

        /**
         * 检查参数
         */
        $res = $this->_authCheck($account_id,$name,$idCardNo);
        if($res['code'] == 0){
            return $res;
        }

        $authdata = array(
            'IdNo' => $idCardNo,
            'IdName' => $name,
        );

        try{
            $this->pnrpayService->fastRealNameAuth($authdata);

        }catch (Exception $e) {
            PnrpayError::addErrorRecord($account_id,$e->getMessage());

        }

        $accountAuth = new AccountAuth();
        $accountAuth->status = 1;
        $accountAuth->name = $name;
        $accountAuth->id_number = $idCardNo;
        $accountAuth->auth_time = time();
        if($accountAuth->save()){
            return ['code'=>1,'msg'=>'认证成功!'];
        }else{
            return ['code'=>0,'msg'=>'系统错误,请重试'];
        }


    }

    private function _authCheck($account_id,$name,$idCardNo){

        $accountauth = AccountAuth::find()->where(['account_id'=>$account_id])->one();

        if(isset($accountauth)){
            return ["code"=>0,"msg"=>"用户已通过实名认证"];
        }
        return ["code"=>1];

    }







}