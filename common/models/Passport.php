<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/13
 * Time: 下午4:00
 */
namespace common\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Passport extends ActiveRecord implements IdentityInterface{

    /**
     * @inheritdoc
     */
    public static function tableName(){

        return '{{%mdx_passport}}';

    }

    /**
     * 用户注册
     */
    public static function register($data){

        if(static::findByPhone($data['phone'])){
            return ['code'=>0,'msg'=>'手机号已被注册'];
        }
        $passport = new Passport();
        $passport->phone = $data['phone'];
        $passport->password = $passport->md5($data['password']);
        if($passport->save()){
            //添加 account记录
            $account = new Account();
            $account->passport_id = $passport->id;
            $account->create_time = time();
            $account->save();
            return ['code'=>1,'msg'=>'注册成功'];
        }else{
            return ['code'=>0,'msg'=>'注册失败'];
        }

    }

    /**
     * 密码加密
     * @param $password
     * @return string
     */
    public function md5($password){
        $crypt = md5($password);
        return substr(md5(substr($crypt, 4, 24) . substr($crypt, 0, 4)), 4, 24);
    }

    /**
     *
     * @param $phone
     * @return null|static
     */
    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => $phone]);
    }

    /**
     * 用户登录
     * @param $post
     * @return array
     */
    public static function login($post){


        $passport = static::findByPhone($post['phone']);
        if(isset($passport)){

            if($passport->password === $passport->md5($post['password'])){
                if(\Yii::$app->user->login($passport, isset($post['rememberMe']) ? 3600 * 24 * 30 : 0)){
                    $account = Account::find()->where(['passport_id'=>$passport->id])->one();
                    \Yii::$app->session['passport_id'] = $passport->id;
                    \Yii::$app->session['account_id'] = $account->id;
                    return ['code'=>1,'msg'=>'登录成功'];
                }
            }else{
                return ['code'=>0,'msg'=>'密码错误'];
            }

        }else{
            return ['code'=>0,'msg'=>'手机号不存在'];
        }


    }






    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }




}