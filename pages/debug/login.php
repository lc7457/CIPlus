<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
用户登录：
<form id="reg" action="https://passport.jciuc.com/passport/user/login" method="post" onsubmit="return pwmd5();">
    email: <input type="text" name="email">
    phone <input type="text" name="phone">
    password <input type="text" name="password">
    <input type="submit">
</form>
管理员登录：
<form id="reg" action="https://passport.jciuc.com/passport/admin/login" method="post" onsubmit="return pwmd5();">
    ID:<input type="text" name="admin">
    password： <input type="text" name="password">
    <input type="submit">
</form>
<script type="text/javascript" src="/assets/jquery/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/assets/jquery/jquery.md5.js"></script>
<script type="text/javascript">
    function pwmd5() {
        var pw = $("input[name=password]").val();
        $("input[name=password]").val($.md5(pw));
    }
</script>
</body>
</html>