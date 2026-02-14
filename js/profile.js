document.addEventListener("DOMContentLoaded", function(){

    const btnCloseModal = document.getElementById("closeModalBtn");

    if(btnCloseModal){
        const modals = document.querySelectorAll(".modal");

        btnCloseModal.addEventListener("click", function(){
            modals.forEach(function(modal){
                modal.style.display = "none";
            });
        });
    }

    const btnEditProfile = document.getElementById("editBtn");
    const editProfileModal = document.getElementById("editProfileModal"); 

    if(btnEditProfile){
        btnEditProfile.addEventListener("click",function(){
            editProfileModal.style.display="block"
        });
    }

})

