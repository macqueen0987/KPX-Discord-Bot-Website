

function show_pass() {
    var x = document.getElementById("inputPassword");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
    var x = document.getElementById("inputPassword_check");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}