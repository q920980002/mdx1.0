<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/20
 * Time: 下午1:49
 */
namespace frontend\service;

use common\chinapnr\chinaPnrPay;

class ChinapnrService {


    public $pnrpayService;


    /**
     * 构造函数
     */
    public function __construct(){
        $this->pnrpayService = new chinaPnrPay();
    }



    private function authName(){

    }

}