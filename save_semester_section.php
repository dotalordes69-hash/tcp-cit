<?php
session_start();
include 'db.php';

// Save multiple semesters
if(isset($_POST['save_semester'])){

    foreach($_POST['semester_name'] as $semester){

        $semester = strtoupper(trim($semester));

        if($semester != ''){

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO semester_sections (semester_name) VALUES (?)"
            );

            mysqli_stmt_bind_param($stmt, "s", $semester);
            mysqli_stmt_execute($stmt);
        }
    }

    header("Location: student_list.php");
    exit();
}
if(isset($_POST['save_school_year'])){

    foreach($_POST['school_year'] as $school_year){

        $school_year = trim($school_year);

        if($school_year == ''){
            continue;
        }

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO semester_sections (school_year) VALUES (?)"
        );

        mysqli_stmt_bind_param($stmt, "s", $school_year);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: student_list.php");
    exit();
}
// Save multiple sections
if(isset($_POST['save_section'])){

    foreach($_POST['section_name'] as $section_name){

        $section_name = strtoupper(trim($section_name));

        if($section_name == ''){
            continue;
        }

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO semester_sections (section_name) VALUES (?)"
        );

        mysqli_stmt_bind_param($stmt, "s", $section_name);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: student_list.php");
    exit();
}
?>
