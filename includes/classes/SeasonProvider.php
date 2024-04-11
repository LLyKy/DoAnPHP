<?php

class SeasonProvider
{
    
    private $con, $username;

    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }
    
    public function create($entity)
    {
        $seasons =  $entity->getSeasons();
        if (sizeof($seasons) == 0) {
            return;
        }
        $seasonsHtml = "";
    
        foreach ($seasons as $season) {
            $seasonNumber = $season->getSeasonNumber();
    
            $videoHtml = "";
            $counter = 0; // Counter to track number of videos in the row
            foreach ($season->getVideos() as $video) {  // Use getVideos()
                // Increment the counter
                $counter++;
    
                // Create video square HTML
                $videoHtml .= $this->createVideoSquare($video);
    
                // If 7 videos are added, add a line break
                if ($counter % 7 == 0) {
                    $videoHtml .= "<div style='clear:both;'></div>"; // Add a line break after every 7 videos
                }
            }
    
            // Add season HTML
            $seasonsHtml .= "<div class='season'>
                <h3>Season $seasonNumber</h3>
                <div class='videos'>
                    $videoHtml
                </div>
            </div>";
        }
        return $seasonsHtml;
    }
    
    
    private function createVideoSquare($video)
    {
        $id = $video->getId();
        $thumbnail = $video->getThumbnail();
        $name = $video->getName();
        $title = $video->getTitle();
        $description = $video->getDescription();
        $episodeNumber = $video->getEpisodeNumber();
        $hasSeen = $video->hasSeen($this->username) ? "<i class='fas fa-check-circle seen'></i>" : "";
        
        return "<a href='watch.php?id=$id'>
                <div class='episodeContainer'>
                    <div class='contents'>
                        <img src='$thumbnail'>
                        <div class='videoInfo'>
                            <h4>$episodeNumber. $title</h4>
                            <span>$description</span>
                            $hasSeen
                        </div>
                    </div>
                </div>
            </a>";
    }
}