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
    <script type="text/javascript" src="/js/fastclick.js"></script>
    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/public.js"></script>
</head>
<body>
<div class="wrap">
    <div class="container">
       <?= $content ?>
    </div>
</div>

<footer class="footer">
    <a href="/" <?php if($this->context->action->id == "index") echo 'class="active"'; ?>>
        <i class="typcn typcn-home"></i>
        <label>首页</label>
    </a>
    <a  href="/cart" <?php if($this->context->action->id == "cart") echo 'class="active"'; ?>>
        <i class="typcn typcn-shopping-cart"></i>
        <label>购物车</label>
    </a>
    <a  href="/user" <?php if($this->context->action->id == "user") echo 'class="active"'; ?>>
        <i class="typcn typcn-user"></i>
        <label>我的账户</label>
    </a>
    <a  href="/site/logout" <?php if($this->context->action->id == "more") echo 'class="active"'; ?>>
        <i class="typcn typcn-social-flickr"></i>
        <label>更多</label>
    </a>
</footer>

</body>
</html>

