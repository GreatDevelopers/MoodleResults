<?php

function getDatabaseConnection() {
   include('config.php');
   // connect to the database
   $db = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
   // Check connection
   if ($db->connect_error) {
       echo '<div class="alert alert-danger"><strong>Something went wrong!</strong> Please try after some time or contact server-admin. </div>';
       die();
   }
   return $db;
}

function getResultByAssignmentId($id) {
    include('config.php');
    $db = getDatabaseConnection();
    $sql = 'SELECT    ggf.id AS ggfid, crs.shortname AS course, cm.id AS assignment_id, asg.name AS assignment,
                      gd.name AS guide, ggc.shortname, ggf.score, ggf.remark, ggf.criterionid, rubm.username AS grader,
                      stu.id AS userid, stu.idnumber AS idnumber, stu.firstname, stu.lastname,
                      stu.username AS student, gin.timemodified AS modified
              FROM ' . $db_config['tables_prefix'] . 'course AS crs
              JOIN ' . $db_config['tables_prefix'] . 'course_modules AS cm ON crs.id = cm.course
              JOIN ' . $db_config['tables_prefix'] . 'assign AS asg ON asg.id = cm.instance
              JOIN ' . $db_config['tables_prefix'] . 'context AS c ON cm.id = c.instanceid
              JOIN ' . $db_config['tables_prefix'] . 'grading_areas AS ga ON c.id=ga.contextid
              JOIN ' . $db_config['tables_prefix'] . 'grading_definitions AS gd ON ga.id = gd.areaid
              JOIN ' . $db_config['tables_prefix'] . 'gradingform_guide_criteria AS ggc ON (ggc.definitionid = gd.id)
              JOIN ' . $db_config['tables_prefix'] . 'grading_instances AS gin ON gin.definitionid = gd.id
              JOIN ' . $db_config['tables_prefix'] . 'assign_grades AS ag ON ag.id = gin.itemid
              JOIN ' . $db_config['tables_prefix'] . 'user AS stu ON stu.id = ag.userid
              JOIN ' . $db_config['tables_prefix'] . 'user AS rubm ON rubm.id = gin.raterid
              JOIN ' . $db_config['tables_prefix'] . 'gradingform_guide_fillings AS ggf ON (ggf.instanceid = gin.id)
              AND (ggf.criterionid = ggc.id)
              WHERE cm.id = ? AND gin.status = 1
              ORDER BY lastname ASC, firstname ASC, userid ASC, ggc.sortorder ASC,
              ggc.shortname ASC';
    $stmt = $db->prepare($sql); 
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
       return;
    }

    // output data of each row
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getResultByPaperId($id) {
    include('config.php');
    $db = getDatabaseConnection();

    $sql = 'SELECT id from ' . $db_config['tables_prefix'] . 'assign WHERE `name` LIKE ? ';
    $stmt = $db->prepare($sql); 
    $pattern = '%' . $id . '%';
    $stmt->bind_param("s", $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = $result->fetch_all();

    usort($ids, function($a, $b) {
      if ($a[0]==$b[0]) return 0;
      return ($a[0]<$b[0])?-1:1;
    });
    $id = $ids[0][0];

    $sql = 'SELECT    ggf.id AS ggfid, crs.shortname AS course, cm.id AS assignment_id, asg.name AS assignment,
                      gd.name AS guide, ggc.shortname, ggf.score, ggf.remark, ggf.criterionid, rubm.username AS grader,
                      stu.id AS userid, stu.idnumber AS idnumber, stu.firstname, stu.lastname,
                      stu.username AS student, gin.timemodified AS modified
              FROM ' . $db_config['tables_prefix'] . 'course AS crs
              JOIN ' . $db_config['tables_prefix'] . 'course_modules AS cm ON crs.id = cm.course
              JOIN ' . $db_config['tables_prefix'] . 'assign AS asg ON asg.id = cm.instance
              JOIN ' . $db_config['tables_prefix'] . 'context AS c ON cm.id = c.instanceid
              JOIN ' . $db_config['tables_prefix'] . 'grading_areas AS ga ON c.id=ga.contextid
              JOIN ' . $db_config['tables_prefix'] . 'grading_definitions AS gd ON ga.id = gd.areaid
              JOIN ' . $db_config['tables_prefix'] . 'gradingform_guide_criteria AS ggc ON (ggc.definitionid = gd.id)
              JOIN ' . $db_config['tables_prefix'] . 'grading_instances AS gin ON gin.definitionid = gd.id
              JOIN ' . $db_config['tables_prefix'] . 'assign_grades AS ag ON ag.id = gin.itemid
              JOIN ' . $db_config['tables_prefix'] . 'user AS stu ON stu.id = ag.userid
              JOIN ' . $db_config['tables_prefix'] . 'user AS rubm ON rubm.id = gin.raterid
              JOIN ' . $db_config['tables_prefix'] . 'gradingform_guide_fillings AS ggf ON (ggf.instanceid = gin.id)
              AND (ggf.criterionid = ggc.id)
              WHERE asg.id = ? AND gin.status = 1
              ORDER BY lastname ASC, firstname ASC, userid ASC, ggc.sortorder ASC,
              ggc.shortname ASC';
    $stmt = $db->prepare($sql); 
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
       return;
    }

    // output data of each row
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getResultsByPaperId($id) {
    include('config.php');
    $db = getDatabaseConnection();

    $sql = 'SELECT    ggf.id AS ggfid, crs.shortname AS course, cm.id AS assignment_id, asg.name AS assignment,
                      gd.name AS guide, ggc.shortname, ggf.score, ggf.remark, ggf.criterionid, rubm.username AS grader,
                      stu.id AS userid, stu.idnumber AS idnumber, stu.firstname, stu.lastname,
                      stu.username AS student, gin.timemodified AS modified
              FROM ' . $db_config['tables_prefix'] . 'course AS crs
              JOIN ' . $db_config['tables_prefix'] . 'course_modules AS cm ON crs.id = cm.course
              JOIN ' . $db_config['tables_prefix'] . 'assign AS asg ON asg.id = cm.instance
              JOIN ' . $db_config['tables_prefix'] . 'context AS c ON cm.id = c.instanceid
              JOIN ' . $db_config['tables_prefix'] . 'grading_areas AS ga ON c.id=ga.contextid
              JOIN ' . $db_config['tables_prefix'] . 'grading_definitions AS gd ON ga.id = gd.areaid
              JOIN ' . $db_config['tables_prefix'] . 'gradingform_guide_criteria AS ggc ON (ggc.definitionid = gd.id)
              JOIN ' . $db_config['tables_prefix'] . 'grading_instances AS gin ON gin.definitionid = gd.id
              JOIN ' . $db_config['tables_prefix'] . 'assign_grades AS ag ON ag.id = gin.itemid
              JOIN ' . $db_config['tables_prefix'] . 'user AS stu ON stu.id = ag.userid
              JOIN ' . $db_config['tables_prefix'] . 'user AS rubm ON rubm.id = gin.raterid
              JOIN ' . $db_config['tables_prefix'] . 'gradingform_guide_fillings AS ggf ON (ggf.instanceid = gin.id)
              AND (ggf.criterionid = ggc.id)
              WHERE asg.name LIKE ? AND gin.status = 1
              ORDER BY asg.name ASC, lastname ASC, firstname ASC, userid ASC, ggc.sortorder ASC,
              ggc.shortname ASC';
    $stmt = $db->prepare($sql);
    $paper_id = '%' . $id . '%';
    $stmt->bind_param("s", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
       return;
    }

    // output data of each row
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getResultByPaperIds(array $ids) {
    $results = array();
    foreach($ids as $id) {
        $result = getResultsByPaperId($id);
        if ($result) {
            $results[$id] =  $result;
        }
    }
    return $results;
}
?>
