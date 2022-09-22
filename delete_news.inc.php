<?php
$id = abs((int)$_GET["del"]);
if ($id){
    if (!$news->deleteNews($id)){
        $err_msg = "Не получилось удалить - ИДИ ФИКСИ ПРИДУРОК!";
    }else{
        header("Location: news.php");
    exit;
    }
}