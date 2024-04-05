<?php
    // Yêu cầu tệp tin header.php từ thư mục includes, sẻ được chạy trước
    require_once("includes/header.php");

    // Tạo một đối tượng PreviewProvider và gọi phương thức createPreviewVideo
    $prevew = new PreviewProvider($con, $userLoggedIn);
    echo $prevew->createPreviewVideo(null);

    // Tạo một đối tượng CategoryContainers và gọi phương thức showAllCategories
    $containers = new CategoryConatiners($con, $userLoggedIn);
    echo $containers->showAllCategories();
    

?>