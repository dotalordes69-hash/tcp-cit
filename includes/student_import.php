<?php

if (isset($_POST['import_students'])) {

    if (!empty($_FILES['excel_file']['tmp_name'])) {

        $file = $_FILES['excel_file']['tmp_name'];

        $default_tuition = floatval($_POST['default_total_amount'] ?? 0);

        if (($handle = fopen($file, "r")) !== false) {

            $row = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {

                $row++;

                // Skip header row
                if ($row == 1) {
                    continue;
                }

                $student_id  = trim($data[0] ?? '');
                $fullname    = trim($data[1] ?? '');
                $contact_no  = trim($data[2] ?? '');
                $email       = trim($data[3] ?? '');
                $semester    = trim($data[4] ?? '');
                $section     = trim($data[5] ?? '');
                $school_year = trim($data[6] ?? '');

                $tuition_fee = isset($data[7]) && is_numeric($data[7])
                    ? floatval($data[7])
                    : $default_tuition;

                // Skip empty rows
                if ($student_id === '' || $fullname === '') {
                    continue;
                }

                // Default email if blank
                if ($email === '') {
                    $email = 'N/A';
                }

                // Check if student already exists
                $check = mysqli_prepare(
                    $conn,
                    "SELECT id FROM students WHERE student_id = ?"
                );

                mysqli_stmt_bind_param($check, "s", $student_id);
                mysqli_stmt_execute($check);
                mysqli_stmt_store_result($check);

                if (mysqli_stmt_num_rows($check) == 0) {

                    mysqli_stmt_close($check);

                    // Insert student
                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO students
                        (
                            student_id,
                            fullname,
                            contact_no,
                            email_address,
                            semester,
                            school_year,
                            section
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssssss",
                        $student_id,
                        $fullname,
                        $contact_no,
                        $email,
                        $semester,
                        $school_year,
                        $section
                    );

                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Create student account
                    $balance = $tuition_fee;

                    $stmt_acc = mysqli_prepare(
                        $conn,
                        "INSERT INTO student_accounts
                        (
                            student_id,
                            total_amount,
                            total_paid,
                            balance
                        )
                        VALUES (?, ?, 0, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt_acc,
                        "sdd",
                        $student_id,
                        $tuition_fee,
                        $balance
                    );

                    mysqli_stmt_execute($stmt_acc);
                    mysqli_stmt_close($stmt_acc);

                } else {

                    mysqli_stmt_close($check);

                    // Optional: Update tuition if student already exists
                    $stmt_acc = mysqli_prepare(
                        $conn,
                        "UPDATE student_accounts
                         SET total_amount = ?,
                             balance = ? - total_paid
                         WHERE student_id = ?"
                    );

                    mysqli_stmt_bind_param(
                        $stmt_acc,
                        "dds",
                        $tuition_fee,
                        $tuition_fee,
                        $student_id
                    );

                    mysqli_stmt_execute($stmt_acc);
                    mysqli_stmt_close($stmt_acc);
                }
            }

            fclose($handle);

            header("Location: student_list.php?import_success=1");
            exit();
        }
    }
}
?>