<?php
    // Nạp các tệp tin cần thiết vào mã PHP
    require_once("includes/config.php");
    require_once("includes/classes/FormSanitizer.php");
    require_once("includes/classes/Constants.php");
    require_once("includes/classes/Account.php");

    // Tạo một đối tượng Account với đối số là đối tượng kết nối $con tới cơ sở dữ liệu
    $account = new Account($con);

    // Kiểm tra xem nút gửi form đã được nhấn chưa
    if(isset($_POST["submitButton"])){
        // Lấy giá trị tên người dùng từ form và sử dụng hàm sửa dữ liệu để làm sạch và chuẩn hóa dữ liệu
        $username = FormSanitizer::sanitizeFormUsername($_POST["username"]);
        // Lấy giá trị mật khẩu từ form và sử dụng hàm sửa dữ liệu để làm sạch và chuẩn hóa dữ liệu
        $password = FormSanitizer::sanitizeFormPassword($_POST["passsword"]);
        
        // Thực hiện phương thức đăng nhập từ đối tượng Account
        $success = $account->login($username, $password);

        // Nếu đăng nhập thành công
        if($success){
            // Lưu tên người dùng đã đăng nhập vào session
            $_SESSION["userLoggedIn"] = $username;
            // Chuyển hướng người dùng đến trang chính sau khi đăng nhập thành công
            header("Location: index.php");
        }
    }

    // Hàm này được sử dụng để lấy giá trị của một trường dữ liệu từ form sau khi đã gửi đi
    function getInputValue($name){
        if(isset($_POST[$name])){
            echo $_POST[$name];
        } 
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to DLTflix</title>
    <link rel="stylesheet" href="./assets/style/style.css">
</head>

<body>
    <div class="signInContainer">
        <div class="column">
            <div class="header">
                <img src="./assets/images/logo.png" alt="logo">
                <h3>Sign In</h3>
                <span>to countinue to DLTflix</span>
            </div>
            <form method="POST" action="">

                <?php echo  $account->getError(Constants::$LoginFailed); ?>

                <input type="text" name="username" placeholder="User Name: " value="<?php getInputValue("username") ?>"
                    required>

                <input type="password" name="passsword" placeholder="Password: " required>

                <input type="submit" name="submitButton" value="SUBMIT">


            </form>
            <a href="register.php" class="signInMessage">Need an account?<span style="color: blue;"> Sign up
                    here!</span></a>
        </div>
    </div>
</body>

</html>