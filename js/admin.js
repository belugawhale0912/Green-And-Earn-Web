function openModal(){
    document.getElementById("activityModal").style.display = "block";
}

function closeModal(){
    document.getElementById("activityModal").style.display = "none";
}


function openAddActivityModal(){
    document.getElementById("activityForm").reset();

    document.getElementById("activityForm").action = "../commands/admin_addActivity.php";

    document.getElementById("modalTitle").innerText = "Add New Record";

    openModal();
}

function openUpdateActivityModal(title,description,points_awarded,start_date,end_date){
    document.getElementById("activityForm").action = "../commands/admin_updateActivity.php";

    document.getElementById("activityTitle").value = title;
    document.getElementById("activityDescription").value = description;
    document.getElementById("activityPoint").value = points_awarded;
    document.getElementById("startDate").value = start_date;
    document.getElementById("endDate").value = end_date;

    document.getElementById("modalTitle").innerText = "Update Record";

    openModal();
}