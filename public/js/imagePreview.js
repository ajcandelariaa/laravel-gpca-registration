// ADD EVENT
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