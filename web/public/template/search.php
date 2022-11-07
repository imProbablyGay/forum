<?php
require_once '_header.php';
require_once './core/LoadMore.php';
$query = $_GET['q'];

$display = new CommonQuestion();

$search = new LoadMore(null, $display);
$search -> search_query_count = "SELECT count(*) AS count FROM questions WHERE title LIKE '%$query%'";
$search -> count();
$search -> search_query = "SELECT q.id,q.title,q.date,q.views,u.login,u.id AS uID FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE title LIKE '%$query%' ORDER BY views DESC LIMIT $search->limit_args";
$displayS = $search -> display();
?>

<body>
    <div class="container search search-block" data-search-type='search_page'>
        <div class="row">
            <div class="col-12">
                <h4>Результаты поиска: "<mark><?=$query?></mark>" (<?=$search->count?>)</h4>
                <hr>
            </div>
            <?=$displayS?>
        </div>
    </div>
</body>