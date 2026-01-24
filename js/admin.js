function openModal(modalID){
    document.getElementById(modalID).style.display = "block";
}

function closeModal(modalID){
    document.getElementById(modalID).style.display = "none";
}

function openAddActivityModal(){
    document.getElementById("activityForm").reset();

    document.getElementById("action").value = "add";

    document.getElementById("activityModalTitle").innerText = "Add New Activity";

    openModal("activityModal");
}

function openUpdateActivityModal(activityID, title,description,points_awarded,start_date,end_date){

    document.getElementById("action").value = "update";

    document.getElementById("activityID").value = activityID;
    document.getElementById("activityTitle").value = title;
    document.getElementById("activityDescription").value = description;
    document.getElementById("activityPoint").value = points_awarded;
    document.getElementById("startDate").value = start_date;
    document.getElementById("endDate").value = end_date;

    document.getElementById("activityModalTitle").innerText = "Update Record";

    openModal("activityModal");
}

function openAddUserModal(){
    document.getElementById("userForm").reset();

    document.getElementById("action").value = "add";

    document.getElementById("userModalTitle").innerText = "Add New User";

    openModal("userModal");
}

function showTable(tableID, clickedTab){
    document.querySelectorAll('.table-section').forEach(function(section) {
        section.classList.remove('active');
    });

    document.querySelectorAll('.tab').forEach(function(tab) {
        tab.classList.remove('active');
    })

    document.getElementById(tableID).classList.add('active');

    clickedTab.classList.add('active');
}

document.addEventListener("DOMContentLoaded", function(){
    const addUserBtn = document.getElementById("addUserBtn");
    const addActivityBtn = document.getElementById("addActivityBtn");
    const cancelBtns = document.querySelectorAll(".cancelBtn");
    const tabs = document.querySelectorAll(".tab");

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            const tabID = tab.dataset.target;
            
            //hide all tables
            document.querySelectorAll(".table-section").forEach(function(section) {
                section.classList.remove("active");
            });

            //show clicked table
            document.getElementById(tabID).classList.add("active");

            //remove "active" from all tabs
            tabs.forEach(function(t){t.classList.remove("active")});

            //click tab
            tab.classList.add("active");
        })
    })

    if(addUserBtn){
        addUserBtn.addEventListener("click", function(){
            openModal("userModal");
        });
    }
    if(addActivityBtn){
        addActivityBtn.addEventListener("click", function(){
            openModal("activityModal");
        });
    }
    cancelBtns.forEach(function(btn) {
        btn.addEventListener("click", function() {
            closeModal(btn.dataset.modal);
        });
    });
});