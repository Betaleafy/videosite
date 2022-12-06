<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxzil Login</title>
    <link rel="stylesheet" href="Design.css">
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
</head>
<body>
<?php include 'header.php';?>
    <div class="cont">
        <div class="loginbox">
            <p>Register</p>
            <form action="">
                <div class= "regForm">
                <div class="labels">
                    <section class="spacer"></section>
                    <label for="uName">Username</label>
                    <section class="spacer"></section>
                    <label for="Email">Email</label>
                    <section class="spacer"></section>
                    <label for="pw1">Password</label>
                    <section class="spacer"></section>
                    <label for="pw2">Password</label>
                </div>
                <div class ="inputs">
                    <input type="text" id = "uName">
                    <input type="text" id = "Email">
                    <input type="password" id = "pw1">
                    <input type="password" id = "pw2">
                </div>
                </div>
                <button id="rgb">Register</button>
              </form>
              <a href="Login.php">Login instead</a>
              <div class="error" id="err">
                <p id="errmsg">---</p>
              </div>
        </div>
    </div>
    <script>
        document.getElementById("rgb").addEventListener("click", function(event){event.preventDefault();ButtonClick()});
        function ButtonClick () {
            let pw1 = document.getElementById("pw1").value;
            let pw2 = document.getElementById("pw2").value;
            let email = document.getElementById("Email").value.toLowerCase();
            let name = document.getElementById("uName").value;
            if(pw1 != pw2){
                document.getElementById('err').style.display = "block";
                document.getElementById('errmsg').innerText = "Your Passwords do not match!";
                return;
            }
            if(!email.includes("@") || !email.includes(".")){
                document.getElementById('err').style.display = "block";
                document.getElementById('errmsg').innerText = "Your Email Address cant be valid!";
                return;
            }
            if(pw1 == "" || pw2 == "" || email == "" || name == ""){
                document.getElementById('err').style.display = "block";
                document.getElementById('errmsg').innerText = "Something is missing.";
                return;
            }
            var xhr = new XMLHttpRequest();
            xhr.open('GET', "DBRegister.php?check=true&cName=Name&cValue="+ name +"&db=Users", true);
            xhr.send();
            xhr.onreadystatechange = processRequest;
            let mode = 1;
            function processRequest(e) {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(mode + ", " + xhr.responseText);
                    if (xhr.responseText == "found"){
                        document.getElementById('err').style.display = "block";
                        if(mode == 1){
                            document.getElementById('errmsg').innerText = "Username already taken!";
                        }
                        else{
                            document.getElementById('errmsg').innerText = "Email Address already Registered!";
                        }
                        return;
                    }
                    if(mode == 1 && xhr.responseText == "data not found"){
                        mode++;
                        xhr.open('GET', "DBRegister.php?check=true&cName=Email&cValue="+ email +"&db=Users", true);
                        xhr.send();
                        xhr.onreadystatechange = processRequest;
                    }
                    if(mode == 2 && xhr.responseText == "data not found"){
                        console.log("Register Authenticated");
                        console.log("Finishing request...");
                        post('', {Create: 'True', Name: name, EMail: email, PW: pw1});
                    }
                }
            }
            document.getElementById('err').style.display = "none";
        }
        function post(path, params, method='post') {
            const form = document.createElement('form');
            form.method = method;
            form.action = path;
            for (const key in params) {
                if (params.hasOwnProperty(key)) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = key;
                    hiddenField.value = params[key];
                    form.appendChild(hiddenField);
                }
            }
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
<?php
    if(isset($_POST['Create'])){
        $server = "localhost";
        $user = "auxzilco_ServerRegister";
        $pass = "7g(c_vRa#i?M";
        $db = "auxzilco_CentralDB";
	    $table = "Videos";
        $conn = new mysqli($server, $user, $pass, $db);
        $conn->set_charset('utf8mb4');
	    if($conn->connect_error)
	    {
		    die("Couldnt connect to SQL Server! (big oof)<br>");
	    }
        echo("<script>console.log('connected to db');</script>");
        $uid = generateRandomString(25);
        $name = mysqli_real_escape_string($conn, $_POST['Name']);
        $mail = mysqli_real_escape_string($conn, $_POST['EMail']);
        $coins = 0;
        $pwIn = mysqli_real_escape_string($conn, $_POST['PW']);
        $salt = generateRandomString(1024);
        $pfp = "/Icons/stpfp.jpg";
        $banner = "/Icons/nobanner.jpg";
        $about = "";
        $mailv = 0;
        $verified = 0;
        $follows = "a:0:{}";
        $pw = hash("sha512", $pwIn . $salt);
        $sql = "INSERT INTO Users (UserID, Name, EMail, Coins, Password, Salt, ProfilePic, Banner, About, EmailVerified, Verified, Follows) VALUES ('$uid','$name','$mail','$coins','$pw','$salt','$pfp','$banner','$about','$mailv','$verified','$follows')";
        $ret = $conn->query($sql);
        echo '<script>window.location="index.php?reg=true"</script>';
    }
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
?>
