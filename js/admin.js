function openModal(){
    document.getElementById("activityModal").style.display = "block";
}

function closeModal(){
    document.getElementById("activityModal").style.display = "none";
}


function openAddActivityModal(){
    document.getElementById("activityForm").reset();

    document.getElementById("action").value = "add";

    document.getElementById("modalTitle").innerText = "Add New Activity";

    openModal();
}

function openUpdateActivityModal(activityID, title,description,points_awarded,start_date,end_date){

    document.getElementById("action").value = "update";

    document.getElementById("activityID").value = activityID;
    document.getElementById("activityTitle").value = title;
    document.getElementById("activityDescription").value = description;
    document.getElementById("activityPoint").value = points_awarded;
    document.getElementById("startDate").value = start_date;
    document.getElementById("endDate").value = end_date;

    document.getElementById("modalTitle").innerText = "Update Record";

    openModal();
}