var xhr;
if (window.ActiveXObject) {
    xhr = new ActiveXObject("Microsoft.XMLHTTP");
}
else if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
}

document.getElementById("userName").onchange = function () {
    var username = document.getElementById("userName").value;

    if (username == null) {
        return;
    }

    var url = "./signupAJAX.php";
    var params = {
        "username": username
    };

    xhr.open("POST", url, true);

    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Content-length", params.length);
    xhr.setRequestHeader("Connection", "close");

    xhr.onreadystatechange = updatePage;

    xhr.send(params);
};

function updatePage() {
    if ((xhr.readyState == 4) && (xhr.status == 200)) {
        window.alert("This username is already taken.");
    }
}

document.getElementById("submit").addEventListener("click", validation());

function validation(){
    var username = document.getElementById("userName");
    var password = document.getElementById("passWord");
    var repeatPassword = document.getElementById("repeatPassWord");
    var validLogin = true;
    var userRegEx = "/^[a-zA-Z]+[a-zA-Z0-9]*$/";
    var passRegEx = '/((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{6,}))/';
    console.log("function called");
    if (username.value != '' && (username.value.length < 10 || username.value.length > 20)){
        validLogin = false;
    }
    else if(username.value != '' && !userRegEx.test(username.value)){
        validLogin = false;
    }
    if (password.value != '' && (password.value.length < 10 || password.value.length > 20)){
        validLogin = false;
    }
    else if(password.value != '' && !passRegEx.test(password.value)){
        validLogin = false;
    }
    else if(password.value != '' && repeatPassword.value != '' && password.value != repeatPassword.value) {
        validLogin = false;
    }

    if(validLogin==false){
        window.alert("Error. Username or Password invalid.")
    }
}