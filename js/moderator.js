//Filter dropdowm logic
const filterDropdown = document.getElementById("statusFilter");
filterDropdown.addEventListener("change", function(){
    document.getElementById("filterForm").submit();
});

// The modal element
const modal = document.getElementById('submissionPopup');


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

    modal.style.display = "block";
}

// 2. Close Modal Logic (Clicking outside)
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }                                       
}