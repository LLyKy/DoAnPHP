<?php
class PreviewProvider
{
    // Biến private, chỉ có thể truy cập từ bên trong lớp này
    private $con, $username;

    // Constructor, được gọi khi một đối tượng của lớp được tạo
    public function __construct($con, $username)
    {
        // Gán giá trị cho các biến thành viên $con và $username
        $this->con = $con;
        $this->username = $username;
    }

    // Hàm tạo video xem trước cho một danh mục cụ thể
    public function createCategoryPreviewVideo($categoryId){
        // Lấy các đối tượng (entities) từ một danh mục cụ thể
        $entitiesArray = EntityProvider::getEntities($this->con, $categoryId, 1);
        // Nếu không có đối tượng nào được trả về, hiển thị thông báo lỗi
        if(sizeof($entitiesArray)==0){
            ErrorMessage::show("No TV show to display ");
        }
        // Tạo và trả về video xem trước cho đối tượng đầu tiên trong mảng
        return $this->createPreviewVideo($entitiesArray[0]);
    }

    // Hàm tạo video xem trước cho các show truyền hình
    public function createTVShowPreviewVideo(){
        // Lấy các đối tượng (entities) của show truyền hình
        $entitiesArray = EntityProvider::getTVShowEntities($this->con, null, 1);
        // Nếu không có đối tượng nào được trả về, hiển thị thông báo lỗi
        if(sizeof($entitiesArray)==0){
            ErrorMessage::show("No TV show to display ");
        }
        // Tạo và trả về video xem trước cho đối tượng đầu tiên trong mảng
        return $this->createPreviewVideo($entitiesArray[0]);
    }

    // Hàm tạo video xem trước cho các bộ phim
    public function createMoviesPreviewVideo(){
        // Lấy các đối tượng (entities) của các bộ phim
        $entitiesArray = EntityProvider::getMoviesEntities($this->con, null, 1);
        // Nếu không có đối tượng nào được trả về, hiển thị thông báo lỗi
        if(sizeof($entitiesArray)==0){
            ErrorMessage::show("No movies to display ");
        }
        // Tạo và trả về video xem trước cho đối tượng đầu tiên trong mảng
        return $this->createPreviewVideo($entitiesArray[0]);
    }

    // Hàm tạo video xem trước dựa trên một đối tượng (entity) cụ thể
    public function createPreviewVideo($entity)
    {
        // Nếu đối tượng truyền vào là null, lấy một đối tượng ngẫu nhiên
        if ($entity == null) {
            $entity = $this->getRandomEntity();
        }

        // Lấy các thông tin cần thiết từ đối tượng để hiển thị
        $id = $entity->getId();
        $name = $entity->getName();
        $preview = $entity->getPreview();
        $thumbnail = $entity->getThumbnail();

        // Lấy video tương ứng với đối tượng cho người dùng hiện tại
        $videoId = VideoProvider::getEntityVideoForUser($this->con, $id, $this->username);
        $video = new Video($this->con, $videoId);
        $inProgress = $video->isInprogress($this->username);
        $playButtonText = $inProgress ? "Countinue watching ":"Play";
        $seasonEpisode = $video->getSeasonAndEpisode();
        $subHeading = $video->isMovie()? "": "<h4>$seasonEpisode</h4>";

        // Trả về HTML của phần giao diện video xem trước
   
        return "<div class='previewContainer'>

        <img src='$thumbnail' class='previewImage' hidden>

        <video autoplay muted class='previewVideo' onended='previewEnded()'>
            <source src='$preview' type='video/mp4'>
        </video>

        <div class='previewOverlay'>
            
            <div class='mainDetails'>
                <h3>$name</h3>
                $subHeading
                <div class='buttons'>
                    <button onclick='watchVideo($videoId)'><i class='fas fa-play'></i> $playButtonText</button>
                    <button onclick='volumeToggle(this)'><i class='fas fa-volume-mute'></i></button>
                </div>

            </div>

        </div>

    </div>";
    }

    // Hàm tạo giao diện xem trước cho một đối tượng (entity)
    public function createEntityPreviewSquare($entity)
    {
        // Lấy thông tin cần thiết từ đối tượng
        $id = $entity->getId();
        $thumbnail = $entity->getThumbnail();
        $name = $entity->getName();

        // Trả về HTML của giao diện xem trước cho một đối tượng
        return "<a href='entity.php?id=$id'>
                    <div class='previewContainer small'>
                        <img src='$thumbnail' title='$name'>
                    </div>
                </a>";
    }

    // Hàm lấy ngẫu nhiên một đối tượng từ cơ sở dữ liệu
    private function getRandomEntity()
    {
        $entity = EntityProvider::getEntities($this->con, null, 1);
        return $entity[0];
    }
}
?>
