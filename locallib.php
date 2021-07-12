<?php
function calculateTotal(array $scores, int $limit) {
    rsort($scores);
    $scores = array_slice($scores, 0, $limit);
    return array_sum($scores);
}

function isCheck(array $questions) {
    $not_attempted_regex = '/^NA|^N\/A|^no[t ]*Attemp.*/i';
    for($x = 0; $x < count($questions) - 1; $x+=2) {
        $score = $questions[$x];
        $remarks = trim($questions[$x + 1]);
        if ($score != 0 and preg_match($not_attempted_regex, $remarks)) {
            return true;
        }
    }
    return false;
}

function processResult(array $result, int $max_questions = 99999, string $paper_id = '') {
    $student_ids = array_unique(array_map(function($line) {return $line['student'];}, $result));
    $header_row1 = array('Student', '', '', '', 'Grading Info', '', '', '', '', 'Paper', '', '');
    $header_row2 = array('First name', 'Last name', 'username', 'Student ID', 'Check', 'Total Score', 'Rounded Total Score', 'Graded by', 'Time graded', 'Paper Id', 'Assignment Id', 'Assignment Name');
    $students = array();
    foreach ($student_ids as $student_id) {
        $student = array();
        $scores = array();
        $graded_by = '';
        $time_graded = '';
        $assignment_name = '';
        $assignment_id = '';
        $questions = array();
        foreach ($result as $key => $line) {
            if ($line['student'] == $student_id) {
                if (count($student) == 0) {
                    array_push($student, ucfirst(strtolower($line['firstname'])), ucfirst(strtolower($line['lastname'])), $line['student'], $line['idnumber']);
                    $graded_by = ucfirst(strtolower($line['grader']));
                    $time_graded = date('l, j F Y, g:i A', $line['modified']);
                    $assignment_id = $line['assignment_id'];
                    $assignment_name = $line['assignment'];
                }
                array_push($questions, floatval($line['score']), $line['remark']);
                array_push($scores, floatval($line['score']));
                unset($result[$key]);
                if (count($students) == 0) {
                    array_push($header_row1, $line['shortname'], '');
                    array_push($header_row2, 'Score', 'Feedback');
                }
            }
        }
	$check = isCheck($questions) ? 'True' : 'False';
        $total = calculateTotal($scores, $max_questions);
        array_push($student, $check, $total, ceil($total), $graded_by, $time_graded, $paper_id, $assignment_id, $assignment_name);
        $student = array_merge($student, $questions);
        array_push($students, $student);
    }

    return array_merge(array($header_row1), array($header_row2), $students);
}

function processPaperResults(array $results, array $max_questions = array(99999), array $paper_ids = array('')) {
    $processedResults = array();
    $headers = array(array(), array());
    $key = 0;
    foreach($results as $paper_id => $result) {
        $processedResult = processResult($result, ($key < count($max_questions)) ? $max_questions[$key] : $max_questions[count($max_questions) - 1], $paper_id);
        if (count($headers[0]) < count(array_slice($processedResult, 0, 2)[0])) {
            $headers = array_slice($processedResult, 0, 2);
        }
        $processedResults = array_merge($processedResults, array_slice($processedResult, 2));
	$key += 1;
    }
    return array_merge($headers, $processedResults);
}

function array_to_csv_download($array, $filename = 'export.csv', $delimiter=',') {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $f = fopen('php://output', 'w');
    // loop over the input array
    foreach ($array as $line) {
        // generate csv lines from the inner arrays
        fputcsv($f, $line, $delimiter);
    }
    fclose($f);
    // tell the browser it's going to be a csv file
    header('Content-Type: application/csv');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    echo ob_get_clean();
    die();
}
?>
