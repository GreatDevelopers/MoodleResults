<?php
session_start();

include('database.php');
include('locallib.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <!--Fontawesome icons -->
    <link rel="stylesheet" href="fontawesome/css/all.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="bootstrap/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="bootstrap/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="bootstrap/bootstrap.min.js"></script>
</head>
<body>

<?php
$result = null;
$export_url = '';
unset($_SESSION['assignment']);
unset($_SESSION['paper']);
$max_questions = array(99999);
if (isset($_GET['max-questions'])) {
    $max_questions = array_map('trim', explode(',', $_GET['max-questions']));
}

if (isset($_GET['assignment'])) {
    $result_ = getResultByAssignmentId($_GET['assignment']);
    if ($result_) {
        if (!isset($_SESSION['assignment'])) {
            $_SESSION['assignment'] = array('assignment_' . $_GET['assignment'] => processResult($result_, $max_questions[0]));
        } else {
            $_SESSION['assignment'] = array_merge($_SESSION['assignment'], array('assignment_' . $_GET['assignment'] => processResult($result_, $max_questions[0])));
        }
        $result = $_SESSION['assignment']['assignment_' . $_GET['assignment']];
        $export_url = 'export.php?assignment=' . $_GET['assignment'] . '&max-questions=' . $_GET['max-questions'];
    }
} elseif (isset($_GET['paper'])) {
    $paper_ids = array_map('trim', explode(',', $_GET['paper']));
    $result_ = getResultByPaperIds($paper_ids);
    if ($result_) {
        if (!isset($_SESSION['paper'])) {
            $_SESSION['paper'] = array('paper_' . $_GET['paper'] => processPaperResults($result_, $max_questions, $paper_ids));
        } else {
            $_SESSION['paper'] = array_merge($_SESSION['paper'], array('paper_' . $_GET['paper'] => processPaperResults($result_, $max_questions, $paper_ids)));
        }
        $result = $_SESSION['paper']['paper_' . $_GET['paper']];
        $export_url = 'export.php?paper=' . $_GET['paper'] . '&max-questions=' . $_GET['max-questions'];
    }
}

?>
<div class="container-fluid">
    <div class="card border-success mt-4">
      <div class="card-header bg-secondary">
        <div class="row">
          <a class="btn btn-large btn-secondary" href="index.php"><h3><i class="fas fa-home"></i></h3></a>
          <h3 class="text-center col align-self-center">Result</h3>
	  <?php if ($result) { ?>
            <a class="btn btn-large btn-secondary" href="<?php echo $export_url; ?>"><h3><i class="fas fa-download"></i></h3></a>
          <?php } ?>
        </div>
      </div>
      <?php if (isset($_GET['assignment']) or isset($_GET['paper'])) {
          if ($result) {
              include('display_result.php');
              displayResult($result);
          } else {
              echo '<div class="alert alert-warning"><strong>Invalid Assignment/Paper Id!</strong></div>';
              include('input_form.php');
          }
      }else {
          include('input_form.php');
      } ?>
</div>
</body>
</html>
