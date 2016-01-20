<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/20
 * Time: 下午4:50
 */
namespace common\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class AccountAuth extends ActiveRecord{


    /**
     * @inheritdoc
     */
    public static function tableName(){

        return '{{%mdx_account_auth}}';

    }



}