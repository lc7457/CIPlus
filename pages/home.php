<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?= lang('title'); ?></title>
    <link href="<?= base_url('assets/jquery/jquery.fullpage.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('assets/jquery/jquery-3.1.1.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/jquery/jquery.easings.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/jquery/jquery.fullpage.min.js') ?>" type="text/javascript"></script>
    <link href="<?= base_url('assets/css/base.css') ?>" rel="stylesheet">
</head>
<body>
<div id="home-page">
    <div class="section">
        <h3>用户</h3>
    </div>
    <div class="section">
        <h3>支付</h3>
    </div>
    <div class="section">
        <h3>认证</h3>
    </div>
    <div class="section">
        <h3>授权</h3>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#home-page').fullpage({
            sectionsColor: ['#1bbc9b', '#4BBFC3', '#7BAABE', '#f90'],
            navigation: true
        });
    });
</script>
</body>
</html>