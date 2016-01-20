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


        $(function(){
            $("#register-btn").click(function(){

                $.ajax({
                    type: 'post',
                    url: '/site/login',
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
        });




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

    <div class="clear"></div>
    <div class="input-box">
        <i class="typcn typcn-lock-closed"></i>
        <input type="password" name="password" />
    </div>
    <div style="height:20px;clear:both;">
        <input name="rememberMe" value="1" type="checkbox" />
        <label>记住密码</label>
        <a style="float: right;color:#d2d2d2;" href="/forget" >忘记密码?</a>
    </div>
    <div style="height:20px;clear:both"></div>
    <div class="login-btn-box">
        <span id="register-btn"  class="button button-3d button-caution button-rounded">登录</span>
    </div>
    <div style="height:30px;clear:both"></div>
    <div style="text-align: center"><a style="padding: 2px 6px;background: #6BC3F2;color:#fff;font-size:12px;border-radius: 9px;" href="/register">注册新账号&gt;&gt;</a></div>

</div>
</form>
</body>
</html>
