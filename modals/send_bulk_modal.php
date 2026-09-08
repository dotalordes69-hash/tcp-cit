<?php

$signatures = [];

$sigQuery = mysqli_query($conn,"
    SELECT id, signature_name, signature_content
    FROM email_signatures
    ORDER BY signature_name ASC
");

while($sig = mysqli_fetch_assoc($sigQuery)){
    $signatures[] = $sig;
}

?>

<link rel="stylesheet" href="assets/css/send_bulk.css">


<div class="modal fade"
     id="bulkEmailModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">


<div class="modal-dialog modal-xl modal-dialog-centered">


<div class="modal-content bulk-modal">


<form action="send_bulk_email.php" method="POST">



<!-- HEADER -->

<div class="modal-header bulk-header text-white">


<div>

<h5 class="modal-title">
📧 Bulk Email Distribution
</h5>


<small>
Send official announcements to selected students.
</small>


</div>


<button type="button"
        class="btn-close btn-close-white"
        data-bs-dismiss="modal">
</button>


</div>





<!-- BODY -->

<div class="modal-body">


<div class="row g-4">





<!-- LEFT PANEL -->

<div class="col-lg-7">



<div class="card bulk-card mb-3">


<div class="card-header">
Student Selection
</div>



<div class="card-body">


<label class="form-label">
School Year
</label>


<select id="schoolYearSelect"
        class="form-select">


<option value="">
-- All School Year --
</option>


<?php

$years=mysqli_query($conn,"
SELECT DISTINCT school_year
FROM students
WHERE school_year!=''
ORDER BY school_year DESC
");


while($yr=mysqli_fetch_assoc($years)):

?>


<option value="<?=htmlspecialchars($yr['school_year'])?>">

<?=htmlspecialchars($yr['school_year'])?>

</option>


<?php endwhile; ?>


</select>





<div class="form-check mt-3">


<input class="form-check-input"
       type="checkbox"
       id="balanceOnly"
       value="1">


<label class="form-check-label">

Students with Balance Only

</label>


</div>





<div class="row mt-3 g-2">


<div class="col-6">


<button type="button"
        id="filterBtn"
        class="btn btn-danger w-100">

<i class="bi bi-funnel"></i>
Filter

</button>


</div>



<div class="col-6">


<button type="button"
        id="resetFilterBtn"
        class="btn btn-outline-danger w-100">

Reset

</button>


</div>


</div>



</div>


</div>






<div class="card bulk-card">


<div class="card-header">

Recipient Preview

</div>



<div id="recipientList"
     class="recipient-box">


<div class="text-center text-muted">

Select filter to load students.

</div>


</div>


</div>



</div>







<!-- RIGHT PANEL -->

<div class="col-lg-5">



<div class="card bulk-card mb-3">


<div class="card-header">

Email Content

</div>



<div class="card-body">



<label class="form-label">
Subject
</label>


<input type="text"
       name="subject"
       class="form-control mb-3"
       placeholder="Email subject"
       required>





<label class="form-label">

Message

</label>


<textarea name="message"
          rows="8"
          class="form-control"
          placeholder="Type message..."
          required></textarea>



</div>


</div>








<div class="card bulk-card">


<div class="card-header">

✍️ Email Signature

</div>



<div class="card-body">



<label class="form-label">

Select Signature

</label>



<select id="bulkSignatureSelect"
        name="signature_id"
        class="form-select mb-3">


<option value="">
-- Select Signature --
</option>


<?php foreach($signatures as $sig): ?>


<option value="<?= $sig['id']; ?>"
data-content="<?=htmlspecialchars($sig['signature_content']);?>">


<?=htmlspecialchars($sig['signature_name']);?>


</option>


<?php endforeach; ?>


</select>





<label class="form-label">

Preview

</label>


<textarea id="bulkSignaturePreview"
          name="signature_content"
          class="form-control mb-3"
          rows="5"
          readonly></textarea>






<button type="button"
        class="btn btn-outline-danger w-100"
        data-bs-toggle="collapse"
        data-bs-target="#newBulkSignature">

➕ Create New Signature

</button>





<div class="collapse mt-3"
     id="newBulkSignature">


<div class="signature-create">


<label class="form-label">
Signature Name
</label>


<input type="text"
       id="bulk_signature_name"
       class="form-control mb-3"
       placeholder="Registrar Official">





<label class="form-label">
Signature Content
</label>


<textarea id="bulk_signature_content"
          class="form-control mb-3"
          rows="4"></textarea>




<button type="button"
        id="saveBulkSignatureBtn"
        class="btn btn-danger w-100">

Save Signature

</button>



<div id="bulkSignatureMsg"
     class="mt-2">

</div>


</div>


</div>



</div>


</div>




</div>


</div>



</div>






<!-- FOOTER -->


<div class="modal-footer">


<button type="button"
        class="btn btn-secondary"
        data-bs-dismiss="modal">

Cancel

</button>



<button type="submit"
        class="btn btn-danger px-4">

📧 Send Bulk Email

</button>


</div>




</form>


</div>

</div>

</div>







<script>


// FILTER

document.getElementById('filterBtn')
.onclick=function(){


let year=document.getElementById('schoolYearSelect').value;

let balance=document.getElementById('balanceOnly').checked ? 1:0;


let box=document.getElementById('recipientList');


box.innerHTML=`

<div class="text-center py-5">

<div class="spinner-border text-danger"></div>

<br>
Loading...

</div>`;


fetch(
'get_bulk_recipients.php?school_year='+
encodeURIComponent(year)+
'&balance_only='+balance
)


.then(res=>res.text())

.then(data=>{

box.innerHTML=data;

});


};





// RESET

document.getElementById('resetFilterBtn')
.onclick=function(){


document.getElementById('schoolYearSelect').value='';

document.getElementById('balanceOnly').checked=false;


document.getElementById('recipientList').innerHTML=`

<div class="text-center text-muted">

Select filter to load students.

</div>`;


};






// SIGNATURE PREVIEW


document.getElementById('bulkSignatureSelect')
.onchange=function(){


let option=this.options[this.selectedIndex];


document.getElementById('bulkSignaturePreview').value =
option.dataset.content || '';


};





// SAVE SIGNATURE


document.getElementById('saveBulkSignatureBtn')
.onclick=function(){


let name=document.getElementById('bulk_signature_name').value;

let content=document.getElementById('bulk_signature_content').value;



if(name=='' || content==''){

document.getElementById('bulkSignatureMsg').innerHTML=
`
<div class="alert alert-warning">

Complete fields.

</div>
`;

return;

}



fetch('save_signature.php',{

method:'POST',

headers:{
'Content-Type':'application/x-www-form-urlencoded'
},


body:
'signature_name='+encodeURIComponent(name)+
'&signature_content='+encodeURIComponent(content)


})


.then(res=>res.text())


.then(()=>{


document.getElementById('bulkSignatureMsg').innerHTML=

`
<div class="alert alert-success">

Saved successfully.

</div>
`;


setTimeout(()=>{

location.reload();

},1000);



});


};



</script>