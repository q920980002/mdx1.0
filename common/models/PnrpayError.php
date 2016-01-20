<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/20
 * Time: 下午4:50
 */
namespace common\models;
use yii\db\ActiveRecord;



class PnrpayError extends ActiveRecord{


    /**
     * @inheritdoc
     */
    public static function tableName(){

        return '{{%mdx_pnrpay_error}}';

    }

    public static function addErrorRecord($account_id,$errorMsg){
        $errorModel = new PnrpayError();
        $errorModel->account_id = $account_id;
        $errorModel->error_msg = $errorMsg;
        $errorModel->save();
    }


}