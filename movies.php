<?php
    require_once("includes/header.php");

    $prevew = new PreviewProvider($con, $userLoggedIn);
    echo $prevew->createMoviesPreviewVideo();
    $containers = new CategoryConatiners($con, $userLoggedIn);
    echo $containers->showMoviesCategories();
    

?>