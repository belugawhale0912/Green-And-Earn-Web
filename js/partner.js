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

function openUpdateRewardModal(reward_id, title, description, required_points, stock, reward_img){

    document.getElementById("action").value = "update";

    document.getElementById("rewardID").value = reward_id;
    document.getElementById("rewardTitle").value = title;
    document.getElementById("rewardDescription").value = description;
    document.getElementById("requiredPoints").value = required_points;
    document.getElementById("stock").value = stock;
    document.getElementById("rewardImg").value = reward_img;

    document.getElementById("modalTitle").innerText = "Update Record";

    openModal();
}