<?php
session_start();
require '../core/common.php';
require '../core/QuestionPage.php';

$data = json_decode(file_get_contents('php://input'), true);
$Q_ID = $data['id'];
$exclude_query;

if (!empty($data['exclude'])) {
    foreach($data['exclude'] as $id) {
        $exclude_query .= "AND a.id != $id";
    }
}

$question = new Answers($Q_ID);
$remained_answers = $question->get_answers('all', $exclude_query, true);

echo $remained_answers;