<?php

include '../db.php';

if(isset($_POST['section_name'])){

    foreach($_POST['section_name'] as $section){

        $section = strtoupper(trim($section));

        if(empty($section)){
            continue;
        }

        $check = mysqli_query(
            $conn,
            "SELECT id
             FROM semester_sections
             WHERE section_name='$section'"
        );

        if(mysqli_num_rows($check) > 0){
            continue;
        }

        mysqli_query(
            $conn,
            "INSERT INTO semester_sections(section_name)
             VALUES('$section')"
        );
    }
}

header("Location: ../student_list.php");
exit;