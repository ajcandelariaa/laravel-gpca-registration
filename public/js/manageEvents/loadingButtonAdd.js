var addForm = document.getElementById("add_form");
var addButton = document.getElementById("add_btn");

addForm.addEventListener("submit", function () {
    addButton.disabled = true;
    addButton.innerHTML = 'Publishing...';
    addButton.classList.remove("cursor-pointer", "hover:bg-registrationPrimaryColorHover");
    addButton.classList.add("opacity-60", "cursor-progress");
});

