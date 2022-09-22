<?php
$items = $news->getNews();
if ($items === false):
    $err_msg = "Пиздец в ленте!";
elseif (!count($items)):
    $err_msg = "Ничего не произошло!";
else:
    foreach ($items as $item):

        $dt = date("d-m-Y H:i:s", $item["dt"]);

        $desc = nl2br($item["description"]);
        echo <<<ITEM
            <h3>{$item["title"]}</h3>
            <p>
                $desc<br>
                {$item["category"]} @ $dt<hr>
                {$item["source"]}
            </p>
        <p align="right">
        <a href="news.php?del={$item['id']}">Удалить</a>
        </p>
ITEM;
        endforeach;
endif;