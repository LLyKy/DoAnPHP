<?php

ob_start(); // Bắt đầu bộ đệm đầu ra để lưu trữ đầu ra.
session_start(); // Khởi động hoặc tiếp tục một phiên làm việc (session).

date_default_timezone_set("Europe/London"); // Thiết lập múi giờ mặc định cho ứng dụng là "Europe/London".
try{
    $con = new PDO("mysql:dbname=lykyflix;host=localhost","root",""); // Kết nối đến cơ sở dữ liệu MySQL
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // Thiết lập chế độ báo lỗi cho đối tượng kết nối.
}
catch(PDOException $e){
    exit("Connection failed " . $e->getMessage()); // Kết thúc chương trình và hiển thị thông điệp lỗi nếu kết nối tới cơ sở dữ liệu thất bại.
}

?>