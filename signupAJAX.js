window.onload = function () {
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
};