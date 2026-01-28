document.addEventListener("DOMContentLoaded", function() {

    //RewardList Page
    const btnAddReward = document.querySelectorAll(".addRewardButton");
    const rewardModal = document.getElementById("rewardModal");


    if(btnAddReward){
        btnAddReward.forEach(function(btn){
            btn.addEventListener("click", function(){
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

                document.getElementById("rewardID").value = id;
                document.getElementById("rewardTitle").value = title;
                document.getElementById("rewardDescription").value = desc;
                document.getElementById("requiredPoints").value = points;
                document.getElementById("stock").value = stock;

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