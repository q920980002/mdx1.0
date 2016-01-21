<?php
namespace frontend\controllers;

use common\models\Passport;
use common\models\User;
use common\service\CheckService;
use common\service\SmsServer;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['signup','logout','cart','user'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','cart','user'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {


        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin(){

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $post = Yii::$app->request->post();

        if (Yii::$app->request->isPost) {
            $r = Passport::login($post);
            $this->ajaxReturn($r);
            //return $this->goBack();
        }

        return $this->renderPartial('login');

    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 购物车
     */
    public function actionCart(){


        return $this->render("cart");
    }



    /**
     * 我的账户
     */
    public function actionUser(){
        return $this->render("user");
    }

    /**
     * 更多
     */
    public function actionMore(){
        return $this->render("more");
    }

    /**
     * 发送注册短信
     */
    public function actionRegisterPhoneCode(){

        $timeSpace = 6; //60秒内只能发一次

        $post = Yii::$app->request->post();
        $phone = $post['phone'];

        $lastTime = Yii::$app->session['registerTime'];
        if(isset($lastTime)){
            if((time() - $lastTime) > $timeSpace){
                $this->ajaxReturn($this->_sendRegisterSms($phone));
            }else{
                $this->ajaxReturn(['code'=>0,'msg'=>'请稍后发送验证码']);
            }
        }else{
            $this->ajaxReturn($this->_sendRegisterSms($phone));
        }





    }
    private function _sendRegisterSms($phone){
        $smsService = new SmsServer();
        $code = rand(100000, 999999);
        Yii::$app->session['registerCode'] = $code;
        Yii::$app->session['registerTime'] = time();
        return $smsService->sendRegisterCodeSms($phone,$code);

    }

    /**
     * 用户注册
     */
    public function actionRegister(){

        $post = Yii::$app->request->post();

        if($post['code'] == Yii::$app->session['registerCode']){
            $this->ajaxReturn(Passport::register($post));
        }else{
            $this->ajaxReturn(['code'=>0,'msg'=>'手机验证码错误']);
        }



    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {

        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        return $this->renderPartial('register');
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
