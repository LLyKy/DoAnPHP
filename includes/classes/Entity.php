<?php
class Entity
{
    private $con, $sqlData;

    // Constructor của lớp Entity, được gọi khi một đối tượng của lớp được tạo
    public function __construct($con, $input)
    {
        // Gán giá trị cho biến thành viên $con
        $this->con = $con;

        // Kiểm tra nếu đối số $input là một mảng, có thể là dữ liệu từ cơ sở dữ liệu đã được truy vấn trước đó
        if (is_array($input)) {
            // Nếu là mảng, gán dữ liệu cho biến thành viên $sqlData
            $this->sqlData = $input;
        } else {
            // Nếu không phải mảng, thực hiện truy vấn để lấy dữ liệu từ cơ sở dữ liệu với id tương ứng
            $query = $this->con->prepare("SELECT * FROM entities WHERE id = :id");
            $query->bindValue(":id", $input);
            $query->execute();
            // Lấy dữ liệu từ kết quả truy vấn và gán cho biến thành viên $sqlData
            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }

    // Phương thức trả về id của đối tượng
    public function getId()
    {
        return $this->sqlData["id"];
    }

    // Phương thức trả về tên của đối tượng
    public function getName()
    {
        return $this->sqlData["name"];
    }

    // Phương thức trả về đường dẫn của hình ảnh thumbnail của đối tượng
    public function getThumbnail()
    {
        return $this->sqlData["thumbnail"];
    }

    // Phương thức trả về đường dẫn của video xem trước (preview) của đối tượng
    public function getPreview()
    {
        return $this->sqlData["preview"];
    }

    // Phương thức trả về id của danh mục (category) mà đối tượng thuộc về
    public function getCategoryId()
    {
        return $this->sqlData["categoryId"];
    }

    // Phương thức trả về thông tin về các mùa (seasons) của đối tượng nếu là một show truyền hình
    public function getSeasons()
    {
        // Truy vấn cơ sở dữ liệu để lấy thông tin về các video của đối tượng
        $query = $this->con->prepare("SELECT * FROM videos WHERE entityId=:id
                                    AND isMovie=0 ORDER BY season, episode ASC");
        $query->bindValue(":id", $this->getId());
        $query->execute();

        // Khởi tạo mảng để lưu thông tin về các mùa và video của mỗi mùa
        $seasons = array();
        $videos = array();
        $currentSeason = null;

        // Lặp qua kết quả truy vấn để xử lý dữ liệu
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // Kiểm tra xem có phải mùa mới không, nếu có thêm thông tin của mùa trước đó vào mảng $seasons
            if ($currentSeason != null && $currentSeason != $row["season"]) {
                $seasons[] = new Season($currentSeason, $videos);
                $videos = array();
            }
            // Cập nhật thông tin về mùa hiện tại
            $currentSeason = $row["season"];
            // Thêm video vào mảng $videos
            $videos[] = new Video($this->con, $row);
        }

        // Nếu còn video chưa được thêm vào một mùa, thêm vào mùa cuối cùng
        if (sizeof($videos) != 0) {
            $seasons[] = new Season($currentSeason, $videos);
        }

        // Trả về thông tin về các mùa của đối tượng
        return $seasons;
    }
}
?>
