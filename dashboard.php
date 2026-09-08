<?php

session_start();
include 'db.php';


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['login_id'])){

    header("Location: login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| SECTION CHART
|--------------------------------------------------------------------------
*/

$section_labels = [];
$section_counts = [];

$section_query = mysqli_query($conn, "
    SELECT
        section,
        COUNT(*) AS total
    FROM students
    GROUP BY section
    ORDER BY section
");

while($row = mysqli_fetch_assoc($section_query)){

    $section_labels[] = $row['section'];
    $section_counts[] = (int)$row['total'];

}


/*
|--------------------------------------------------------------------------
| SCHOOL YEAR CHART
|--------------------------------------------------------------------------
*/

$year_labels = [];
$year_counts = [];

$year_query = mysqli_query($conn, "
    SELECT
        school_year,
        COUNT(*) AS total
    FROM students
    GROUP BY school_year
    ORDER BY school_year
");

while($row = mysqli_fetch_assoc($year_query)){

    $year_labels[] = $row['school_year'];
    $year_counts[] = (int)$row['total'];

}


/*
|--------------------------------------------------------------------------
| TOTAL STUDENTS
|--------------------------------------------------------------------------
*/

$total_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students
    ")
)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| ENROLLMENT STATUS
|--------------------------------------------------------------------------
|
| ENROLLED
| = verified Registration Fee
|
| UNENROLLED
| = no verified Registration Fee
|
|--------------------------------------------------------------------------
*/

$total_enrolled_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students s

        WHERE EXISTS(

            SELECT 1
            FROM student_accounts sa

            INNER JOIN payment_history ph
                ON ph.student_id = sa.student_id

            WHERE sa.student_id = s.student_id

            AND sa.registration_fee = 1

            AND ph.particular = 'Registration Fee'

            AND ph.gcash_status = 'verified'
        )
    ")
)['total'] ?? 0;


$total_unenrolled_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students s

        WHERE NOT EXISTS(

            SELECT 1
            FROM student_accounts sa

            INNER JOIN payment_history ph
                ON ph.student_id = sa.student_id

            WHERE sa.student_id = s.student_id

            AND sa.registration_fee = 1

            AND ph.particular = 'Registration Fee'

            AND ph.gcash_status = 'verified'
        )
    ")
)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| ENROLLMENT PERCENTAGES
|--------------------------------------------------------------------------
*/

$enrollment_total =
    $total_enrolled_students +
    $total_unenrolled_students;


$enrolled_percent = $enrollment_total > 0
    ? round(
        ($total_enrolled_students / $enrollment_total) * 100,
        1
    )
    : 0;


$unenrolled_percent = $enrollment_total > 0
    ? round(
        ($total_unenrolled_students / $enrollment_total) * 100,
        1
    )
    : 0;


/*
|--------------------------------------------------------------------------
| PAYMENT STATUS
|--------------------------------------------------------------------------
|
| PAID
| total_amount <= 0
| OR
| balance <= 0
|
| PARTIAL
| total_amount > 0
| total_paid > 0
| balance > 0
|
| UNPAID
| total_amount > 0
| total_paid <= 0
| balance > 0
|
|--------------------------------------------------------------------------
*/

$total_paid_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM student_accounts

        WHERE
            COALESCE(total_amount, 0) <= 0

            OR

            COALESCE(balance, 0) <= 0
    ")
)['total'] ?? 0;


$total_partial_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM student_accounts

        WHERE
            COALESCE(total_amount, 0) > 0

            AND

            COALESCE(total_paid, 0) > 0

            AND

            COALESCE(balance, 0) > 0
    ")
)['total'] ?? 0;


$total_unpaid_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM student_accounts

        WHERE
            COALESCE(total_amount, 0) > 0

            AND

            COALESCE(total_paid, 0) <= 0

            AND

            COALESCE(balance, 0) > 0
    ")
)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| PAYMENT PERCENTAGE
|--------------------------------------------------------------------------
*/

$payment_total =
    $total_paid_students +
    $total_partial_students +
    $total_unpaid_students;


$paid_percent = $payment_total > 0
    ? round(
        ($total_paid_students / $payment_total) * 100,
        1
    )
    : 0;


/*
|--------------------------------------------------------------------------
| CREDENTIAL STATUS
|--------------------------------------------------------------------------
*/

$total_complete_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students
        WHERE status_credentials = 1
    ")
)['total'] ?? 0;


$total_incomplete_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students

        WHERE
            status_credentials IS NULL

            OR

            status_credentials = 0
    ")
)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| CREDENTIAL PERCENTAGE
|--------------------------------------------------------------------------
*/

$credential_total =
    $total_complete_students +
    $total_incomplete_students;


$complete_percent = $credential_total > 0
    ? round(
        ($total_complete_students / $credential_total) * 100,
        1
    )
    : 0;


/*
|--------------------------------------------------------------------------
| LET EXAMINEES
|--------------------------------------------------------------------------
|
| let_exam = 1
|--------------------------------------------------------------------------
*/

$total_let_exam = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students
        WHERE let_exam = 1
    ")
)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| LET PASSERS
|--------------------------------------------------------------------------
|
| A passer must also be an actual LET examinee.
|--------------------------------------------------------------------------
*/

$total_let_passers = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students
        WHERE let_exam = 1
        AND let_status = 1
    ")
)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| LET NON-PASSERS
|--------------------------------------------------------------------------
*/

$total_let_non_passers =
    max(
        0,
        $total_let_exam - $total_let_passers
    );


/*
|--------------------------------------------------------------------------
| LET PASSING RATE
|--------------------------------------------------------------------------
*/

$let_passing_rate = $total_let_exam > 0
    ? round(
        ($total_let_passers / $total_let_exam) * 100,
        1
    )
    : 0;


/*
|--------------------------------------------------------------------------
| LET PER SECTION
|--------------------------------------------------------------------------
*/

$let_section_labels = [];
$let_section_takers = [];
$let_section_passers = [];
$let_section_rates = [];


$let_section_query = mysqli_query($conn, "
    SELECT
        section,

        SUM(
            CASE
                WHEN let_exam = 1
                THEN 1
                ELSE 0
            END
        ) AS total_takers,

        SUM(
            CASE
                WHEN let_exam = 1
                AND let_status = 1
                THEN 1
                ELSE 0
            END
        ) AS total_passers

    FROM students

    GROUP BY section

    ORDER BY section
");


while($row = mysqli_fetch_assoc($let_section_query)){

    $section = $row['section'];

    $takers = (int)$row['total_takers'];

    $passers = (int)$row['total_passers'];


    $rate = $takers > 0
        ? round(
            ($passers / $takers) * 100,
            1
        )
        : 0;


    $let_section_labels[] = $section;

    $let_section_takers[] = $takers;

    $let_section_passers[] = $passers;

    $let_section_rates[] = $rate;

}


/*
|--------------------------------------------------------------------------
| OPTIONAL CREDENTIAL LIST
|--------------------------------------------------------------------------
*/

$all_credentials = [

    'TOR-Informative Copy',

    'CTC-TOR Granted',

    'GMC',

    'CTC Request',

    'PSA Birth/Marriage Certificate',

    '2x2 Recent Photo (Hardcopy)',

    '2x2 Recent Photo (Softcopy)',

    'Account Card',

];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
<title>TCP Dashboard</title>

<STYLE>
:root{
    --primary:#8B0000;
    --primary-light:#fdf2f2;
    --text:#2c3e50;
    --muted:#6c757d;
    --border:#e9ecef;
    --bg:#f5f7fa;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', Tahoma, sans-serif;
}

body{
    display:flex;
    background:var(--bg);
    color:var(--text);
    overflow-y:scroll;
}

/* MAIN */
.main{
    margin-left:240px;
    width:100%;
    padding:25px;
}

/* PAGE TITLE */
.page-title{
    font-size:30px;
    font-weight:700;
    color:var(--primary);
    margin-bottom:20px;
    letter-spacing:.3px;
}

/* TOPBAR */
.topbar{
    background:#fff;
    padding:16px 24px;
    border-radius:14px;
    border:1px solid var(--border);
    box-shadow:0 1px 4px rgba(0,0,0,.04);
}

.logout{
    background:var(--primary);
    border:none;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    font-weight:600;
    transition:.2s;
}

.logout:hover{
    opacity:.9;
}

/* STATS CARD */
.stats-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:20px;
    box-shadow:0 2px 6px rgba(0,0,0,.04);
    transition:.2s;
    border-left:4px solid var(--primary);
}

.stats-card:hover{
    transform:translateY(-2px);
}


.stats-title{
    font-size:12px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.8px;
    color:var(--muted);
}

.stats-value{
    font-size:32px;
    font-weight:700;
    color:var(--primary);
    margin-top:5px;
}

.stats-sub{
    font-size:12px;
    color:var(--muted);
    margin-top:6px;
}

/* MINI PROGRESS */
.progress-mini{
    height:6px;
    border-radius:20px;
    background:#edf1f5;
    margin-top:12px;
}

.progress-mini span{
    display:block;
    height:100%;
    background:linear-gradient(
        90deg,
        #8B0000,
        #c62828
    );
    border-radius:20px;
}

/* HEADER CARDS */
.card-box{
    background:#fff;
    padding:18px;
    border-radius:16px;
    border:1px solid var(--border);
    box-shadow:0 2px 6px rgba(0,0,0,.04);
}

.card-box h4{
    color:var(--muted);
    font-size:13px;
    margin-bottom:8px;
}

.card-box p{
    font-size:28px;
    font-weight:700;
    color:var(--primary);
}

/* QUICK ACTIONS */
.quick-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:24px;
    text-align:center;
    box-shadow:0 2px 6px rgba(0,0,0,.04);
    transition:.25s;
}

.quick-card:hover{
    transform:translateY(-3px);
    border-color:#d6dce3;
}

.quick-card h3{
    color:var(--primary);
    font-size:18px;
    margin-bottom:10px;
}

.quick-card p{
    font-size:13px;
    color:var(--muted);
}

/* ANNOUNCEMENT */
.announcement{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:24px;
    box-shadow:0 2px 6px rgba(0,0,0,.04);
}

/* CHART */
.chart-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 2px 6px rgba(0,0,0,.04);
}

.chart-card .card-header{
    background:#fff;
    color:var(--primary);
    border-bottom:1px solid var(--border);
    font-weight:700;
    padding:15px 20px;
}

.chart-wrapper{
    padding:15px;
    height:250px;
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>
<div class="main">
<div class="card chart-card mb-3">
    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-md-4">
                <h4 class="fw-bold text-danger mb-1">
                    CIT-TCP  Dashboard
                </h4>

                <small class="text-muted">
                    Academic and Enrollment Performance Overview
                </small>

                <div class="display-5 fw-bold text-danger mt-2">
                    <?= number_format($total_students) ?>
                </div>

                <small class="text-muted">
                    Total Students
                </small>
            </div>

            <div class="col-md-8">

                <div class="row g-3">

                    <div class="col-md-3">
                        <small class="text-muted">Enrolled Rate</small>

<div class="fw-bold text-success">
    <?= $enrolled_percent ?>%
</div>

<div style="font-size:12px;color:#6c757d;">
    Enrolled:
<strong><?= $total_enrolled_students ?></strong>
    /
    Total: <strong><?= $total_students ?></strong>
</div>

<div class="progress mt-1" style="height:6px;">
    <div class="progress-bar bg-success"
         style="width:<?= $official_percent ?>%">
    </div>
</div>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted">Payment Compliance</small>

<div class="fw-bold text-primary">
    <?= $paid_percent ?>%
</div>

<div style="font-size:12px;color:#6c757d;">
    Paid: <strong><?= $total_paid_students ?></strong>
    /
    Total: <strong><?= $total_students ?></strong>
</div>

<div class="progress mt-1" style="height:6px;">
    <div class="progress-bar bg-primary"
         style="width:<?= $paid_percent ?>%">
    </div>
</div>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted">Credentials</small>

<div class="fw-bold text-warning">
    <?= $complete_percent ?>%
</div>

<div style="font-size:12px;color:#6c757d;">
    Complete: <strong><?= $total_complete_students ?></strong>
    /
    Total: <strong><?= $total_students ?></strong>
</div>

<div class="progress mt-1" style="height:6px;">
    <div class="progress-bar bg-warning"
         style="width:<?= $complete_percent ?>%">
    </div>
</div>
                    </div>

                    <div class="col-md-3">
<small class="text-muted">LET Passing Rate</small>

<div class="fw-bold text-danger fs-5">
    <?= $let_passing_rate ?>%
</div>

<div style="font-size:12px;color:#6c757d;">
    Passers: <strong><?= $total_let_passers ?></strong>
    /
    Examinees: <strong><?= $total_let_exam ?></strong>
</div>

                        <div class="progress mt-1" style="height:6px;">
<div class="progress-bar"
     style="width:<?= $let_passing_rate ?>%">
    <?= $let_passing_rate ?>%
</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
<div class="row g-3">

    <div class="col-lg-8">
        <div class="card chart-card">
            <div class="card-header">
                Enrollment Trend
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card chart-card">
<div class="card-header">
    Enrollment Status
</div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="officialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="row g-3 mt-1">

    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-header">
                Students Per School Year
            </div>

            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="schoolYearChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card chart-card">
            <div class="card-header">
                Payment Status
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card chart-card">
            <div class="card-header">
                Credentials
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="credentialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="row g-3 mt-1">

    <div class="col-lg-12">
        <div class="card chart-card">
            <div class="card-header">
                LET Examination Performance Per Section
            </div>
            <div class="card-body">
                <div class="chart-wrapper" style="height:320px;">
                    <canvas id="letSectionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>


</div>
</body>
</html>
<script>new Chart(document.getElementById('officialChart'), {
    type: 'doughnut',
    data: {
        labels: [
            'Enrolled (<?= $total_enrolled_students ?>)',
            'Unenrolled (<?= $total_unenrolled_students ?>)'
        ],
        datasets: [{
            data: [
                <?= $total_enrolled_students ?>,
                <?= $total_unenrolled_students ?>
            ],
            backgroundColor:[
                '#198754',
                '#dc3545'
            ]
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                position:'right'
            }
        }
    }
});
</script>
<script>
new Chart(document.getElementById('paymentChart'), {
    type: 'pie',
    data: {
        labels: [
            'Paid (<?= $total_paid_students ?>)',
            'Partial (<?= $total_partial_students ?>)',
            'Unpaid (<?= $total_unpaid_students ?>)'
        ],
        datasets: [{
            data: [
                <?= $total_paid_students ?>,
                <?= $total_partial_students ?>,
                <?= $total_unpaid_students ?>
            ]
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                position:'right'
            }
        }
    }
});
</script>
<script>
new Chart(document.getElementById('credentialChart'), {
    type: 'doughnut',
    data: {
        labels: [
            'Complete (<?= $total_complete_students ?>)',
            'Incomplete (<?= $total_incomplete_students ?>)'
        ],
        datasets: [{
            data: [
                <?= $total_complete_students ?>,
                <?= $total_incomplete_students ?>
            ]
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                position:'right'
            }
        }
    }
});
</script>
<script>
new Chart(document.getElementById('sectionChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($section_labels) ?>,
        datasets: [{
            label: 'Students',
            data: <?= json_encode($section_counts) ?>
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false
    }
});
</script>
<script>
new Chart(document.getElementById('enrollmentChart'),{
    type:'line',
    data:{
        labels: <?= json_encode($year_labels) ?>,
        datasets:[{
            label:'Total Enrollees',
            data: <?= json_encode($year_counts) ?>,
            borderColor:'#8B0000',
            backgroundColor:'rgba(139,0,0,.15)',
            tension:.3,
            fill:true
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                display:true
            }
        }
    }
});
</script>

<script>
new Chart(document.getElementById('letSectionChart'), {
    type:'bar',
    data:{
        labels: <?= json_encode($let_section_labels) ?>,
        datasets:[
            {
                label:'LET Takers',
                data: <?= json_encode($let_section_takers) ?>,
                backgroundColor:'#ffc107'
            },
            {
                label:'LET Passers',
                data: <?= json_encode($let_section_passers) ?>,
                backgroundColor:'#198754'
            },
            {
                label:'Pass Rate (%)',
                data: <?= json_encode($let_section_rates) ?>,
                type:'line',
                borderColor:'#dc3545',
                backgroundColor:'#dc3545',
                yAxisID:'y1'
            }
        ]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,

        plugins:{
            legend:{
                position:'bottom'
            },
            tooltip:{
                callbacks:{
                    afterLabel:function(context){

                        let rates = <?= json_encode($let_section_rates) ?>;

                        return 'Pass Rate: ' +
                               rates[context.dataIndex] +
                               '%';
                    }
                }
            }
        },

        scales:{
            y:{
                beginAtZero:true,
                title:{
                    display:true,
                    text:'Students'
                }
            },
            y1:{
                beginAtZero:true,
                position:'right',
                max:100,
                grid:{
                    drawOnChartArea:false
                },
                title:{
                    display:true,
                    text:'Pass Rate (%)'
                }
            }
        }
    }
});
</script>
<script>
new Chart(document.getElementById('schoolYearChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($year_labels) ?>,
        datasets: [{
            label: 'Students',
            data: <?= json_encode($year_counts) ?>,
            backgroundColor: '#8B0000',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>