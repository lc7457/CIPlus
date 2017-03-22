<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= lang('backend_title'); ?></title>
    <link rel="shortcut icon" href="/assets/favicon.ico">
    <link href="/assets/lib/normalize/normalize.css" rel="stylesheet">
    <link href="/assets/lib/bootstrap/themes/simplex/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/lib/jquery/plugins/jquery.mmenu.all.css" rel="stylesheet">
    <link href="/assets/admin/css/core.css" rel="stylesheet">
</head>
<body>
<div id="page">
    <?php $this->load->view("inc/header"); ?>
    <?php $this->load->view("inc/menu"); ?>
    <div class="content">
        Debug Template
    </div>
</div>

<script src="/assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
<script src="/assets/lib/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/lib/jquery/plugins/jquery.mmenu.all.min.js" type="text/javascript"></script>
<script src="/assets/admin/js/core.js" type="text/javascript"></script>
</body>
</html>