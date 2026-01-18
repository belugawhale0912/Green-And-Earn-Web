function openModal(){
    document.getElementById("editModal").style.display = "block";
}

function closeModal(){
    document.getElementById("editModal").style.display = "none";
}


//RewardList Page
function openAddRewardModal(){
    document.getElementById("rewardForm").reset();

    document.getElementById("rewardForm").action = "../commands/partner_addReward.php";

    document.getElementById("modalTitle").innerText = "Add New Record";

    openModal();
}

function openUpdateRewardModal(rewardID,title,description,requiredPoiints,stock){

    document.getElementById("rewardForm").action = "../commands/partner_updateReward.php";

    document.getElementById("rewardID").value = rewardID;
    document.getElementById("rewardTitle").value = title;
    document.getElementById("rewardDescription").value = description;
    document.getElementById("requiredPoints").value = requiredPoiints;
    document.getElementById("stock").value = stock;

    document.getElementById("modalTitle").innerText = "Update Record";

    openModal();
}