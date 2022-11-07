<?
require '../core/common.php';

$data = json_decode(file_get_contents('php://input'), true);
$str = $data['value'];
$result = select("SELECT id, title FROM questions WHERE title LIKE '%$str%' LIMIT 3");
$matches = json_encode($result);
echo $matches;
?>