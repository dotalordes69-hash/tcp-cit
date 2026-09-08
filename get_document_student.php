<?php
include 'db.php';

if(isset($_GET['student_id'])){

    $student_id = mysqli_real_escape_string($conn, $_GET['student_id']);

    $query = mysqli_query($conn,"
        SELECT fullname, semester, school_year
        FROM students
        WHERE student_id = '$student_id'
        LIMIT 1
    ");

    if(mysqli_num_rows($query) > 0){

        $row = mysqli_fetch_assoc($query);

        echo json_encode([
            "status" => "success",
            "fullname" => $row['fullname'],
            "semester" => $row['semester'],
            "school_year" => $row['school_year']
        ]);

    }else{

        echo json_encode([
            "status" => "error"
        ]);

    }
}