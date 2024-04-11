<?php
class VideoProvider
{
    // Phương thức lấy video tiếp theo dựa trên video hiện tại
    public static function getUpNext($con, $currentVideo)
    {
        // Truy vấn cơ sở dữ liệu để lấy video tiếp theo
        $query = $con->prepare("SELECT * FROM videos 
                WHERE entityId=:entityId AND id != :videoId
                AND (
                    (season = :season AND episode > :episode) OR season > :season 
                )
                ORDER BY season, episode ASC LIMIT 1");
        $query->bindValue(":entityId", $currentVideo->getEntityId());
        $query->bindValue(":season", $currentVideo->getSeasonNumber());
        $query->bindValue(":episode", $currentVideo->getEpisodeNumber());
        $query->bindValue(":videoId", $currentVideo->getId());

        $query->execute();

        // Nếu không có video nào sau video hiện tại, chọn video phổ biến nhất
        if ($query->rowCount() == 0) {
            $query = $con->prepare("SELECT * FROM videos
                WHERE season <= 1 AND episode <= 1
                AND id != :videoId
                ORDER BY views DESC LIMIT 1");
            $query->bindValue(":videoId", $currentVideo->getId());
            $query->execute();
        }
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return new Video($con, $row);
    }

    // Phương thức lấy video của entity cho người dùng
    public static function getEntityVideoForUser($con, $entityId, $username)
    {
        // Truy vấn cơ sở dữ liệu để lấy video của entity cho người dùng
        $query = $con->prepare("SELECT videoId FROM videoprogress 
                                INNER JOIN videos
                                ON videoprogress.videoId = videos.id 
                                WHERE videos.entityId = :entityId
                                AND videoprogress.username = :username
                                ORDER BY videoprogress.dateModified DESC 
                                LIMIT 1");
        $query->bindValue(":entityId", $entityId);
        $query->bindValue(":username", $username);
        $query->execute();
        // Nếu không có dữ liệu, chọn video đầu tiên của entity
        if ($query->rowCount() == 0) {
            $query = $con->prepare("SELECT * FROM videos
            WHERE entityId = :entityId
            ORDER BY season, episode ASC LIMIT 1 ");
            $query->bindValue(":entityId", $entityId);
            $query->execute();
        }
        return $query->fetchColumn();
    }
}
