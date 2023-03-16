var editForm = document.getElementById("edit_form");
var updateButton = document.getElementById("update_btn");

editForm.addEventListener("submit", function () {
    updateButton.disabled = true;
    updateButton.innerHTML = 'Updating...';
    updateButton.classList.remove("cursor-pointer", "hover:bg-registrationPrimaryColorHover");
    updateButton.classList.add("opacity-60", "cursor-progress");
});

