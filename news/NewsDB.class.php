<?php
include "INewsDB.class.php";

class NewsDB implements INewsDB, IteratorAggregate{

    const DB_NAME = "news.db";

    const RSS_NAME = "rss.xml";
    const RSS_TITLE = "Новости-Хуёвости!";
    const RSS_LINK = "http://news.learn.site/news/news.php";

    private $_db;
    protected $_items;

    function __construct(){

        $this->_db = new PDO('sqlite:'.self::DB_NAME);

        if (!filesize(self::DB_NAME)){
            try {
                $sql = "CREATE TABLE msgs(
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    title TEXT,
                                    category INTEGER,
                                    description TEXT,
                                    source TEXT,
                                    dt INTEGER)";

                if (!$this->_db->exec($sql)) {
                    throw new Exception("Не сделал таблицу msgs");
                }

                $sql = "CREATE TABLE category(
                    id INTEGER,
                    name TEXT)";

                if (!$this->_db->exec($sql)) {
                    throw new Exception("Не сделал таблицу category");
                }

                $sql = "INSERT INTO category(id, name)
                    SELECT 1 as id, 'Политика' as name
                    UNION SELECT 2 as id, 'Культура' as name
                    UNION SELECT 3 as id, 'Спорт' as name";

                if (!$this->_db->exec($sql)) {
                    throw new Exception("Не вставил в таблицу category");
                }
            }catch (Exception $e){
                echo "Програмисты уже получают пизды!";
                die($e->getMessage());
            }
        }
        $this->getCategories();
    }
    function __destruct(){
        unset($this->_db);
    }

    function __get($name){
        if ($name == "db"){
            return $this->_db;
        }else{
        throw new Exception("Неправильное имя БД!");
        }
    }
    function __set($name, $value){
        throw new Exception("Ты не охуел?");
    }

    private function getCategories(){
        $sql ="SELECT id, name FROM category";
        $result = $this->_db->query($sql);
        while($row = $result->fetch(PDO::FETCH_ASSOC))
            $this->_items[$row['id']] = $row['name'];
    }
    function getIterator(){
        return new ArrayIterator($this->_items);
    }

    function saveNews($title, $category, $description, $source){
        $dt = time();

        $sql = "INSERT INTO msgs (title, category, description, source, dt)
                VALUES ('$title', $category, '$description', '$source', $dt)";

        $result = $this->_db->exec($sql);

        if (!$result) return false;

        $this->create_rss();
        return true;
    }

    function db2_arr($data){
        $arr = [];

        while($row = $data->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        };

        return $arr;
    }
    function getNews(){
        $sql = "SELECT msgs.id as id, title, category.name as category, description, source, dt
        FROM msgs, category
        WHERE category.id = msgs.category
        ORDER BY msgs.id DESC";

        $items = $this->_db->query($sql);
        if (!$items){
            return false;
        }
        return $this->db2_arr($items);
    }
    function deleteNews($id){
    $sql = "DELETE FROM msgs WHERE id=$id";
    return $this->_db->exec($sql);
    }

   function create_rss(){

        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $rss = $dom->createElement("rss");
        $version = $dom->createAttribute("version");
        $version->value = "2.0";
        $rss->appendChild($version);
        $dom->appendChild($rss);

        $channel = $dom->createElement("channel");
        $title = $dom->createElement("title", self::RSS_TITLE);
        $link = $dom->createElement("link", self::RSS_LINK);
        $channel->appendChild($title);
        $channel->appendChild($link);
        $rss->appendChild($channel);

        $lenta = $this->getNews();
        if (!$lenta) {
            return false;
        }

            foreach ($lenta as $news) {
                $item = $dom->createElement("item");
                $title = $dom->createElement("title", $news['title']);
                $category = $dom->createElement("category", $news['category']);
                $description = $dom->createElement("description");
                $cdata = $dom->createCDATASection($news['description']);
                $description->appendChild($cdata);
                $link_text = self::RSS_LINK . '?id=' . $news['id'];
                $link = $dom->createElement("link", $link_text);
                $dt = date('r', $news['dt']);
                $pub_date = $dom->createElement("pub_date", $dt);
                $item->appendChild($title);
                $item->appendChild($link);
                $item->appendChild($description);
                $item->appendChild($pub_date);
                $item->appendChild($category);
                $channel->appendChild($item);
            }
            $dom->save(self::RSS_NAME);
    }

}