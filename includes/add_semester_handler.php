<?php

include '../db.php';

if(isset($_POST['semester_name'])){

    foreach($_POST['semester_name'] as $semester){

        $semester = trim($semester);

        if(empty($semester)){
            continue;
        }

        $semester = mysqli_real_escape_string(
            $conn,
            strtoupper($semester)
        );

        $check = mysqli_query(
            $conn,
            "SELECT id
             FROM semester_sections
             WHERE semester_name='$semester'"
        );

        if(mysqli_num_rows($check) > 0){
            continue;
        }

        mysqli_query(
            $conn,
            "INSERT INTO semester_sections
            (semester_name)
            VALUES
            ('$semester')"
        );
    }
}

header("Location: ../student_list.php");
exit;