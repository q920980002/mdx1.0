<?php
/**
 * Created by PhpStorm.
 * User: Devon
 * Date: 15/10/22
 * Time: 17:13
 */

namespace common\service\sms;

interface SmsInterface
{
    public function send($mobile, $text);
}