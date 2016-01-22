<?php
/**
 * Created by Gaoshichao.
 * Date: 15/9/24
 * Time: 下午3:45
 */
//汇付配置
define('PLATFORM_HOST',"http://123.57.43.221");

//版本号
define('INTERFACE_VERSION','10');

//商户号
define('MERCHANT_ID','510895');

//角色号
define('USER_ROLE','99');

//签名服务器
//define('SIGN_SERVER','192.168.1.4:7189');
//define('SIGN_SERVER','220.249.13.234:7189');
define('SIGN_SERVER','127.0.0.1:8733');


//异步通知地址
define('NOTIFY_URL',PLATFORM_HOST.'/pnrpay/notify');

//生产环境服务器地址
define('PRODUCT_SERVER_BACKEND_URL','https://mas.chinapnr.com');//前台
define('PRODUCT_SERVER_FRONTEND_URL','http://mas.chinapnr.com');//后台

//测试服务器地址
define('TEST_SERVER_URL','http://test.chinapnr.com');

//接口请求地址(实名认证，带角色银账户开户，绑定银行卡接口,代扣充值短信验证申请接口)
define('BASE_QUERY_URL',TEST_SERVER_URL.'/gao/entry.do');

//接口请求地址(代扣充值接口,用户开户,自动扣款,取现)
define('PART_QUERY_URL',TEST_SERVER_URL.'/gar/entry.do');

//接口请求地址(单笔订单支付)
define('CYBER_QUERY_URL',TEST_SERVER_URL.'/gar/RecvMerchant.do');

//接口请求地址(自动扣款签约)
define('TRANSFER_AUTH_QUERY_URL',TEST_SERVER_URL.'/gau/UnifiedServlet');

//接口请求地址(账户余额查询)
define('ACCOUNT_BALANCE_QUERY_URL',TEST_SERVER_URL.'/gaq/entry.do');

define('ACCOUNT_USER_PREFIX','mdx2_');

//每日取现次数(不要随便更改，除非汇付另有通知)
define('EVERYDAY_CASH_COUNT',2);


