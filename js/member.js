
// Modal controls

document.addEventListener('DOMContentLoaded', function() {
    // 1. Select ALL buttons with the class 'openModalBtn'
    const buttons = document.querySelectorAll('.openModalBtn');

    // 2. Loop through them and add the click event
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            
            // 'this' refers to the button that was clicked
            const id = this.getAttribute("data-id");
            const title = this.getAttribute("data-title");
            const desc = this.getAttribute("data-desc");
            const points = this.getAttribute("data-points");

            // Populate the Modal
            document.getElementById("activityTitle").innerText = title;
            document.getElementById("activityDescription").innerText = desc;
            document.getElementById("activityPoints").innerText = "Points awarded: " + points;
            document.getElementById("activity_id").value = id;

            // Show the Modal
            document.querySelector('.modal').style.display = "block";
        });
    });

});

window.onclick = function(event) {
        const modal = document.querySelector('.modal');
        if (modal && event.target == modal) {
            modal.style.display = "none";
        }
    }