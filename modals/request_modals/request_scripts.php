<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// =========================================
// AUTO FILL STUDENT INFORMATION
// =========================================
const studentID = document.getElementById("student_id");

if(studentID){

    studentID.addEventListener("input", function(){

        let student_id = this.value;

        if(student_id===""){

            document.getElementById("fullname").value="";
            document.getElementById("semester").value="";
            document.getElementById("school_year").value="";
            return;

        }

        fetch("get_document_student.php?student_id="+encodeURIComponent(student_id))
        .then(res=>res.json())
        .then(data=>{

            if(data.status==="success"){

                document.getElementById("fullname").value=data.fullname;
                document.getElementById("semester").value=data.semester;
                document.getElementById("school_year").value=data.school_year;

            }else{

                document.getElementById("fullname").value="";
                document.getElementById("semester").value="";
                document.getElementById("school_year").value="";

            }

        });

    });

}


// =========================================
// DELIVERY MODE
// =========================================
const deliveryMode=document.getElementById("delivery_mode");
const trackingField=document.getElementById("trackingField");
const trackingNumber=document.getElementById("tracking_number");

if(deliveryMode){

    deliveryMode.addEventListener("change",function(){

        if(this.value==="Shipment"){

            trackingField.style.display="block";
            trackingNumber.required=true;

        }else{

            trackingField.style.display="none";
            trackingNumber.required=false;
            trackingNumber.value="";

        }

    });

}


// =========================================
// STATUS DROPDOWN
// =========================================
function bindStatusEvents(){

    document.querySelectorAll(".status-select").forEach(function(select){

        select.onchange=function(){

            let id=this.dataset.id;
            let status=this.value;

            fetch("update_document_status.php",{

                method:"POST",

                headers:{
                    "Content-Type":"application/x-www-form-urlencoded"
                },

                body:
                    "id="+encodeURIComponent(id)+
                    "&status="+encodeURIComponent(status)

            })

            .then(response=>response.text())

            .then(result=>{

                if(result.trim()==="success"){

<?php if(!$isSuperAdmin){ ?>

                    if(status==="Released"){
                        select.disabled=true;
                    }

<?php } ?>

                    let url=new URL(window.location.href);

                    url.searchParams.set("status","<?= $statusFilter ?>");
                    url.searchParams.set("school_year","<?= $schoolYearFilter ?>");
                    url.searchParams.set("page","<?= $page ?>");
                    url.searchParams.set("modal","1");

                    window.location.href=url.toString();

                }else{

                    Swal.fire(
                        "Error",
                        "Failed to update status.",
                        "error"
                    );

                }

            })

            .catch(function(){

                Swal.fire(
                    "Error",
                    "Connection error.",
                    "error"
                );

            });

        };

    });

}

bindStatusEvents();


// =========================================
// AUTO OPEN TRANSACTION MODAL
// =========================================
<?php if($openModal){ ?>

document.addEventListener("DOMContentLoaded",function(){

    new bootstrap.Modal(
        document.getElementById("transactionModal")
    ).show();

});

<?php } ?>

</script>