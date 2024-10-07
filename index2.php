<?php include 'header.php'; ?>
<form action="admin.php" method="post">
    用户名: <input type="text" name="username"><br>
    密码: <input type="password" name="password"><br>
	 <input type="hidden" name="origin" value="index">
    <input type="submit" value="登录">
</form>
<?php include 'footer.php'; ?>