function show_new_pass() {
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

function show_old_pass() {
    var x = document.getElementById("original_passwd");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}