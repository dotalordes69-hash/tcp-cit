<?php

include '../db.php';

if(isset($_POST['school_year'])){

    foreach($_POST['school_year'] as $school_year){

        $school_year = strtoupper(trim($school_year));

        if(empty($school_year)){
            continue;
        }

        $check = mysqli_query(
            $conn,
            "SELECT id
             FROM semester_sections
             WHERE school_year='$school_year'"
        );

        if(mysqli_num_rows($check) > 0){
            continue;
        }

        mysqli_query(
            $conn,
            "INSERT INTO semester_sections (school_year)
             VALUES ('$school_year')"
        );
    }
}

header("Location: ../student_list.php");
exit;