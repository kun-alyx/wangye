<?php
/* 显示错误信息
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
session_start();
$valid_username = 'admin';
$valid_password_hash = password_hash('admin', PASSWORD_DEFAULT);

if (!class_exists('ZipArchive')) {
    die('ZipArchive class not found');
}
// 检查是否提交了登录表单
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['origin']) && $_POST['origin'] === 'index' ) {
	
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 验证用户名和密码
    if ($username === $valid_username && password_verify($password, $valid_password_hash)) {
        // 密码验证成功，设置登录状态
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        // 登录失败，显示错误消息
        echo '登录失败：用户名或密码错误。';
    }
}

// 检查用户是否已登录
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

// 文件列表
$files = ['index.php', 'header.php', 'content.php', 'footer.php'];

// 检查是否有文件被选中以编辑
$editFile = isset($_GET['file']) && in_array($_GET['file'], $files) ? $_GET['file'] : null;
$fileDirectory = './'; // 文件所在目录的相对路径，假设在当前目录下
$editFilePath = $fileDirectory . $editFile; // 构建完整的文件路径

// 检查文件是否存在
if (!file_exists($editFilePath)) {
    echo '文件不存在或路径不正确。';
    exit;
}

// 加载文件内容
$fileContent = $editFile ? file_get_contents($editFilePath) : '';



// 检查是否有文件内容提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content']) && isset($_POST['file'])) {
    $fileToSave = $_POST['file'];
    $contentToSave = $_POST['content'];

    // 确保文件在允许编辑的列表中
    if (in_array($fileToSave, $files)) {
        $filePathToSave = $fileDirectory . $fileToSave;
        // 保存文件内容
        file_put_contents($filePathToSave, $contentToSave);
        echo '文件保存成功。';
    } else {
        echo '错误：不允许编辑该文件。';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['origin']) && $_POST['origin'] === 'zip' ) {
    $remote_file_url = $_POST['file_url'];
    $local_file = basename($remote_file_url);
    $copy = copy($remote_file_url, $local_file);
    if (!$copy) {
        echo '<div style="color: red;">Download failed</div>';
    } else {
        $zip = new ZipArchive();
        $res = $zip->open($local_file);
        if ($res === TRUE) {
            $zip->extractTo('./');
            $zip->close();
            echo '<div style="color: green;">Download successful and extracted</div>';
        } else {
            echo '<div style="color: red;">Download failed or unzip failed</div>';
        }
        echo '<script>progressBar.style.display = "none" </script>';
    }
}

if (isset($_POST['logout'])&&  isset($_POST['origin']) && $_POST['origin'] === 'out') {
    // 销毁会话
    session_destroy();
    // 重定向到登录页
    header('Location: index.php');
    exit;
}

?>
<?php include 'header.php'; ?>
<ul>
    <?php foreach ($files as $file): ?>
        <li><a href="?file=<?php echo $file; ?>"><?php echo $file; ?></a></li>
    <?php endforeach; ?>
</ul>

<?php if ($editFile): ?>
    <h2>编辑 <?php echo $editFile; ?></h2>
    <form action="" method="post">
        <textarea name="content" rows="20" cols="80"><?php echo htmlspecialchars($fileContent); ?></textarea><br>
        <input type="hidden" name="file" value="<?php echo $editFile; ?>">
        <input type="submit" value="保存">
    </form>
<?php endif; ?>
   <style>#progressBar {  display: none;}</style>
    <script>function aa(){progressBar.style.display = "block"}</script>
<div id="progressBar">downloading</div>
<form method="POST" onsubmit="startDownload(); return false;">
    <label for="file_url">输入zip地址:</label>
    <textarea type="text" name="file_url" id="file_url"></textarea>
		 <input type="hidden" name="origin" value="zip">
    <button id="downloadBtn" type="submit" onclick="aa()">下载并解压</button>
</form>
<p>
<form action="" method="post">
 <input type="hidden" name="origin" value="out">
    <input type="submit" name="logout" value="退出登录">	
</form>
</p>
<?php include 'footer.php'; ?>
