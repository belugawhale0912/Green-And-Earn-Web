document.addEventListener("DOMContentLoaded", function() {

    // ActivityManagement Page
    const addActivityBtn = document.getElementById("addActivityBtn");

    if(addActivityBtn){
        addActivityBtn.addEventListener("click",function(){
            document.getElementById("action").value = "add"; // Important!
            document.getElementById("activityModalTitle").innerText = "Add New Activity";
            document.getElementById("activityModal").style.display = "block";
        });
    }

    const activityFilter = document.getElementById("statusFilter");
    if(activityFilter){
        const activityFilterForm = document.getElementById("filterForm");
        activityFilter.addEventListener("change", function(){
            activityFilterForm.submit();
        });
    }
    
    const editActivityBtn = document.querySelectorAll('.editActivityBtn');

    if (editActivityBtn.length > 0) {
        editActivityBtn.forEach(function(btn) {
            btn.addEventListener('click', function() {
                
                const id = btn.getAttribute('data-id');
                const title = btn.getAttribute('data-title');
                const desc = btn.getAttribute('data-desc');
                const point = btn.getAttribute('data-point');
                const start = btn.getAttribute('data-start');
                const end = btn.getAttribute('data-end');

                // 2. Populate the Modal Form
                document.getElementById("action").value = "update"; // Important!
                document.getElementById("activityID").value = id;
                document.getElementById("activityTitle").value = title;
                document.getElementById("activityDescription").value = desc;
                document.getElementById("activityPoint").value = point;
                document.getElementById("startDate").value = start;
                document.getElementById("endDate").value = end;

                // 3. Update Modal Title
                document.getElementById("activityModalTitle").innerText = "Update Activity";

                // 4. Show Modal
                // Assuming you have a generic openModal function, or just:
                document.getElementById("activityModal").style.display = "block";
            });
        });
    }
    
    // UserManagementPage
    const addUserBtn = document.getElementById("addUserBtn");
    const addUserModal = document.getElementById("userModal");

    if(addUserBtn){
        addUserBtn.addEventListener("click",function(){
            document.getElementById("userForm").reset();
            document.getElementById("action").value = "add";
            addUserModal.style.display = "block"
        });
    }

    // RewardManagementPage
    const rewardFilter = document.getElementById("statusFilter");
    if(rewardFilter){
        const rewardFilterForm = document.getElementById("filterForm");
        rewardFilter.addEventListener("change", function(){
            rewardFilterForm.submit();
        });
    }

    // close modal function
    const cancelBtns = document.querySelectorAll(".cancelBtn");

    cancelBtns.forEach(function(btn) {
        btn.addEventListener("click", function() {
            const modals = document.querySelectorAll(".modal");
            modals.forEach(function(modal){
                modal.style.display = "none";
            })
        });
    });


    // Tab control logic
    const tabs = document.querySelectorAll(".tab");

    if(tabs.length>0){
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
            });
        });
    }
    //date input validation
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    
    if(startDate && endDate){
        const currentDate = new Date().toISOString().split('T')[0];
        startDate.min = currentDate;
    
        startDate.addEventListener('change', function(){
            endDate.min = startDate.value;
        })
    }
});

