// ADD & EDIT EVENT LOGO & BANNER
function previewLogo(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('imgLogo');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
function previewBanner(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('imgBanner');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}


// ADD & EDIT BADGE BANNERS
function previewBadgeFrontBanner(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('badgeFrontBanner');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
function previewBadgeBackBanner(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('badgeBackBanner');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

