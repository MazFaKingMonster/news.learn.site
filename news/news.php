<?php
require "NewsDB.class.php";
$news = new NewsDB();
$err_msg = "";

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    require "save_news.inc.php";
}
if (isset($_GET["del"])){
    require "delete_news.inc.php";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Новостная лента</title>
	<meta charset="utf-8" />
</head>
<body>
  <h1>Новости-Хуёвости</h1>
  <?php
    if($err_msg){
        echo "<h3>$err_msg</h3>";
    }
  ?>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
    Заголовок новости:<br />
    <input type="text" name="title" /><br />
    Выберите категорию:<br />
    <select name="category">
        <?php
        foreach ($news as $value => $name)
            echo "<option value='$value'>$name</option>"
        ?>
    </select>
    <br />
    Текст новости:<br />
    <textarea name="description" cols="50" rows="5"></textarea><br />
    Источник:<br />
    <input type="text" name="source" /><br />
    <br />
    <input type="submit" value="Добавить!" />
</form>
<?php
require "get_news.inc.php";
?>
</body>
</html>