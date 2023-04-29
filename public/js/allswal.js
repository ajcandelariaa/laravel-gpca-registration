// PAYMENT REMINDER
window.addEventListener("swal:payment-reminder-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, send it!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('paymentReminderConfirmed')
        }
      });
});

// PAYMENT REMINDER SUCCESS
window.addEventListener("swal:payment-reminder-success", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});


// REGISTRATION AUTHENTICATION ERROR
window.addEventListener("swal:registration-error-authentication", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

// REGISTRATION CONFIRMATION
window.addEventListener("swal:registration-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, I will!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('registrationConfirmed')
        }
      });
});


// PROMO CODE ADD CONFIRMATION
window.addEventListener("swal:add-promo-code-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, add it!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('addPromoCodeConfirmed')
        }
      });
});
// PROMO CODE UPDATE DETAILS
window.addEventListener("swal:add-promo-code", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

// PROMO CODE UPDATE CONFIRMATION
window.addEventListener("swal:update-promo-code-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, update it!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('updatePromoCodeConfirmed')
        }
      });
});
// PROMO CODE UPDATE DETAILS
window.addEventListener("swal:update-promo-code", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});










// DELEGATE IMPORt CONFIRMATION
window.addEventListener("swal:import-delegate-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, import it!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('importDelegateConfirmed')
        }
      });
});

// DELEGATE ADD DETAILS
window.addEventListener("swal:import-delegate", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});







// MEMBER ADD DETAILS
window.addEventListener("swal:add-member", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

// MEMBER UPDATED DETAILS
window.addEventListener("swal:update-member", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

// MEMBER DELETE CONFIRMATION
window.addEventListener("swal:delete-member-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, delete it!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('deleteMemberConfirmed')
        }
      });
});

// MEMBER DELETED
window.addEventListener("swal:delete-member", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

// IMPORT MEMBER CONFIRMATION
window.addEventListener("swal:import-member-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, import it!",
                value: true,
                visible: true,
                closeModal: true,
            },
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                closeModal: true,
            },
        }
      }).then((result) => {
        console.log(result);
        if (result) {
            Livewire.emit('importMemberConfirmed')
        }
      });
});

// MEMBER IMPORTED
window.addEventListener("swal:import-member", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});














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