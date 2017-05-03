<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
用户注册：
<form id="reg" action="https://passport.jciuc.com/passport/user/reg" method="post" onsubmit="return pwmd5();">
    email: <input type="text" name="email">
    phone <input type="text" name="phone">
    password <input type="text" name="password">
    <input type="submit">
</form>
管理员注册：
<form id="reg" action="https://passport.jciuc.com/passport/admin/reg" method="post" onsubmit="return apwmd5();">
    ID:<input type="text" name="admin">
    password： <input type="text" id="password" name="password">
    <input type="submit">
</form>
<script type="text/javascript" src="/assets/jquery/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/assets/jquery/jquery.md5.js"></script>
<script type="text/javascript">
    function pwmd5() {
        var pw = $("input[name=password]").val();
        $("input[name=password]").val($.md5(pw));
    }
    function apwmd5() {
        var pw = $("#password").val();
        $("#password").val($.md5(pw));
    }
</script>
</body>
</html>