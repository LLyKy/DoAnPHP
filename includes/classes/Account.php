<?php
// Định nghĩa một lớp Account để xử lý các hoạt động liên quan đến tài khoản.
class Account
{
    // Các thuộc tính con và errorArray được khai báo, con là đối tượng PDO kết nối đến cơ sở dữ liệu, errorArray lưu trữ các thông báo lỗi.
    private $con;
    private $errorArray = array();

    // Phương thức __construct() được sử dụng để khởi tạo một đối tượng Account, nhận đối số là kết nối đến cơ sở dữ liệu.
    public function __construct($con)
    {
        $this->con = $con;
    }

    // Phương thức updateDetails() được sử dụng để cập nhật chi tiết của tài khoản.
    public function updateDetails($fn, $ln, $em, $un)
    {
        // Các thông tin cập nhật được truyền vào là firstName, lastName, email, và username.
        // Các phương thức validateFirstName(), validateLastName(), và validateNewEmails() được gọi để kiểm tra tính hợp lệ của các thông tin cập nhật.
        $this->validateFirstName($fn);
        $this->validateLastName($ln);
        $this->validateNewEmails($em, $un);
        // Nếu không có lỗi, truy vấn cập nhật được thực hiện và kết quả trả về.
        if (empty($this->errorArray)) {
            $query = $this->con->prepare("UPDATE users SET firstName=:fn, lastName=:ln, email=:em WHERE username=:un");
            $query->bindValue(":fn", $fn);
            $query->bindValue(":ln", $ln);
            $query->bindValue(":em", $em);
            $query->bindValue(":un", $un);
            return $query->execute();
        }
        return false;
    }

    // Phương thức register() được sử dụng để đăng ký một tài khoản mới.
    public function register($fn, $ln, $un, $em, $em2, $pw, $pw2)
    {
        $this->validateFirstName($fn);
        $this->validateLastName($ln);
        $this->validateUsername($un);
        $this->validateEmails($em, $em2);
        $this->validatePassword($pw, $pw2);
        // Nếu không có lỗi, chi tiết tài khoản mới được thêm vào cơ sở dữ liệu.
        if (empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $ln, $un, $em, $pw);
        }
        return false;
    }

    // Phương thức login() được sử dụng để xác thực tài khoản khi đăng nhập.
    public function login($un, $pw)
    {
        // Mật khẩu được mã hóa bằng thuật toán SHA-512 trước khi so sánh với dữ liệu trong cơ sở dữ liệu.
        $pw = hash("sha512", $pw);
        $query = $this->con->prepare("SELECT * FROM users WHERE username=:un AND password=:pw");
        $query->bindValue(":un", $un);
        $query->bindValue(":pw", $pw);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        // Nếu thông tin đăng nhập chính xác, một bản ghi về lịch sử đăng nhập được thêm vào cơ sở dữ liệu.
        if ($query->rowCount() == 1) {
            $query = $this->con->prepare("INSERT INTO history_logs(user_id, status) VALUES (:ud, :stt)");
            $query->bindValue(":ud", $results["id"]);
            $query->bindValue(":stt", "Thanh Cong");
            $query->execute();
            return true;
        }
        array_push($this->errorArray, Constants::$LoginFailed);
        return false;
    }

    // Phương thức insertUserDetails() được sử dụng để thêm chi tiết tài khoản mới vào cơ sở dữ liệu.
    private function insertUserDetails($fn, $ln, $un, $em, $pw)
    {
        // Mật khẩu được mã hóa bằng thuật toán SHA-512 trước khi lưu vào cơ sở dữ liệu.
        $pw = hash("sha512", $pw);
        $query = $this->con->prepare("INSERT INTO users (firstName, lastName, username, email, password) VALUES (:fn, :ln, :un, :em, :pw)");
        $query->bindValue(":fn", $fn);
        $query->bindValue(":ln", $ln);
        $query->bindValue(":un", $un);
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);
        return $query->execute();
    }

    // Các phương thức validateFirstName(), validateLastName(), validateUsername(), validateEmails(), validateNewEmails(), validatePassword(), validateOldPassword(), getError(), và getFirstError() được sử dụng để kiểm tra và lấy thông tin lỗi trong quá trình xử lý tài khoản.

    public function validateFirstName($firstName)
    {
        if (strlen($firstName) < 2 || strlen($firstName) > 25) {
            array_push($this->errorArray, Constants::$firstNameCharacters);
        }
    }

    public function validateLastName($lastName)
    {
        if (strlen($lastName) < 2 || strlen($lastName) > 25) {
            array_push($this->errorArray, Constants::$lastNameCharacters);
        }
    }

    public function validateUsername($username)
    {
        if (strlen($username) < 2 || strlen($username) > 25) {
            array_push($this->errorArray, Constants::$usernameCharacters);
            return;
        }
        $query = $this->con->prepare("SELECT * FROM users WHERE username=:un");
        $query->bindValue(":un", $username);
        $query->execute();
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$usernameTaken);
        }
    }

    private function validateEmails($email, $email2)
    {
        if ($email != $email2) {
            array_push($this->errorArray, Constants::$emailDonMatch);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
        $query = $this->con->prepare("SELECT * FROM users WHERE email=:em");
        $query->bindValue(":em", $email);
        $query->execute();
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$emailTaken);
        }
    }

    private function validateNewEmails($email, $username)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
        $query = $this->con->prepare("SELECT * FROM users WHERE email=:email AND username!=:username");
        $query->bindValue(":email", $email);
        $query->bindValue(":username", $username);
        $query->execute();
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$emailTaken);
        }
    }

    public function getError($error)
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class='errorMessage' >$error</span>";
        }
    }

    public function validatePassword($password, $password2)
    {
        if ($password != $password2) {
            array_push($this->errorArray, Constants::$passwordsDonMatch);
            return;
        }
        if (strlen($password) < 5 || strlen($password) > 25) {
            array_push($this->errorArray, Constants::$passwordLength);
        }
    }
    public function getFirstError(){
        if(!empty($this->errorArray)){
            return $this->errorArray[0];
        }
    }

    // public function updatePassword($oldPw, $pw, $pw2, $un){
    //     $this->validateOldPassword($oldPw, $un);
    //     $this->validatePassword($pw,$pw2);
    //     if (empty($this->errorArray)) {
    //         $query = $this->con->prepare("UPDATE users SET passsword=:pw 
    //         WHERE username=:un");
    //         $pw = hash("sha512", $pw);
    //         $query->bindValue(":pw", $pw);
    //         $query->bindValue(":un", $un);
    //         return $query->execute();
    //     }
    //     return false;

    // }

    public function updatePassword($oldPw, $pw, $pw2, $un) {
        $this->validateOldPassword($oldPw, $un);
        $this->validatePassword($pw, $pw2);

        if(empty($this->errorArray)) {
            $query = $this->con->prepare("UPDATE users SET password=:pw WHERE username=:un");
            $pw = hash("sha512", $pw);
            $query->bindValue(":pw", $pw);
            $query->bindValue(":un", $un);

            return $query->execute();
        }

        return false;
    }
    public function validateOldPassword($oldPw, $un){
        $pw = hash("sha512", $oldPw);
        $query = $this->con->prepare("SELECT * FROM users WHERE username=:un AND password=:pw");
        $query->bindValue(":un", $un);
        $query->bindValue(":pw", $pw);
        $query->execute();
        if($query->rowCount()==0){
                array_push($this->errorArray, Constants::$passwordIncorrect);
        }
    }

}