document.addEventListener("DOMContentLoaded", function() {

    //RewardList Page
    const btnAddReward = document.querySelectorAll(".addRewardButton");
    const rewardModal = document.getElementById("rewardModal");

    const rewardFilter = document.getElementById("statusFilter");
    if(rewardFilter){
        const rewardFilterForm = document.getElementById("filterForm");
        rewardFilter.addEventListener("change", function(){
            rewardFilterForm.submit();
        });
    }

    if(btnAddReward){
        btnAddReward.forEach(function(btn){
            btn.addEventListener("click", function(){
                document.getElementById('action').value="add";
                document.getElementById("modalTitle").innerText = "Add New Reward";
                document.getElementById("rewardForm").reset();
                rewardModal.style.display="block";
            });
        });
    }

    const btnEditReward = document.querySelectorAll(".editRewardBtn");

    if(btnEditReward){
        btnEditReward.forEach(function(btn){
            btn.addEventListener("click", function(){
                const id = btn.getAttribute("data-id");
                const title = btn.getAttribute("data-title");
                const desc = btn.getAttribute("data-desc");
                const points = btn.getAttribute("data-points");
                const stock = btn.getAttribute("data-stock");
                const img = btn.getAttribute("data-img");

                document.getElementById('action').value="update";
                document.getElementById("rewardID").value = id;
                document.getElementById("rewardTitle").value = title;
                document.getElementById("rewardDescription").value = desc;
                document.getElementById("requiredPoints").value = points;
                document.getElementById("stock").value = stock;
                document.getElementById("img").value = img;

                document.getElementById("modalTitle").innerText = "Update Reward";
                rewardModal.style.display="block";
            });
        });
    }

    const btnCancel = document.getElementById("cancelBtn");
    const modals = document.querySelectorAll(".modal");
    
    if(btnCancel){
        btnCancel.addEventListener("click", function(){
            modals.forEach(function(modal){
                modal.style.display ="none";
            });
        });
    }
})