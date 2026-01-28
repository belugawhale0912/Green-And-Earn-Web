// SWAL custom config
const CustomSwal = Swal.mixin({
    customClass: {
        // Custom name for styling
        popup: 'popup',       
        title: 'title',       
        confirmButton: 'confirm_btn'
    },
});

// function for basic popup
function showMessage(title, text, icon) {
    CustomSwal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'OK'
    });
}