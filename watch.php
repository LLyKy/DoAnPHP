<?php
$hideNav = true; // Ẩn thanh điều hướng
require_once("includes/header.php"); // Bao gồm file header.php vào trang

if (!isset($_GET["id"])) { // Nếu không có ID được chuyển đến trang
    ErrorMessage::show("NO ID passed into page"); // Hiển thị thông báo lỗi
}
$video = new Video($con, $_GET["id"]); // Tạo đối tượng video với ID được chuyển đến
$video->incrementViews(); // Tăng số lượt xem cho video

$upNextVideo = VideoProvider::getUpNext($con, $video); // Lấy video tiếp theo từ cơ sở dữ liệu


// xử lý 
?>
<div class="watchContainer">

<div class="videoControls watchNav">
    <button onclick="goBack()"><i class="fas fa-arrow-left"></i></button>
    <h1><?php echo $video->getTitle(); ?></h1>
</div>

<div class="videoControls upNext" style="display:none;">

    <button onclick="restartVideo();"><i class="fas fa-redo"></i></button>

    <div class="upNextContainer">
        <h2>Up next:</h2>
        <h3><?php echo $upNextVideo->getTitle(); ?></h3>
        <h3><?php echo $upNextVideo->getSeasonAndEpisode(); ?></h3>

        <button class="playNext" onclick="watchVideo(<?php echo $upNextVideo->getId(); ?>)">
            <i class="fas fa-play"></i> Play
        </button>
    </div>

</div>

<video controls autoplay onended="showUpNext()">
    <source src='<?php echo $video->getFilePath(); ?>' type="video/mp4">
</video>
</div>
<script>
initVideo("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn; ?>");
</script>