<?php
require_once("includes/header.php");

if (!isset($_GET["id"])) {
    ErrorMessage::show("NO ID passed into page");
}

$entityId = $_GET["id"];
$entity = new Entity($con, $entityId);


$prevew = new PreviewProvider($con, $userLoggedIn);
echo $prevew->createPreviewVideo($entity);


$seasonProvider = new SeasonProvider($con, $userLoggedIn);
echo $seasonProvider->create($entity);

$categoryContainers = new CategoryConatiners($con, $userLoggedIn);
echo $categoryContainers->showCategory($entity->getCategoryId(), "You Can like <3");
