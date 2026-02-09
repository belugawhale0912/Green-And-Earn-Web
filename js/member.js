document.addEventListener("DOMContentLoaded", function() {
    
    const btnCloseModal = document.getElementById("closeModalBtn");

    if(btnCloseModal){
        const modals = document.querySelectorAll(".modal");

        btnCloseModal.addEventListener("click", function(){
            modals.forEach(function(modal){
                modal.style.display = "none";
            });
        });
    }


    //Task List Page Functions
    // Submission Modal
    const btnOpenSubmission = document.querySelectorAll('.openSubmissionModalBtn');

    if(btnOpenSubmission.length>0){
        // for each buttons we apply function(each of the button)
        btnOpenSubmission.forEach(function(button) {
            // for each button from buttons, we run the below code
            // add click listener to button, if a click is detected run the function
            button.addEventListener('click', function() { 
                // these will run when button is clicked
                const id = this.getAttribute("data-id");
                const title = this.getAttribute("data-title");
                const desc = this.getAttribute("data-desc");
                const points = this.getAttribute("data-points");

                // set modal with recorded values
                document.getElementById("activityTitle").innerText = title;
                document.getElementById("activityDescription").innerText = desc;
                document.getElementById("activityPoints").innerText = "Points awarded: " + points;
                document.getElementById("activity_id").value = id;

                // Show the Modal
                document.getElementById('submitTask').style.display = "block";
            });
        });
    }

    const btnViewSubmission = document.querySelectorAll('.viewSubmissionBtn');

    if(btnViewSubmission.length>0){
        btnViewSubmission.forEach(function(button){
            button.addEventListener('click', function(){
                const proofImg = button.getAttribute("data-img");

                document.getElementById("proofImg").src = proofImg;
                document.getElementById("viewProof").style.display = "block";
            })
        })
    }


    // Submission Filter
    const submissionFilter = document.getElementById("submissionFilter");
    const submissionCard = document.querySelectorAll(".taskCard");

    if(submissionFilter){
        submissionFilter.addEventListener("change",function(){
            const filter = submissionFilter.value;

            submissionCard.forEach(function(card){
                const submissionType = card.getAttribute("data-status");

                if(filter == "All"){ 
                    card.style.display = "";
                }

                else if(submissionType == filter){
                    card.style.display = "";
                }
                else{
                    card.style.display = "none";
                }
            });
        });
    }





    // Reward Page Functions
    const confirmForm = document.querySelectorAll('.redemptionForm');

    if(confirmForm.length > 0){
        confirmForm.forEach(function(form){
            form.addEventListener('submit', function(event) {
                
                // Stop the form from sending 
                event.preventDefault(); 
            
                CustomSwal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to redeem this reward?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, redeem it!'
                }).then((result) => {
                    // submit the form if the user click comfirm
                    if (result.isConfirmed) {
                        form.submit(); 
                    }
                });
            });
        });
    }

    // Transaction Page functions
    const transactionFilter = document.getElementById("transactionFilter");
    const transactionCard = document.querySelectorAll(".transactionCard");

    if(transactionFilter){
        transactionFilter.addEventListener("change",function(){
            const filter = transactionFilter.value;
            transactionCard.forEach(function(card){
                const transactionType = card.getAttribute("data-transactionType");
                if(filter=="All"){
                    card.style.display="";
                }
                else if(filter==transactionType){
                    card.style.display="";
                }
                else{
                    card.style.display="none";
                }
            });
        });
    }

    //  Friend Page functions
    const btnViewFriendProfile = document.querySelectorAll(".viewFriendProfileBtn");

    if(btnViewFriendProfile.length>0){
        btnViewFriendProfile.forEach(function(button){
            button.addEventListener("click",function(){
                const friendName = button.getAttribute("data-friendName");
                const friendEmail = button.getAttribute("data-friendEmail");
                const friendBio = button.getAttribute("data-friendBio");
                const friendImg = button.getAttribute("data-img");
                const friendSince = button.getAttribute("data-friendSince");
                const totalPoints = button.getAttribute("data-totalPoints");

                document.getElementById("friendImg").src = friendImg;
                document.getElementById("friendName").innerHTML=friendName;
                document.getElementById("profileTitle").innerHTML=friendName;
                document.getElementById("friendEmail").innerHTML=friendEmail;
                document.getElementById("bio").innerHTML=friendBio;
                document.getElementById("totalPoint").innerHTML=totalPoints;
                document.getElementById("friendSince").innerHTML=friendSince;

                document.getElementById("friendProfile").style.display="block";
            });
        });
    }

    const deleteFriendForm = document.querySelectorAll('.deleteFriendForm');

    if(deleteFriendForm){
        // Apply function for each from from comfirmForm
        deleteFriendForm.forEach(function(form){
            form.addEventListener('submit', function(event) {
                
                // Stop the form from sending data to PHP immediately
                event.preventDefault(); 
                // comfirmation dialog
                CustomSwal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to delete this friend?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); 
                    }
                });
            });
            
        });
    }

    const friendSearchBar = document.getElementById("friendSearch");
    const friendCard = document.querySelectorAll(".friendCard");

    if(friendSearchBar){
        friendSearchBar.addEventListener("input",function(event){
            const searchText = event.target.value.toLowerCase().trim();

            friendCard.forEach(function(card){
                const username = card.getAttribute("data-username");

                if(username.includes(searchText)){
                    card.style.display="";
                }
                else{
                    card.style.display="none";
                }
            });
        });
    }
    
    // Profile Page
    const btnEditProfile = document.getElementById("editProfileBtn");
    const editProfileModal = document.getElementById("editProfileModal"); 

    if(btnEditProfile){
        btnEditProfile.addEventListener("click",function(){
            editProfileModal.style.display="block"
        });
    }

});


