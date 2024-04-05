<?php
    require_once("includes/header.php");

    if(!isset($_GET["id"])){
        ErrorMessage::show("No id passed to page");
    }


    $prevew = new PreviewProvider($con, $userLoggedIn);
    echo $prevew->createCategoryPreviewVideo($_GET["id"]);
    $containers = new CategoryConatiners($con, $userLoggedIn);
    echo $containers->showCategory($_GET["id"]);
    

?>