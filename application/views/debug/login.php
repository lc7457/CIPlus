<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form id="reg" action="/api/login?access_token=<?= $token ?>" method="post" onsubmit="return pwmd5();">
    email: <input type="text" name="email">
    phone <input type="text" name="phone">
    password <input type="text" name="password">
    <input type="submit">
</form>
<script type="text/javascript" src="http://static.cifuwu.com/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript" src="http://static.cifuwu.com/jquery/plugins/jquery.md5.js"></script>
<script type="text/javascript">
    function pwmd5() {
        var pw = $("input[name=password]").val();
        $("input[name=password]").val($.md5(pw));
    }
</script>
</body>
</html>