<?php
   
    require_once("includes/config.php");
    require_once("includes/classes/FormSanitizer.php");
    require_once("includes/classes/Constants.php");
    require_once("includes/classes/Account.php");

   

    // Tạo một thể hiện của lớp Account với tham số $con, có thể là kết nối đến cơ sở dữ liệu.
    $account = new Account($con);

    
    //Kiểm tra xem nút "submitButton" đã được nhấn hay chưa bằng cách sử dụng isset($_POST["submitButton"])
    //Nếu nút "submitButton" đã được nhấn, lấy các giá trị từ các trường nhập liệu và làm sạch chúng bằng các phương thức từ lớp FormSancitizer.
    if(isset($_POST["submitButton"])){
     
        $firstName = FormSanitizer:: sanitizeFormString( $_POST["firstName"]);
        $lastName = FormSanitizer:: sanitizeFormString( $_POST["lastName"]);
        $username = FormSanitizer:: sanitizeFormUsername( $_POST["username"]);
        $email = FormSanitizer:: sanitizeFormEmail( $_POST["email"]);
        $email2 = FormSanitizer:: sanitizeFormEmail( $_POST["email2"]);
        $password = FormSanitizer:: sanitizeFormPassword( $_POST["passsword"]);
        $password2 = FormSanitizer:: sanitizeFormPassword( $_POST["passsword2"]);

        // echo $firstName. " " . $lastName;
        //Gọi phương thức register của đối tượng Account với các tham số đã làm sạch.
       //$success =  $account->register($firstName, $lastName, $username, $email, $email2, $password, $password2);
       $success =  $account->register($firstName, $lastName, $username, $email, $email2, $password, $password2);

       // Nếu đăng ký thành công (trả về true), thiết lập biến $_SESSION["userLoggedIn"] và chuyển hướng người dùng đến trang index.php.
       if($success){
        $_SESSION["userLoggedIn"] = $username;

        header("Location: index.php");
       }
       
    }

    //Định nghĩa hàm getInputValue($name) để lấy giá trị của trường nhập liệu từ $_POST, nếu có.
    function getInputValue($name){
       if(isset($_POST[$name])){
            echo $_POST[$name];
    } 
}
   
//sử dụng phương thức getError của đối tượng Account để hiển thị thông báo lỗi.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to LKflix</title>
    <link rel="stylesheet" href="./assets/style/style.css">
</head>

<body>
    <div class="signInContainer">
        <div class="column">
            <div class="header">
                <img src="./assets/images/logo.png" title="Logo" alt="Site logo">
                <h3>Sign Up</h3>
                <span>to countinue to DLTflix</span>
            </div>
            <form method="POST">
                <?php echo  $account->getError(Constants::$firstNameCharacters); ?>
                <input type="text" name="firstName" placeholder="First Name: " value="<?php getInputValue("firstName") ?>"required>

                <?php echo $account->getError(Constants::$lastNameCharacters); ?>
                <input type="text" name="lastName" placeholder="Last Name: " value="<?php getInputValue("lastName") ?>" required>

                <?php echo $account->getError(Constants::$usernameCharacters); ?>
                <?php echo $account->getError(Constants::$usernameTaken); ?>

                <input type="text" name="username" placeholder="User Name: " value="<?php getInputValue("username") ?>"required>
                <?php  echo $account->getError(Constants::$emailDonMatch); ?>
                <?php echo $account->getError(Constants::$emailInvalid); ?>
                <?php echo $account->getError(Constants::$emailTaken); ?>

                <input type="email" name="email" placeholder="Email: " value="<?php getInputValue("email") ?>" required>
                <input type="email" name="email2" placeholder="Confirm email: " value="<?php getInputValue("email2") ?>"
                    required>

                <?php echo $account->getError(Constants::$passwordsDonMatch); ?>
                <?php echo $account->getError(Constants::$passwordLength); ?>


                <input type="password" name="passsword" placeholder="Password: " required>
                <input type="password" name="passsword2" placeholder="Confirm Password: " required>

                <input type="submit" name="submitButton" value="SUBMIT">
            </form>
            <a href="login.php" class="signInMessage">Already have an account? Sign in here!</a>
        </div>
    </div>
</body>

</html>