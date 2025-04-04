// ADD DELEGATE TO GRIP
window.addEventListener("swal:add-to-grip", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:add-to-grip-confirmation", (event) => {
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
            Livewire.emit(event.detail.livewireEmit)
        }
    });
});

//DIGITAL HELPER LOADING
window.addEventListener("remove-dh-loading-screen", () => {
    let dhLoadingScreen = document.getElementById('dh-loading-screen');
    dhLoadingScreen.classList.add('hidden');
    console.log("removed dh loading screen");
});

window.addEventListener("add-dh-loading-screen", (event) => {
    let dhLoadingScreen = document.getElementById('dh-loading-screen');
    let dhLoadingScreenText = document.getElementById('loading-text');
    dhLoadingScreenText.innerText = event.detail.text;
    dhLoadingScreen.classList.remove('hidden');
    console.log("add dh loading screen");
});


// BROADCAST EMAIL NOTIFICATION
window.addEventListener("swal:broadcast-email-success", (event) => {
    let registrationLloadingScreen = document.getElementById('registration-loading-screen');
    registrationLloadingScreen.classList.add('hidden');
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:broadcast-email-confirmation", (event) => {
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
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit('broadcastEmailConfirmed')
        }
      });
});



//PRINT BADGE
window.addEventListener("swal:print-badge-confirmed", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
    
    var win = window.open(event.detail.url, '_blank');
    win.focus();
});

window.addEventListener("swal:print-badge-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, print it!",
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
            Livewire.emit('printBadgeConfirmed')
        }
      });
});


//MARK AS PAID 
window.addEventListener("swal:mark-as-paid-success", (event) => {
    let registrationLloadingScreen = document.getElementById('registration-loading-screen');
    registrationLloadingScreen.classList.add('hidden');
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:mark-as-paid-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, mark this as paid!",
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
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit('markAsPaidConfirmed')
        }
      });
});

// CANCEL REFUND DELEGATE
window.addEventListener("swal:delegate-cancel-refund-success", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:delegate-cancel-refund-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, I'm sure!",
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
            Livewire.emit('cancelRefundDelegateConfirmed')
        }
      });
});

// CANCEL REPLACEMENT DELEGATE
window.addEventListener("swal:delegate-cancel-replace-success", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:delegate-cancel-replace-confirmation", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
        buttons: {
            confirm: {
                text: "Yes, cancel and replace it!",
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
            Livewire.emit('cancelReplaceDelegateConfirmed')
        }
      });
});



// REGISTRATION TYPE
window.addEventListener("swal:update-registration-type", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:update-registration-type-confirmation", (event) => {
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
            Livewire.emit('updateRegistrationTypeConfirmed')
        }
      });
});

window.addEventListener("swal:add-registration-type", (event) => {
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

window.addEventListener("swal:add-registration-type-confirmation", (event) => {
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
            Livewire.emit('addRegistrationTypeConfirmed')
        }
      });
});


// SEND EMAIL REGISTRATION CONFIRMATION SINGLE
window.addEventListener("swal:send-email-registration-confirmation-confirmation-single", (event) => {
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
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit('sendEmailRegistrationConfirmationSingleConfirmed')
        }
      });
});

// SEND EMAIL REGISTRATION CONFIRMATION
window.addEventListener("swal:send-email-registration-confirmation-confirmation", (event) => {
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
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit('sendEmailRegistrationConfirmationConfirmed')
        }
      });
});

// SEND EMAIL REGISTRATION CONFIRMATION SUCCESS
window.addEventListener("swal:send-email-registration-success", (event) => {
    let registrationLloadingScreen = document.getElementById('registration-loading-screen');
    registrationLloadingScreen.classList.add('hidden');
    swal({
        title: event.detail.message,
        text: event.detail.text,
        icon: event.detail.type,
    });
});

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
                text: "Yes, I'm sure!",
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
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit('registrationConfirmed')
        }
      });
});

window.addEventListener("swal:remove-loading-button", () => {
      let payButton = document.getElementById('payButton');
      let processingButton = document.getElementById('processingButton');
      let registrationLloadingScreen = document.getElementById('registration-loading-screen');
      let registrationLloadingScreen2 = document.getElementById('registration-loading-screen-2');
      
      processingButton.classList.add('hidden');
      payButton.classList.remove('hidden');
      registrationLloadingScreen.classList.add('hidden');
      registrationLloadingScreen2.classList.add('hidden');
});

window.addEventListener("swal:hide-pay-button", () => {
      let payButton = document.getElementById('payButton');
      payButton.classList.add('hidden');
});

window.addEventListener("swal:remove-registration-loading-screen", () => {
      let registrationLloadingScreen = document.getElementById('registration-loading-screen');
      registrationLloadingScreen.classList.add('hidden');
});

window.addEventListener("swal:add-registration-loading-screen", () => {
    let registrationLloadingScreen = document.getElementById('registration-loading-screen');
    registrationLloadingScreen.classList.remove('hidden');
    Livewire.emit('emitSubmit')
});

window.addEventListener("swal:add-step3-registration-loading-screen", () => {
    let registrationLloadingScreen = document.getElementById('registration-loading-screen');
    registrationLloadingScreen.classList.remove('hidden');
    Livewire.emit('emitSubmitStep3')
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
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit('importDelegateConfirmed')
        }
      });
});

// DELEGATE ADD DETAILS
window.addEventListener("swal:import-delegate", (event) => {
    let registrationLloadingScreen = document.getElementById('registration-loading-screen');
    registrationLloadingScreen.classList.add('hidden');
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
window.addEventListener("swal:delete-all-members-confirmation", (event) => {
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
            Livewire.emit('deleteAllMembersConfirmed')
        }
      });
});

// MEMBER DELETED
window.addEventListener("swal:delete-all-members", (event) => {
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