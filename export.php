<?php
session_start();
include('database.php');
include('locallib.php');

$max_questions = array(99999);
if (isset($_GET['max-questions'])) {
    $max_questions = array_map('trim', explode(',', $_GET['max-questions']));
}

if (!isset($_GET['assignment']) and !isset($_GET['paper'])) {
    echo 'GET parameter `assignment` and `paper` missing';
} else {
    $result = array();
    $filename = '';
    if (isset($_GET['assignment'])) {
        if (!(isset($_SESSION['assignment']) and isset($_SESSION['assignment']['assignment_' . $_GET['assignment']]))) {
            $result_ = getResultByAssignmentId($_GET['assignment']);
            if (!isset($_SESSION['assignment'])) {
                $_SESSION['assignment'] = array('assignment_' . $_GET['assignment'] => processResult($result_, $max_questions[0]));
            } else {
                $_SESSION['assignment'] = array_merge($_SESSION['assignment'], array('assignment_' . $_GET['assignment'] => processResult($result_, $max_questions[0])));
            }
        }
        $result = $_SESSION['assignment']['assignment_' . $_GET['assignment']];
        $filename = 'results_' . $_GET['assignment'] . '.csv';
    } elseif (isset($_GET['paper'])) { 
        if (!(isset($_SESSION['paper']) and isset($_SESSION['paper']['paper_' . $_GET['paper']]))) {
            $paper_ids = array_map('trim', explode(',', $_GET['paper']));
            $result_ = getResultByPaperIds($paper_ids);
            if (!isset($_SESSION['paper'])) {
                $_SESSION['paper'] = array('paper_' . $_GET['paper'] => processPaperResults($result_, $max_questions, $paper_ids));
            } else {
                $_SESSION['paper'] = array_merge($_SESSION['paper'], array('paper_' . $_GET['paper'] => processPaperResults($result_, $max_questions, $paper_ids)));
            }
        }
        $result = $_SESSION['paper']['paper_' . $_GET['paper']];
        $filename = 'results_' . $_GET['paper'] . '.csv';
    }

    array_to_csv_download($result, $filename);
}

?>
