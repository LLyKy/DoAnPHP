<?php
class Video
{
    private $con;
    private $sqlData;
    private $entity;

    // Constructor của lớp Video
    public function __construct($con, $input)
    {
        $this->con = $con;

        // Kiểm tra input là một mảng hay một ID video
        if (is_array($input)) {
            $this->sqlData = $input; // Nếu là mảng, sử dụng dữ liệu từ mảng này
        } else {
            // Nếu không phải mảng, truy vấn cơ sở dữ liệu để lấy dữ liệu của video với ID tương ứng
            $query = $this->con->prepare("SELECT * FROM videos WHERE id = :id");
            $query->bindValue(":id", $input, PDO::PARAM_INT);
            $query->execute();
            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

        // Khởi tạo đối tượng Entity với entityId tương ứng của video
        $this->entity = new Entity($con, $this->sqlData["entityId"]);
    }

    // Phương thức trả về ID của video
    public function getId()
    {
        return $this->sqlData["id"];
    }

    // Phương thức trả về tiêu đề của video
    public function getTitle()
    {
        return $this->sqlData["title"];
    }

    // Phương thức trả về mô tả của video
    public function getDescription()
    {
        return $this->sqlData["description"];
    }

    // Phương thức trả về tên của video (nếu có)
    public function getName()
    {
        return isset($this->sqlData["name"]) ? $this->sqlData["name"] : null;
    }

    // Phương thức trả về đường dẫn của file video
    public function getFilePath()
    {
        return $this->sqlData["filePath"];
    }

    // Phương thức trả về ảnh đại diện của video
    public function getThumbnail()
    {
        return $this->entity->getThumbnail();
    }

    // Phương thức trả về số tập của video (nếu là phim truyền hình)
    public function getEpisodeNumber()
    {
        return $this->sqlData["episode"];
    }

    // Phương thức trả về số mùa của video (nếu là phim truyền hình)
    public function getSeasonNumber()
    {
        return $this->sqlData["season"];
    }

    // Phương thức trả về entityId của video
    public function getEntityId()
    {
        return $this->sqlData["entityId"];
    }

    // Phương thức tăng số lượt xem cho video
    public function incrementViews()
    {
        try {
            $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
            $query->bindValue(":id", $this->getId(), PDO::PARAM_INT);
            $query->execute();
        } catch (PDOException $e) {
            // Xử lý lỗi, ghi log hoặc ném ra ngoại lệ
        }
    }

    // Phương thức trả về chuỗi biểu thị mùa và tập của video (nếu là phim truyền hình)
    public function getSeasonAndEpisode()
    {
        if ($this->isMovie()) {
            return null;
        }

        $season = $this->getSeasonNumber();
        $episode = $this->getEpisodeNumber();
        return "Season $season, Episode $episode";
    }

    // Phương thức kiểm tra xem video có phải là phim hay không
    public function isMovie()
    {
        return $this->sqlData["isMovie"] == 1;
    }

    // Phương thức kiểm tra xem người dùng đã bắt đầu xem video chưa
    public function isInprogress($username)
    {
        try {
            $query = $this->con->prepare("SELECT * FROM videoprogress WHERE videoId=:videoId AND username=:username");
            $query->bindValue(":videoId", $this->getId(), PDO::PARAM_INT);
            $query->bindValue(":username", $username);
            $query->execute();
            return $query->rowCount() != 0;
        } catch (PDOException $e) {
            // Xử lý lỗi, ghi log hoặc ném ra ngoại lệ
        }
    }

    // Phương thức kiểm tra xem người dùng đã xem hết video chưa
    public function hasSeen($username)
    {
        try {
            $query = $this->con->prepare("SELECT * FROM videoprogress WHERE videoId=:videoId AND username=:username AND finished=1");
            $query->bindValue(":videoId", $this->getId(), PDO::PARAM_INT);
            $query->bindValue(":username", $username);
            $query->execute();
            return $query->rowCount() != 0;
        } catch (PDOException $e) {
            // Xử lý lỗi, ghi log hoặc ném ra ngoại lệ
        }
    }
}
