<?php

session_start();

function logout() {

    session_unset();     // 清除会话变量

    session_destroy();   // 销毁整个会话

    header("Location: ".$_SERVER['PHP_SELF']); // 重定向回当前页面，实现页面刷新

    exit;

}

// 检查是否有退出登录的请求

if (isset($_GET['logout'])) {

    logout();

}

// 硬编码的用户名和密码

define('HARDCODED_USERNAME', 'admin');

define('HARDCODED_PASSWORD_HASH', password_hash('admin', PASSWORD_DEFAULT));



function validate_login() {

    global $loggedIn;



    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = $_POST['username'] ?? '';

        $password = $_POST['password'] ?? '';



        if (empty($username) || empty($password)) {

            $_SESSION['error'] = '用户名和密码不能为空。';

            return;

        }



        if ($username === HARDCODED_USERNAME && password_verify($password, HARDCODED_PASSWORD_HASH)) {

            $_SESSION['logged_in'] = true;

            $_SESSION['user_id'] = 1;

            $loggedIn = true;

            header('Location: #uploadedImages'); // 使用锚点跳转到页面内图片展示区域

            exit;

        } else {

            $_SESSION['error'] = '登录失败，请检查您的用户名和密码。';

        }

    }

}



validate_login();

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];



// 图片上传处理

if ($loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

    $target_dir = "./"; 

    $filename = basename($_FILES["image"]["name"]);

    $target_file = $target_dir . basename($_FILES["image"]["name"]);



    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

        echo "文件 ". htmlspecialchars( basename( $_FILES["image"]["name"])). " 已成功上传。";

    } else {

        echo "文件上传出错。";

    }

}



// 显示已上传图片

function displayUploadedImages() {

    $dir = "./";

    if ($dh = opendir($dir)) {

        while (($file = readdir($dh)) !== false) {

            if ($file != "." && $file != "..") {

                // 确认文件是图片（这里简单检查常见的图片扩展名）

                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if (in_array(strtolower($ext), $allowedTypes)) {                   



                    $imageUrl =  rawurlencode($file);

echo '<a class="cl" href="' . htmlspecialchars($imageUrl) . '" target="_blank">' . htmlspecialchars($file) . '</a><br />';



                }

            }

        }

        closedir($dh);

    }

}

?>



<!DOCTYPE html><html><head><meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>图片外链系统</title>

</head>

<body>

<header>     <h2>我的图床</h2></header>

<div class=content>

   <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>

        <!-- 登录表单仅在未登录时显示 -->

   

        <?php if (isset($_SESSION['error'])): ?>

            <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); ?></p>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data">

            <label for="username">账号:</label>

            <input type="text" id="username" name="username" required><br>

            <label for="password">密码:</label>

            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="登录">

        </form>

    <?php else: ?>

        <!-- 登录成功后显示的内容 span用来定位 不用删  不占位置-->

        <span id="uploadedImages"></span>

        <h2>欢迎回来</h2>

        <form method="post" action="" enctype="multipart/form-data">

            <label for="image">上传新图片:</label>

            <input type="file" id="image" name="image" accept="image/*"><br>

            <input type="submit" value="Upload">

        </form>

        

        <h2>已上传图片</h2><div id="messageBox"></div>

        <?php displayUploadedImages(); ?>

		   

    <?php endif; ?>

	</div>

	<footer><a href="?logout=true">退出登录</a></footer>

	<style>

/* 重置浏览器默认样式 */

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

}



/* 页面整体样式 */

body {

    font-family: 'Arial', sans-serif;

    line-height: 1.6;

    color: #333;

    background-color: #f4f4f4;

    padding: 20px;

}



/* 标题样式 */

header h2 {

    text-align: center;

    margin-bottom: 20px;

}



/* 内容区域样式 */

div.content {

    max-width: 800px;

    margin: 0 auto;

    background: #fff;

    padding: 20px;

    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

}



/* 登录表单样式 */

form {

    width: 100%;

    max-width: 300px;

    margin: 0 auto;

}



form label {

   /* display: block;*/

    margin-bottom: 5px;

}



form input[type="text"],

form input[type="password"],

form input[type="file"] {

    width: 100%;

    padding: 10px;

    margin-bottom: 15px;

    border: 1px solid #ddd;

}



form input[type="submit"] {

    background: #5cb85c;

    color: white;

    border: 0;

    padding: 10px 20px;

    cursor: pointer;

}



form input[type="submit"]:hover {

    background: #4cae4c;

}



/* 错误信息样式 */

p {

    background: #ffdddd;

    color: #cc0000;

    padding: 10px;

    margin-bottom: 20px;

}



/* 已上传图片列表样式 */

.uploaded-images {

    margin-top: 20px;

}



.uploaded-images a {

    display: block;

    margin-bottom: 10px;

    text-decoration: none;

    color: #337ab7;

}



.uploaded-images a:hover {

    text-decoration: underline;

}



/* 页脚样式 */

footer {

    text-align: center;

    margin-top: 30px;

}



footer a {

    color: #333;

    text-decoration: none;

}



footer a:hover {

    text-decoration: underline;

}



  /* 消息框样式 */

        #messageBox {

            display: none; /* 默认不显示 */

            padding: 10px;

            margin: 10px 0;

            border: 1px solid #dcdcdc;

            background-color: #f8f8f8;

            text-align: center;

        }

	</style>

<script>

// Function to copy the full URL and display the message

function copyFullURL(filename) {

    // Get the base URL of the current page, excluding any file name

    var baseURL = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);



    // Construct the full URL by combining the base URL with the filename

    var fullURL = baseURL + filename;



    // Use the Clipboard API to copy the text

    navigator.clipboard.writeText(fullURL)

        .then(() => {

            // Display the message in the messageBox div

            var messageBox = document.getElementById('messageBox');

            messageBox.textContent = 'Copied the full URL: ' + fullURL;

            messageBox.style.display = 'block'; // 显示消息框

        })

        .catch(err => {

            // Use alert to display the error message in a popup window

            alert('Failed to copy: ' + err);

        });

}

function logURL(url) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "log.php", true); // Replace 'log.php' with your PHP script name
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status !== 200) {
                console.error('Failed to log URL: ' + xhr.statusText);
            }
        }
    };
    xhr.send("url=" + encodeURIComponent(url));
}

// Add event listener to all links with the class 'cl'

document.querySelectorAll('.cl').forEach(link => {

    link.addEventListener('click', function(event) {

        var filename = this.getAttribute('href'); // Get the filename from the href attribute

        copyFullURL(filename); // Call the copy function

        event.preventDefault(); // Prevent the default action of the link

    });

});



</script>

</body>

</html>