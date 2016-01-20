<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobileoptimized" content="0"/>
    <link href="/css/public.css" rel="stylesheet">
    <link href="/css/site.css" rel="stylesheet">
    <link href="/css/button.css" rel="stylesheet">
    <script type="text/javascript" src="/js/fastclick.js"></script>
    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/public.js"></script>

    <script>
        /**
         * 多少秒发送一次
         */
        var TIMES = 60;

        $(function(){
            $("#getCodeBtn").click(function(){
                startTimeDown();
            });

            $("#register-btn").click(function(){

                $.ajax({
                    type: 'post',
                    url: '/site/register',
                    data: $("#register-form").serialize(),
                    dataType: 'json',
                    success: function (data) {
                        alert(data.msg);
                        if(data.code == 1){
                            location.href="/";
                        }
                    },
                    error: function () {
                        alert("ajax 错误");
                    }
                });

            });
        })
        var isbegin = false;
        function startTimeDown(){
            if(!isbegin){
                isbegin = true;
                var downTimes = TIMES;
                $("#getCodeBtn").addClass("disable");
                var timer = setInterval(function(){
                    if(downTimes > 0){
                        $("#getCodeBtn").text(downTimes+"秒后重新获得");
                        downTimes--;
                    }else{
                        clearInterval(timer);
                        downTimes = 10;
                        isbegin=false;
                        $("#getCodeBtn").removeClass("disable");
                        $("#getCodeBtn").text("重新获得");
                    }
                },1000);


                var param = {phone:$("#phone").val()};
                $.ajax({
                    type: 'post',
                    url: '/site/register-phone-code',
                    data: param,
                    dataType: 'json',
                    success: function (data) {
                        if(data.code != 1){
                            clearInterval(timer);
                            downTimes = 10;
                            isbegin=false;
                            $("#getCodeBtn").removeClass("disable");
                            $("#getCodeBtn").text("重新获得");
                        }
                        alert(data.msg);
                    },
                    error: function () {
                        alert("ajax 错误");
                    }
                });

            }

        }


    </script>

</head>
<body>
<div class="header">
    <a class="backArrow" href="/"></a>
    <h1>注册</h1>
</div>
<div class="login-logo">
    <img src="/images/logo.png" />

</div>
<form id="register-form">
<div class="site-login">
    <div class="input-box">
        <i class="typcn typcn-device-phone"></i>
        <input type="tel" name="phone" id="phone" />
    </div>
    <div class="verification-box">
        <div class="input-box">
            <i class="typcn typcn-tick"></i>
            <input type="tel" name="code" />
        </div>

        <span class="btn-code" id="getCodeBtn">获取验证码</span>
    </div>
    <div class="clear"></div>
    <div class="input-box">
        <i class="typcn typcn-lock-closed"></i>
        <input type="password"  name="password" />
    </div>
    <div class="input-box">
        <i class="typcn typcn-lock-closed"></i>
        <input type="password"  name="repeat-password" />
    </div>
    <div class="login-btn-box">
        <span id="register-btn"  class="button button-3d button-caution button-rounded">注册</span>
    </div>
    <div style="height:20px;clear:both"></div>
    <div>已有账号?<a href="/login">立即登录</a></div>

</div>
</form>
</body>
</html>
