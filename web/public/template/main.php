<?php
require_once "template/_header.php";
require_once './core/LoadMore.php';

$display = new CommonQuestion();

$search = new LoadMore(null, $display);
$cur_date = date('m.Y'); 
$search -> search_query_count = "SELECT count(*) AS count FROM questions WHERE date LIKE '%$cur_date%'";
$search -> count();

$search -> search_query = "SELECT q.id,q.title,q.date,q.views,u.login,u.id AS uID FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE date LIKE '%$cur_date%' ORDER BY views DESC LIMIT $search->limit_args";
$displayS = $search -> display();
?>


<body>
    <div class="container main search-block" data-search-type='main_page'>
        <div class="row">
            <div class="col-12">
                <h4>Популярные вопросы за этот месяц</h4>
            </div>
            <?=$displayS?>
        </div>
    </div>
</body>
