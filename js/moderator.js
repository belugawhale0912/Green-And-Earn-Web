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
const submissionModal = document.getElementById('submissionPopup');

if(submissionModal){
    function openSubmissionModal(id, username, activity, time, proof) {
        document.getElementById('popupUsername').innerText = username;
        document.getElementById('popupActivity').innerText = activity;
        document.getElementById('popupSubmissionTime').innerText = time;

        document.getElementById('submission_id').value = id;

        const imgElement = document.getElementById('popupProof');
        
        if (proof && proof !== "") {
            imgElement.src = proof;
            imgElement.style.display = "block";
        } else {
            imgElement.style.display = "none";
        }

        submissionModal.style.display = "block";
    }
}

//Modal for partner points
const partnerModal = document.getElementById('partnerPopup');

if(partnerModal){
    function openPartnerModal(transactionID, username, amountSpent, pointsAwarded, recieptImg){
        document.getElementById('partnerUsername').innerText = username;
        document.getElementById('partnerAmount').innerText = amountSpent;
        document.getElementById('partnerPoints').innerText = pointsAwarded;

        const imgElement = document.getElementById('partnerReciept');
        if(recieptImg && recieptImg !==""){
            imgElement.src = recieptImg;
            imgElement.style.display = "block";
        }else{
            imgElement.style.display = "none";
        }
        document.getElementById('partnerTransactionID').value = transactionID;
        partnerModal.style.display="block";
    }
}

// 2. Close Modal Logic (Clicking outside)
window.onclick = function(event) {
    if (event.target == submissionModal) {
        submissionModal.style.display = "none";
    }
    if(event.target == partnerModal){
        partnerModal.style.display = "none";
    }
};