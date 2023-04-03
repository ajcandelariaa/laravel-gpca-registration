
// DELEGATE UPDATE DETAILS
window.addEventListener("swal:delegate-update", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

// DELEGATE COMPANY UPDATE DETAILS
window.addEventListener("swal:company-update", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});