
function readySet() {
    //add listener for closing of popup
    $("#popup").click(
        function () {
            $("#popup").fadeOut(400);
        }
    );
}

$( document).ready(function () {
    readySet();
});
