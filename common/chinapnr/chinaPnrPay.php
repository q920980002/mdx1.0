<?php
/**
 * Created by Gaoshichao.
 * Date: 15/9/24
 * Time: 上午11:01
 */
namespace common\chinapnr;

use yii\base\Exception;
require_once 'chinaPnrPay.conf.php';

/**
 * 在线网银支付类
 * Class onlinePay
 * @package common\onlinepay
 */
class chinaPnrPay
{
    private $_pay_default_value;
    private $_ret_code_info;

    public function __construct()
    {
        //版本号
        $this->_pay_default_value['Version'] = INTERFACE_VERSION;
        $this->_pay_default_value['MerId'] = MERCHANT_ID;
        $this->_pay_default_value['UsrRole'] = USER_ROLE;
        $this->_pay_default_value['RetUrl'] = NOTIFY_URL;
        $this->_pay_default_value['BgRetUrl'] = NOTIFY_URL;


    }


    /**
     * 实名认证接口
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'CtznShpCertTrans'
     * MerId 商户号 必须
     * OperId 操作员号 非必须
     * IdNo 身份证号 必须
     * IdName 姓名 必须
     * Remark 备注 非必须
     */
    public function fastRealNameAuth($data)
    {
        $data['CmdId'] = 'CtznShpCertTrans';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OperId' => 'N',
                'IdNo' => 'Y',
                'IdName' => 'Y',
                'Remark' => 'N',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'CertStat' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array();
        return $this->_apiGate($data, $params, $check_fields);

    }

    /**
     * 开通汇付账户接口
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'regist'
     * MerId 商户号 必须
     * MerUsrId 用户号 必须
     * UsrMp 购买者手机号 必须
     * IdType 证件类型 必须 ‘01’代表身份证 必须
     * IdNo 证件号码 必须
     * UsrName 用户名（个人->姓名；公司->公司名） 必须
     * UsrPwd 登录操作员密码（md5加密） 必须
     * UsrRole 用户角色 必须
     * UsrShortName 用户名简称 必须
     * IsCertChk 是否实名 必须
     * IsActivate 是否激活 必须
     * IsOperRecv 是否开通收款户 必须
     * IsSignAutoPay 是否签约自动扣款 必须
     * IsPrivateCash 是否允许对私结算 非必须
     * UsrId 用户号 非必须 非必须
     * OperEmail 用户邮箱 非必须
     * ProvId 用户所在省份代号 非必须
     * AreaId 用户所在城市代号 非必须
     *
     */
    public function createChinapnrAccount($data)
    {
        $data['CmdId'] = 'regist';
        $data['IdType'] = '01';
        $data['UsrRole'] = USER_ROLE;
        $data['IsCertChk'] = 'Y';
        $data['IsActivate'] = 'Y';
        $data['IsOperRecv'] = 'Y';
        $data['IsSignAutoPay'] = 'Y';
        $data['IsPrivateCash'] = 'Y';
        $data['IsOpenDep'] = 'N';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'MerUsrId' => 'Y',
                'UsrMp' => 'Y',
                'IdType' => 'Y',
                'IdNo' => 'Y',
                'UsrName' => 'Y',
                'UsrPwd' => 'Y',
                'UsrRole' => 'Y',
                'UsrShortName' => 'Y',
                'IsCertChk' => 'Y',
                'IsActivate' => 'Y',
                'IsOperRecv' => 'Y',
                'IsSignAutoPay' => 'Y',
                'IsPrivateCash' => 'N',
                'UsrId' => 'N',
                'OperEmail' => 'N',
                'ProvId' => 'N',
                'AreaId' => 'N',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'UsrId' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需加校验的字段
        $check_fields = array();

        return $this->_apiGate($data, $params, $check_fields);
    }

    /**
     * 绑定充值卡
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'WHBindCard'
     * MerId 商户号 必须
     * OperId 操作员号 必须
     * LoginPwd 登录密码 必须
     * CardNo 银行卡卡号 必须
     * OpenAcctName 银行卡开户姓名 必须
     * BankCode 银行编码 必须
     * CertType 证件类型('00' 身份证) 必须
     * CertId 证件号（身份证号） 必须
     * UsrMp 预留手机号 必须
     * CardType 卡类型（'D':借记卡） 必须
     * BgRetUrl 后台通知地址 必须
     */
    public function bindRechargeCard($data)
    {
        $data['CmdId'] = 'WHBindCard';
        $data['CertType'] = '00';
        $data['CardType'] = 'D';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OperId' => 'Y',
                'LoginPwd' => 'Y',
                'CardNo' => 'Y',
                'OpenAcctName' => 'Y',
                'BankCode' => 'Y',
                'CertType' => 'Y',
                'CertId' => 'Y',
                'UsrMp' => 'Y',
                'CardType' => 'Y',
                'BgRetUrl' => 'N',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array(
            'req_check_fields' => array(
                'Version',
                'CmdId',
                'MerId',
                'OperId',
                'LoginPwd',
                'CardNo',
                'OpenAcctName',
                'BankCode',
                'CertType',
                'CertId',
                'UsrMp',
                'CardType',
            ),
        );
        return $this->_apiGate($data, $params, $check_fields);
    }

    /**
     * 绑定取现卡
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'SDPBindCard'
     * MerId 商户号 必须
     * OperId 操作员号 必须
     * Password 操作员登录密码（超过6次锁定） 必须
     * OpenAcctId 银行卡号 必须
     * OpenAcctName 开户姓名 必须
     * OpenBankId 银行编码 必须
     * OpenProvId 开户省份 必须
     * OpenAreaId 开户城市 必须
     * AutoCashFlag 是否设置为自动取现银行 必须
     */
    public function bindCashCard($data)
    {
        $data['CmdId'] = 'SDPBindCard';
        $data['AutoCashFlag'] = 'Y';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OperId' => 'Y',
                'Password' => 'Y',
                'OpenAcctId' => 'Y',
                'OpenAcctName' => 'Y',
                'OpenBankId' => 'Y',
                'OpenProvId' => 'Y',
                'OpenAreaId' => 'Y',
                'AutoCashFlag' => 'Y',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array();
        return $this->_apiGate($data, $params, $check_fields);
    }

    /**
     * 网银充值接口
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'Buy'
     * MerId 商户号 必须
     * OrderId 订单号(10-20位 保证唯一) 必须
     * OrdAmt 订单总金额 必须
     * CurCode 币种 必须 固定值：'RMB'
     * Pid 商品编号 非必须
     * RetUrl 页面返回Url 必须
     * MerPeiv 商户私有域 非必须
     * GateId 银行ID（建议为空） 非必须
     * UsrMp 购买者手机号 非必须
     * DivDetails 分账明细 非必须
     * OrderType 订单类型 非必须
     * PayUsrId 付费用户号 非必须
     * PnrNum pnr号 非必须
     * BgRetUrl 异步通知地址 必须
     * IsBalance 是否结算 非必须
     * RequestDomain 请求域名（防钓鱼） 非必须
     * OrderTime 订单时间 非必须
     * ValidTime 有效时间 非必须
     * ValidIp 有效Ip 非必须
     *
     */
    public function cyberPay($data)
    {
        $data['CmdId'] = 'Buy';
        $data['CurCode'] = 'RMB';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OrdId' => 'Y',
                'OrdAmt' => 'Y',
                'CurCode' => 'Y',
                'Pid' => 'N',
                'RetUrl' => 'Y',
                'MerPriv' => 'N',
                'GateId' => 'N',
                'UsrMp' => 'N',
                'DivDetails' => 'N',
                'OrderType' => 'N',
                'PayUsrId' => 'N',
                'PnrNum' => 'N',
                'BgRetUrl' => 'Y',
                'IsBalance' => 'N',
                'RequestDomain' => 'N',
                'OrderTime' => 'N',
                'ValidTime' => 'N',
                'ValidIp' => 'N',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'RespCode' => 'Y',
                'TrxId' => 'Y',
                'OrdAmt' => 'Y',
                'CurCode' => 'N',
                'Pid' => 'N',
                'OrderId' => 'Y',
                'MerPriv' => 'N',
                'RetType' => 'Y',
                'DivDetails' => 'N',
                'GateId' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array();
        return $this->_webGate($data, $params, $check_fields, CYBER_QUERY_URL);
    }


    /**
     * 代扣充值
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'WHDebitDeductSave'
     * MerId 商户号 必须
     * OperId 操作员号 必须
     * CardNo 银行卡卡号 必须
     * OpenAcctName 银行卡开户姓名 必须
     * CertType 证件类型('00' 身份证) 必须
     * CertId 证件号（身份证号） 必须
     * UsrMp 预留手机号 必须
     * TransAmt 交易金额 必须
     * CardType 卡类型（'D':借记卡） 必须
     * Remark 备注 必须
     * BgRetUrl 异步通知地址 非必须
     * MerOrdId 商户订单号 非必须
     * AuthCode 短信验证码
     */
    public function authWithholdPay($data)
    {
        $data['CmdId'] = 'WHDebitDeductSave';
        $data['CertType'] = '00';
        $data['CardType'] = 'D';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OperId' => 'Y',
                'CardNo' => 'Y',
                'OpenAcctName' => 'Y',
                'CertType' => 'Y',
                'CertId' => 'Y',
                'UsrMp' => 'Y',
                'TransAmt' => 'Y',
                'CardType' => 'Y',
                'Remark' => 'Y',
                'BgRetUrl' => 'N',
                'MerOrdId' => 'N',
                'AuthCode' => 'N',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array(
            'req_check_fields' => array(
                'Version',
                'CmdId',
                'MerId',
                'OperId',
                'CardNo',
                'OpenAcctName',
                'CertType',
                'CertId',
                'UsrMp',
                'TransAmt',
                'CardType',
                'Remark',
                'MerOrdId',
                'AuthCode',
            ),
        );
        return $this->_apiGate($data, $params, $check_fields, PART_QUERY_URL);

    }

    /**
     * 代扣充值短信验证申请
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'SaveSmsAuthcodeReq'
     * MerId 商户号 必须
     * MerOrdId 商户订单号 必须
     * OperId 操作员号 必须
     * CardNo 银行卡卡号 必须
     * TransAmt 交易金额 必须
     */
    public function applyWithholdSms($data)
    {
        $data['CmdId'] = 'SaveSmsAuthcodeReq';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'MerOrdId' => 'Y',
                'OperId' => 'Y',
                'CardNo' => 'Y',
                'TransAmt' => 'Y',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array();
        return $this->_apiGate($data, $params, $check_fields);
    }

    /**
     * 自动扣款签约
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：''
     * MerId 商户号 必须
     * MerDate 商户日期 必须
     * MerTime 商户时间 必须
     * BgRetUrl 异步通知地址
     */
    public function autoTransferAuth($data)
    {
        $data['CmdId'] = 'AutoPaySign';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'MerDate' => 'Y',
                'MerTime' => 'Y',
                'BgRetUrl' => 'Y',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OperId' => 'Y',
                'RespCode' => 'Y',
                'finAcctBal' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );


        //需校验的字段
        $check_fields = array();
        return $this->_webGate($data, $params, $check_fields, TRANSFER_AUTH_QUERY_URL);

    }

    /**
     * 自动扣款接口
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'BuyPayOut'
     * MerId 商户号 必须
     * OrdId 订单号(10-20位 保证唯一) 必须
     * OrdAmt 订单总金额 必须
     * CurCode 币种 非必须 固定值：'RMB'
     * Pid 商品编号 非必须
     * MerPeiv 商户私有域 非必须
     * GateId 银行ID 必须 固定值：'61'
     * UsrMp 购买者手机号 非必须
     * DivDetails 分账明细 非必须
     * OrderType 订单类型 非必须
     * CrFlag 信用标志 非必须
     * PayUsrId 付费用户号 必须
     * BgRetUrl 异步通知地址 必须
     * PnrNum pnr号 非必须
     * IsBalance 是否结算 非必须
     */
    public function autoTransfer($data)
    {
        $data['CmdId'] = 'BuyPayOut';
        $data['CurCode'] = 'RMB';
        $data['GateId'] = '61';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OrdId' => 'Y',
                'OrdAmt' => 'Y',
                'CurCode' => 'N',
                'Pid' => 'N',
                'MerPriv' => 'N',
                'GateId' => 'Y',
                'UsrMp' => 'N',
                'DivDetails' => 'N',
                'OrderType' => 'N',
                'CrFlag' => 'N',
                'PayUsrId' => 'Y',
                'BgRetUrl' => 'Y',
                'PnrNum' => 'N',
                'IsBalance' => 'N',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'MerId' => 'Y',
                'TrxId' => 'Y',
                'OrdAmt' => 'Y',
                'CurCode' => 'N',
                'Pid' => 'N',
                'OrdId' => 'Y',
                'MerPriv' => 'N',
                'RetType' => 'Y',
                'DivDetails' => 'N',
                'GateId' => 'Y',
                'PayUsrId' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );


        //需校验的字段
        $check_fields = array();
        return $this->_apiGate($data, $params, $check_fields, PART_QUERY_URL);

    }


    /**
     * 取现接口
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'PCashOut'
     * MerId 商户号 必须
     * OperId 操作员号 必须
     * TransPwd 操作员交易密码 必须
     * TransAmt 交易金额 必须、
     * CardNo 银行卡卡号 必须
     * Remark 备注 必须
     */
    public function userCashOut($data)
    {
        $data['CmdId'] = 'PCashOut';
        $data['Remark'] = '取现';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'OperId' => 'Y',
                'TransPwd' => 'Y',
                'TransAmt' => 'Y',
                'CardNo' => 'Y',
                'Remark' => 'Y',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );


        //需校验的字段
        $check_fields = array();
        return $this->_apiGate($data, $params, $check_fields, PART_QUERY_URL);

    }

    /**
     * 账户余额查询
     * Version 版本号 必须 固定值：10
     * CmdId 消息类型 必须 固定值：'QueryBalance'
     * MerId 商户号 必须
     * UsrId 用户名 必须
     * AcctType 账户类型（00:收款账户；01:大客户信用账户；02:收款账户（增加冻结余额）; 03:收款账户+理财账户） 必须
     */
    public function accountQueryBalance($data)
    {
        $data['CmdId'] = 'QueryBalance';

        //参数校验(顺带签名顺序)
        $params = array(
            'req_params' => array(
                'Version' => 'Y',
                'CmdId' => 'Y',
                'MerId' => 'Y',
                'UsrId' => 'Y',
                'AcctType' => 'Y',
            ),
            'resp_params' => array(
                'CmdId' => 'Y',
                'RespCode' => 'Y',
                'AvlBal' => 'Y',
                'TmpBal' => 'Y',
                'SepBal' => 'Y',
                'LiqBal' => 'Y',
                'AcctBal' => 'Y',
                'finAcctBal' => 'Y',
                'ErrMsg' => 'Y',
            ),
        );

        //需校验的字段
        $check_fields = array();
        return $this->_apiGate($data, $params, $check_fields, ACCOUNT_BALANCE_QUERY_URL);


    }

    /**
     * 异步通知接口
     * @param $data
     * @return array
     * @throws Exception
     */
    public function notify($data)
    {
        $resp_params = array();
        switch ($data['CmdId']) {
            case 'Buy'://绑定充值卡
                $resp_params = array(
                    'CmdId' => 'Y',
                    'MerId' => 'Y',
                    'RespCode' => 'Y',
                    'TrxId' => 'Y',
                    'OrdAmt' => 'Y',
                    'CurCode' => 'N',
                    'Pid' => 'N',
                    'OrdId' => 'Y',
                    'MerPriv' => 'N',
                    'RetType' => 'Y',
                    'DivDetails' => 'N',
                    'GateId' => 'Y',
                );

                break;
            case 'WHBindCard'://绑定充值卡
                $resp_params = array(
                    'CmdId' => 'Y',
                    'MerId' => 'Y',
                    'CardNo' => 'Y',
                    'OperId' => 'Y',
                    'OrdId' => 'Y',
                    'RespCode' => 'Y',
                    'TransStat' => 'Y',
                    'ErrMsg' => 'Y',
                );

                break;
            case 'WHDebitDeductSave'://代扣充值
                $resp_params = array(
                    'CmdId' => 'Y',
                    'MerId' => 'Y',
                    'OrdId' => 'Y',
                    'RespCode' => 'Y',
                    'ErrMsg' => 'Y',
                    'Remark' => 'Y',
                    'TransAmt' => 'Y',
                );

                break;
            case 'BuyPayOut'://自动扣款
                $resp_params = array(
                    'CmdId' => 'Y',
                    'MerId' => 'Y',
                    'RespCode' => 'Y',
                    'TrxId' => 'Y',
                    'OrdAmt' => 'Y',
                    'CurCode' => 'N',
                    'Pid' => 'N',
                    'OrdId' => 'Y',
                    'MerPriv' => 'N',
                    'RetType' => 'Y',
                    'DivDetails' => 'N',
                    'GateId' => 'Y',
                );

                break;
        }

        $res_data = $this->checkRespData($data, $resp_params, array());

        return $res_data;
    }


    /**
     * 网关跳转
     * @param $data
     * @param $req_param_format
     * @return string
     * @throws Exception
     */
    private function _webGate($data, $param_format, $check_field = array(), $url)
    {
        //检测参数是否正确
        $tem_arr = $this->_checkReqParams($data, $param_format['req_params']);

        //获取ChkValue
        $tem_arr['ChkValue'] = $this->_getChkValue($tem_arr, $check_field);

        error_log('【' . date("Y-m-d H:i:s") . '】 ' . $url . '?' . http_build_query($tem_arr) . "\n", 3, dirname(__FILE__) . "/web_req.log");
        //return CYBER_QUERY_URL . '?' . http_build_query($tem_arr);
        //自动扣款授权（直接返回）
        if ($tem_arr['CmdId'] == 'AutoPaySign') {
            header("Location:" . $url . '?' . http_build_query($tem_arr));
        } else {
            return $this->createPostForm($tem_arr, $url);
        }
    }

    public function createPostForm($infoArr, $url)
    {
        //构造表单
        $resForm = "<form id='form' action='" . $url . "'  method='post'>";
        if (is_array($infoArr)) {
            foreach ($infoArr as $k => $v) {
                $resForm .= <<<Eof
<input type="hidden" name="{$k}" value="{$v}" />

Eof;

            }
            $resForm .= "</form>";

            //提交表单
            $resForm .= <<<EOf

<script type="text/javascript">
    document.forms['form'].submit();
</script>
EOf;
            echo $resForm;
        } else {
            throw new Exception('非法表单数据');
        }

    }

    /**
     * 直连接口
     * @param $data     请求数据
     * @param array $param_format 数据格式(附带顺序)
     * @param array $check_field 需进行校验的字段
     * @param string $url 请求地址
     * @return array
     * @throws Exception
     */
    private function _apiGate($data, $param_format, $check_field = array(), $url = BASE_QUERY_URL)
    {
        //检测参数是否正确
        $tem_arr = $this->_checkReqParams($data, $param_format['req_params']);

        //获取ChkValue
        $tem_arr['ChkValue'] = $this->_getChkValue($tem_arr, $check_field);

        $req_str = "";
        foreach($tem_arr as $key=>$a){
            $req_str.=$key."=>".$a."\r\n";
        }


        error_log('【' . date("Y-m-d H:i:s") . '】\n' . $url . '?' . http_build_query($tem_arr) . "\n".iconv('GBK', 'UTF-8', $req_str). "\n\n\n", 3, dirname(__FILE__) . "/api_req.log");

        //请求数据
        $res = $this->_curl($url, $tem_arr);

        error_log('【' . date("Y-m-d H:i:s") . "】\n" .  iconv('GBK', 'UTF-8', $res) . "\n\n\n", 3, dirname(__FILE__) . "/api_resp.log");

        return $this->checkRespData($res, $param_format['resp_params'], $check_field);

    }

    /**
     * 检测请求参数是否合法
     * @param $data （数据数组）
     * @return array
     */
    private function _checkReqParams($data, $format)
    {
        $res_arr = array();

        foreach ($format as $k => $v) {
            if (is_array($v)) {
                $val = $this->_checkReqParams($data[$k], $format[$k]);
            } else {
                if ($v == 'Y' && !isset($data[$k]) && !isset($this->_pay_default_value[$k])) {
                    throw new Exception('缺少必要参数【' . $k . '】');
                    exit;
                } else {
                    $val = isset($data[$k]) ? $data[$k] : (isset($this->_pay_default_value[$k]) ? $this->_pay_default_value[$k] : '');
                }
            }

            if ($val !== '') {
                $res_arr[$k] = iconv('UTF-8', 'GBK', $val);
            }
        }

        return $res_arr;
    }


    /**
     * 生成订单号(长度为18位)
     */
    public function createRequestNo()
    {
        //获取日期（方便统计）
        $date = date("mdHi");

        //获取当前时间微秒(确保唯一)
        list($usec, $sec) = explode(" ", microtime());
        $usec = substr((string)$usec, 2, 6);
        //$current = $usec . (string)$sec;

        //生成随机数(确保唯一)
        $rand = mt_rand(1000, 9999);
        return $date . $rand . $usec;
    }

    /**
     * 获取请求所需的hmac加密串
     * @param $data （参数数组）
     * @param $order (排序数组，用于给请求参数排序)
     * @return string
     */
    private function _getChkValue($data, $check_fields)
    {
        if (is_array($data)) {
            $reqHmacString = '';

            //产生加密原始数据
            foreach ($data as $k => $v) {
                if (isset($check_fields['req_check_fields'])) {
                    $reqHmacString .= in_array($k, $check_fields['req_check_fields']) ? $v : '';
                } else {
                    $reqHmacString .= $v;
                }
            }

            return $this->_sign($reqHmacString);
        } else {
            new Exception('请求参数不完整');
            exit;
        }

    }

    /**
     * 检测直连接口返回的数据
     * @param $data
     * @param $check_field
     * @return array
     * @throws Exception
     */
    public function checkRespData($data, $resp_params, $check_field)
    {
        $res_data = array();    //返回数据
        $to_check_str = null;   //要检测的字符串
        $Chkvalue = null;       //原签名;

        $tem_arr = array();     //临时存放原数据数组
        if (is_array($data)) {//判断是否是数组
            foreach ($data as $k => $v) {
                //拼接需校验数据
                if ($k != 'ChkValue') {
                    $tem_arr[$k] = trim(urldecode($v));
                } else {
                    $Chkvalue = trim(urldecode($v));
                }
                $res_data[$k] = iconv('GBK', 'UTF-8', urldecode($v));
            }

        } else {
            $org_data = explode("\n", trim($data, "\t\n\r\0\x0B"));
            foreach ($org_data as $v) {
                $tem_data = explode("=", trim($v, "\t\n\r\0\x0B"));

                if (is_array($tem_data) && count($tem_data) == 2) {
                    //拼接要校验的返回数据
                    if ($tem_data[0] != 'ChkValue') {
                        $tem_arr[$tem_data[0]] = urldecode($tem_data[1]);
                    } else {
                        $Chkvalue = urldecode($tem_data[1]);
                    }

                    $res_data[$tem_data[0]] = iconv('GBK', 'UTF-8', urldecode($tem_data[1]));
                } else {
                    throw new Exception('返回数据格式错误');
                }
            }
        }

        if ($res_data['RespCode'] == '000000') {
            //检测返回数据格式，并排序
            $order_arr = array();
            foreach ($resp_params as $k => $v) {
                if ($v == 'Y' && !isset($tem_arr[$k])) {
                    throw new Exception('返回数据缺少必要参数' . $k);
                } else {
                    $order_arr[] = $tem_arr[$k];
                }
            }
        } else {
            $order_arr = array($tem_arr['CmdId'], $tem_arr['RespCode'], $tem_arr['ErrMsg']);
        }


        $to_check_str = implode('', $order_arr);

        //var_dump($to_check_str);
        if (empty($res_data)) {
            throw new Exception('返回数据为空');
        } elseif (!$this->_verifySign($to_check_str, $Chkvalue)) {
            throw new Exception('验签失败，数据可能被篡改');
        } else {
            if (isset($res_data['RespCode'])) {//判断是否成功
                //p($res_data);
                if ($res_data['RespCode'] != '000000') {
                    throw new Exception($res_data['ErrMsg']);
                } else {
                    return $res_data;
                }
            } else {
                throw new Exception('未知数据');
            }
        }

    }


    private function _sign($toSignSting)
    {
        //组合签名数据
        $str_len = strlen($toSignSting);
        $toSignSting = 'S' . MERCHANT_ID . ($str_len > 1000 ? $str_len : str_pad($str_len, 4, '0', STR_PAD_LEFT)) . $toSignSting;

        $str_len = strlen($toSignSting);
        $toSignSting = ($str_len > 1000 ? $str_len : str_pad($str_len, 4, '0', STR_PAD_LEFT)) . $toSignSting;

        //p($toSignSting);die;
        //签名
        //$sign = $this->_curl(SIGN_SERVER, $toSignSting);


        $sign = $this->_socket($toSignSting);

        return substr($sign, 15, 256);
    }

    /**
     * @param $toVerifySignString
     * @param $ChkValue
     * @return bool
     * @throws Exception
     */
    private function _verifySign($toVerifySignString, $ChkValue)
    {
        //组合签名数据
        $str_len = strlen($toVerifySignString);
        $toVerifySignString = 'V' . MERCHANT_ID . ($str_len > 1000 ? $str_len : str_pad($str_len, 4, '0', STR_PAD_LEFT)) . $toVerifySignString . $ChkValue;

        $str_len = strlen($toVerifySignString);
        $toVerifySignString = ($str_len > 1000 ? $str_len : str_pad($str_len, 4, '0', STR_PAD_LEFT)) . $toVerifySignString;

        //p($toVerifySignString);
        //验签
        $ChkValue = $this->_socket($toVerifySignString);

        $ChkValue = substr($ChkValue, -260);
        $ChkValue = substr($ChkValue, 0, 256);

        if (substr($ChkValue, -4) == '0000') {
            return true;
        } else {
            return false;
        }
    }

    private function _curl($url, $data, $method = 'POST')
    {
        if (is_array($data)) {
            $req_str = http_build_query($data);
        } else {
            $req_str = $data;
        }
        if ($method !== 'POST' && $data) {
            $url .= '?' . $req_str;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36');
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_str);
        }
        $res = curl_exec($ch);
        $resCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (($resCode > 399 && $resCode < 500) || $err_no = curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        return $res;
    }

    private function _socket($data)
    {
        //$signServerScheme = parse_url(SIGN_SERVER, PHP_URL_SCHEME);
        $signServerHost = parse_url(SIGN_SERVER, PHP_URL_HOST);
        $signServerPort = parse_url(SIGN_SERVER, PHP_URL_PORT);

        $fp = fsockopen($signServerHost, $signServerPort, $errno, $errstr, 10);

        if (!$fp) {
            throw new Exception('socket初始化失败');
        } else {
            fputs($fp, $data);
            $ChkValue = '';
            while (!feof($fp)) {
                $ChkValue .= fgets($fp, 128);
            }
            //$ChkValue = substr($ChkValue,15,256);
            fclose($fp);
            return $ChkValue;
        }
    }
}

?>