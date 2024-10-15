/**
 * Check the two password, if they are equals, the input's border become green
 *
 * @param name
 */
function checkPwd(name) {
    let pwd = document.getElementById("pwd" + name).value;
    let confpwd = document.getElementById("pwdConf" + name).value;
    if (pwd !== confpwd) {
        document.getElementById("pwd" + name).style.borderColor = "red";
        document.getElementById("pwdConf" + name).style.borderColor = "red";
        document.getElementById("valid" + name).disabled = true;
    } else {
        document.getElementById("pwd" + name).style.borderColor = "green";
        document.getElementById("pwdConf" + name).style.borderColor = "green";
        document.getElementById("valid" + name).disabled = false;
    }
}