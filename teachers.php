<?php
session_start();

/* DATABASE CONNECTION */
$conn = mysqli_connect("localhost","root","","tcp_db");

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

/* ADD TEACHER */
if(isset($_POST['add'])){
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $date_hired = $_POST['date_hired'];

    mysqli_query($conn, "INSERT INTO teachers (fullname,email,course,date_hired) 
    VALUES ('$name','$email','$course','$date_hired')");

    header("Location: teachers.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Teacher Management</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
<style>
body{
    font-family:'Segoe UI';
    background:#f4f6f9;
    margin:0;
    display:flex;
}

/* CONTAINER */
.container{
    padding:20px;
    margin-left:250px; /* space for sidebar */
    width: calc(100% - 250px);
    box-sizing:border-box;
}

/* BUTTON */
button{
    background:#b30000;
    color:#fff;
    padding:10px 15px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
}

button:hover{
    background:#ff3333;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    margin-top:20px;
}

th, td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
}

th{
    background:#b30000;
    color:#fff;
}

tr:hover{
    background:#f9f9f9;
}
/* MODAL */
.modal{
    display:none; 
    position:fixed;
    z-index:10000;
    left:0;
    top:0;
    width:100%;
    height:100%;
    overflow:auto;
    background: rgba(0,0,0,0.5);
}

/* MODAL CONTENT */
.modal-content{
    background:#fff;
    margin:5% auto;
    padding:20px;
    border-radius:10px;
    width:400px;         /* fixed modal width */
    max-width:90%;       /* responsive on small screens */
    box-shadow:0 5px 15px rgba(0,0,0,0.3);
    position:relative;
    box-sizing:border-box; /* ensures padding doesn’t overflow */
}

/* CLOSE BUTTON */
.close{
    position:absolute;
    top:10px;
    right:15px;
    font-size:20px;
    font-weight:bold;
    color:#999;
    cursor:pointer;
}

.close:hover{
    color:#b30000;
}

/* FORM FIELDS */
.modal-content input, 
.modal-content select, 
.modal-content textarea, 
.modal-content button{
    width:100%;              /* make inputs fill modal width */
    padding:10px;
    margin:6px 0;
    border:1px solid #ccc;
    border-radius:5px;
    box-sizing:border-box;    /* ensures padding doesn't overflow */
}

.modal-content label{
    font-weight:bold;
    margin-top:10px;
    display:block;
}

.modal-content button{
    width:100%;
    margin-top:10px;
    background:#b30000;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    transition:0.3s;
}

.modal-content button:hover{
    background:#ff3333;
}


</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">

<h2>Teacher Management</h2>

<!-- ADD TEACHER BUTTON -->
<button id="openModal">➕ Add Teacher</button>

<!-- DISPLAY TEACHERS -->
<table>
<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Course</th>
    <th>Date Hired</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM teachers ORDER BY id DESC");
while($row = mysqli_fetch_assoc($result)){
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['fullname']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['course']; ?></td>
    <td><?php echo $row['date_hired']; ?></td>
</tr>
<?php } ?>

</table>

</div>

<!-- THE MODAL -->
<div id="teacherModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add New Teacher</h3>
    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="course" placeholder="Course" required>
        <label>Date Hired:</label>
        <input type="date" name="date_hired" required>
        <button name="add">Add Teacher</button>
    </form>
  </div>
</div>

<script>
// GET ELEMENTS
var modal = document.getElementById("teacherModal");
var btn = document.getElementById("openModal");
var span = document.getElementsByClassName("close")[0];

// OPEN MODAL
btn.onclick = function() {
  modal.style.display = "block";
}

// CLOSE MODAL
span.onclick = function() {
  modal.style.display = "none";
}

// CLOSE IF CLICK OUTSIDE MODAL
window.onclick = function(event) {
  if(event.target == modal){
    modal.style.display = "none";
  }
}
</script>

</body>
</html>
