<?php
class CategoryConatiners
{
    private $con, $username;

    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }

    //Phương thức này truy xuất tất cả các danh mục từ cơ sở dữ liệu và tạo ra mã HTML để hiển thị chúng. Nó sử dụng phương thức getCategoryHtml() để tạo HTML cho mỗi danh mục.
    public function showAllCategories()
    {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();
        $html = "<div class='previewCategories'>";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // $html .= $row["name"];
            $html .= $this->getCategoryHtml($row, null, true, true);
        }
        return $html . "</div>";
    }

    public function showTVShowCategories()
    {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();
        $html = "<div class='previewCategories'>
                    <h1>TV SHOWS</h1>
        
        ";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // $html .= $row["name"];
            $html .= $this->getCategoryHtml($row, null, true, false);
        }
        return $html . "</div>";
    }
    public function showMoviesCategories()
    {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();
        $html = "<div class='previewCategories'>
                    <h1>Movies</h1>
        
        ";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // $html .= $row["name"];
            $html .= $this->getCategoryHtml($row, null, false, true);
        }
        return $html . "</div>";
    }

    public function showCategory($categoryId, $title = null)
    {
        $query = $this->con->prepare("SELECT * FROM categories WHERE id = :id");
        $query->bindValue(":id", $categoryId);
        $query->execute();
        $html = "<div class='previewCategories noScroll'>";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, $title, true, true);
        }
        return $html . "</div>";
    }


    public function getCategoryHtml($sqlData, $title, $tvShows, $movies)
    {
        $categoryId = $sqlData["id"];
        $title = $title == null ? $sqlData["name"] : $title;

        if ($tvShows && $movies) {
            $entities = EntityProvider::getEntities($this->con, $categoryId, 30);
        } else if ($tvShows) {
            $entities = EntityProvider::getTVShowEntities($this->con, $categoryId, 30);
            
        } else {
            //
            $entities = EntityProvider::getMoviesEntities($this->con, $categoryId, 30);

        }
        if (sizeof($entities) == 0) {
            return;
        }
        $entitiesHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach ($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
            //$entitiesHtml .= "" . $entity->getName();
        }
        // return $entitiesHtml . "<br/>";
        return "<div class='category'>
            <a href='category.php?id=$categoryId'>
            <h3>$title</h3>
            </a>
            <div class='entities'>
                $entitiesHtml;
            </div>
        </div>";
    }
}
