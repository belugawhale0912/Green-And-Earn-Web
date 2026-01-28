document.addEventListener("DOMContentLoaded", function() {
    //Filter logic for submissions history page
    const filterDropdown = document.getElementById("statusFilter");
    if(filterDropdown){
        filterDropdown.addEventListener("change", function(){
            document.getElementById("filterForm").submit();
        });
    }

    //FIlter logic for partner points history page
    const filterDropdownPartner = document.getElementById("statusFilterPartner");
    if(filterDropdownPartner){
        filterDropdownPartner.addEventListener("change", function(){
            document.getElementById("filterFormPartner").submit();
        });
    }

    // Modal for submission
    const btnOpenSubmissionModal = document.querySelectorAll(".viewSubmissionBtn");
    const submissionModal = document.getElementById("submissionPopup");

    if(btnOpenSubmissionModal){
        btnOpenSubmissionModal.forEach(function(btn){
            btn.addEventListener("click",function(){
                const submissionID = btn.getAttribute("data-id");
                const submitterUsername = btn.getAttribute("data-username");
                const activity = btn.getAttribute("data-activity");
                const time = btn.getAttribute("data-time");
                const proof = btn.getAttribute("data-proof");
                document.getElementById("submission_id").value = submissionID;
                document.getElementById("popupUsername").innerText = submitterUsername;
                document.getElementById("popupActivity").innerText = activity;
                document.getElementById("popupSubmissionTime").innerText = time;
                document.getElementById("popupProof").src = "../../uploads/proofs/" + proof;
                submissionModal.style.display = "block";
            });
        });
    }

    //Modal for partner points
    const btnViewPartner = document.querySelectorAll('.viewPartnerBtn');
    const partnerModal = document.getElementById('partnerPopup');

    if(btnViewPartner.length>0){
        btnViewPartner.forEach(function(btn){
            btn.addEventListener("click",function(){
                const transactionID = btn.getAttribute('data-id');
                const username = btn.getAttribute('data-username');
                const amount = btn.getAttribute('data-amount');
                const points = btn.getAttribute('data-points');
                const receipt = btn.getAttribute('data-receipt');

                document.getElementById('partnerTransactionID').value = transactionID;
                document.getElementById('partnerUsername').innerText = username;
                document.getElementById('partnerAmount').innerText = amount;
                document.getElementById('partnerPoints').innerText = points;
                document.getElementById('partnerReceipt').src = receipt;
                partnerModal.style.display = "block";
            });
        });
    }
});