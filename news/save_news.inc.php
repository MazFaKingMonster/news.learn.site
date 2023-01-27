<?php

$title = $_POST["title"];
$category = abs((int)$_POST["category"]);
$description = $_POST["description"];
$source = $_POST["source"];

if(empty($title) or empty($description)){
    $err_msg = "Заполни ебаную новость, сука!";
}else{
    if(!$news->saveNews($title, $category, $description, $source)){
        $err_msg = "Кто-то накосячил!";
    }else{
        header("Location: news.php");
        exit;
    }
}

