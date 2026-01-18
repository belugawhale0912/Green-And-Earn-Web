function openModal(){
    document.getElementById("editModal").style.display = "block";
}

function closeModal(){
    document.getElementById("editModal").style.display = "none";
}


//RewardList Page
function openAddRewardModal(){
    document.getElementById("rewardForm").reset();

    document.getElementById("action").value = "add";

    document.getElementById("modalTitle").innerText = "Add New Reward";

    openModal();
}

function openUpdateRewardModal(rewardID,title,description,requiredPoints,stock){

    document.getElementById("action").value = "update";

    document.getElementById("rewardID").value = rewardID;
    document.getElementById("rewardTitle").value = title;
    document.getElementById("rewardDescription").value = description;
    document.getElementById("requiredPoints").value = requiredPoints;
    document.getElementById("stock").value = stock;

    document.getElementById("modalTitle").innerText = "Update Record";

    openModal();
}