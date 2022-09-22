<?php
include "INewsDB.class.php";
class NewsDB implements INewsDB{

    const DB_NAME = "news.db";
    private $_db;

    function __construct(){

        $this->_db = new SQLite3(self::DB_NAME);

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

    function saveNews($title, $category, $description, $source){
        $dt = time();

        $sql = "INSERT INTO msgs (title, category, description, source, dt)
                VALUES ('$title', $category, '$description', '$source', $dt)";
        return $this->_db->exec($sql);
    }

    function db2_arr($data){
        $arr = [];

        while($row = $data->fetchArray(SQLITE3_ASSOC)){
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

}