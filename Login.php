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
        <div class="loginbox" id="lgn">
            <p>Login</p>
            <form action="">
                <div class= "regForm">
                <div class="labels">
                    <section class="spacer"></section>
                    <label for="uName">Username</label>
                    <section class="spacer"></section>
                    <label for="pw1">Password</label>
                </div>
                <div class ="inputs">
                    <input type="text" id = "uName" name="uN">
                    <input type="password" id = "pw" name="inPassword">
                </div>
                </div>
                <button name="Login" type="submit" formmethod="post">Login</button>
              </form>
              <a href="ForgotPassword.php">Forgot your Password?</a><a href="Register.php">Register instead</a>
        </div>
    </div>
</body>
</html>
<?php
    if(isset($_POST['Login'])){
        $server = "localhost";
        $user = "auxzilco_ServerRegister";
        $pass = "7g(c_vRa#i?M";
        $db = "auxzilco_CentralDB";
	    $table = "Users";
        $conn = new mysqli($server, $user, $pass, $db);
        $conn->set_charset('utf8mb4');
	    if($conn->connect_error)
	    {
		    die("Couldnt connect to SQL Server! (big oof)<br>");
        }

        $name = mysqli_real_escape_string($conn, $_POST['uN']);
        $pw = mysqli_real_escape_string($conn, $_POST['inPassword']);
        $sql = "SELECT * FROM Users WHERE Name='$name'";
        $ret = $conn->query($sql);
        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                $userSalt = $i['Salt'];
                $hashedPw = $i['Password'];
                $compPw = hash("sha512", $pw . $userSalt);
                echo "<script>console.log('$userSalt');</script>";
                echo "<script>console.log('$hashedPw');</script>";
                echo "<script>console.log('$compPw');</script>";
                if($hashedPw == $compPw){
                    $_SESSION['UserID'] = $i['UserID'];
                    $_SESSION['Username'] = $i['Name'];
                    echo '<script>window.location="index.php?li=true"</script>';
                }
            }
		}
    }
?>
