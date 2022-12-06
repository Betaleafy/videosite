<?php session_start();?>
<?php
    $pageName = "Reset Password";
    $pageInfo = "Just enter your Email Address, and if that Account Exists, you will get a Mail from us!";

    $styleInput = "<style>.pw{display:none;}</style>";
    $tokenInpt = "";
    $buttonFunc = "reset";

    $buttonText = "Send Mail";

    if(isset($_GET['t'])){
        $pageName = "Set new Password";
        $pageInfo = "Please input your new Password!";
        $styleInput = "<style>.uMail{display:none;}</style>";
        $buttonText = "Reset Password";
        $tokenInpt = $_GET['t'];
        $buttonFunc = "usetoken";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageName;?></title>
    <link rel="stylesheet" href="Design.css">
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
</head>
<body>
    <?php include 'header.php';?>
    <?php echo $styleInput;?>
    <div class="cont">
        <div class="loginbox">
            <p><?php echo $pageName;?></p>
            <h3><?php echo $pageInfo;?></h3>
            <form action="">
                <div class= "regForm">
                <div class="labels">
                    <section class="spacer"></section>
                    <label for="uName" class="uMail">Email</label>
                    <label for="uName" class="pw">Password</label>
                    <section class="spacer"></section>
                    <label for="uName" class="pw">Password</label>
                </div>
                <div class ="inputs">
                    <input type="hidden" name="token" value="<?php echo $tokenInpt;?>">
                    <input type="text" class = "uMail" name="Mail">
                    <input type="password" class = "pw" name="pw1">
                    <input type="password" class = "pw" name="pw2">
                </div>
                </div>
                <button name="<?php echo $buttonFunc;?>" type="submit" formmethod="post"><?php echo $buttonText;?></button>
              </form>
        </div>
    </div>
</body>
</html>
<?php
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

    if(isset($_POST['reset'])){     
        //Get mail
        $mail = mysqli_real_escape_string($conn, $_POST['Mail']);
        //Search database for mail
        $sql = "SELECT * FROM Users WHERE EMail='$mail'";
        $ret = $conn->query($sql);

        $token = generateRandomString(64);

        //if found do stuff
        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                //i variable holds all user info use $i['<value name>']; to get it
                //Email code here  //No u //Yes I
                $sql = "SELECT * FROM Token WHERE Email='$mail'";
                $ret = $conn->query($sql);

                if($ret->num_rows > 0)
                {
                  $sql = "UPDATE Token SET Token='$token' WHERE Email='$mail'";
                  $ret = $conn->query($sql);
                }
                else
                {
                  $sql = "INSERT INTO Token (Email, Token) VALUES ('$mail', '$token')";
                  $ret = $conn->query($sql);
                }

                $subject = "Password Reset";
                $message = "<h2>You requested a Password Reset!</h2>\r\n<p>A request to reset your Password was send!</p>\r\n<p>If that wasnt you please contact support!</p>\r\n<a href='https://auxzil.com/ForgotPassword.php?t=$token'>Click here to reset your Password!</a>";
                $headers  = "From: Auxzil Service <service@auxzil.com>\r\n";
                $headers .= "Reply-To: service@auxzil.com\r\n";
                $headers .= "Content-type: text/html\r\n";
                mail($mail, $subject, $message, $headers);
                
                //Redirect user back to homepage
                echo "<script>window.location = 'index.php?rp=1'</script>";
                echo"rdr";
            }
        }
    }

    if(isset($_POST['usetoken'])){
        if(isset($_POST['pw1']) && isset($_POST['pw2'])){
            if($_POST['pw1'] == $_POST['pw2']){
                echo "<script>console.log('1')</script>";
                $tkn = $_POST['token'];
                $pw = $_POST['pw1'];
                $sql = "SELECT * FROM Token WHERE Token = '$tkn'";
                $ret = $conn->query($sql);
                echo "<script>console.log('2')</script>";

                if($ret->num_rows > 0){
                    echo "<script>console.log('3')</script>";
                    $info = $ret->fetch_assoc();
                    $mail = $info['Email'];
                    $sql = "SELECT * FROM Users WHERE EMail = '$mail'";
                    $ret = $conn->query($sql);
                    echo "<script>console.log('4')</script>";

                    if($ret->num_rows > 0){
                        echo "<script>console.log('5')</script>";
                        $info = $ret->fetch_assoc();
                        $UserSalt = $info['Salt'];

                        $hash = hash("sha512", $pw . $UserSalt);

                        echo "<script>console.log('6')</script>";
                        $sql = "UPDATE Users SET Password = '$hash' WHERE EMail = '$mail'";
                        $ret = $conn->query($sql);

                        echo "<script>console.log('7')</script>";
                        $sql = "DELETE FROM Token WHERE Email = '$mail'";
                        $ret = $conn->query($sql);
                        echo "<script>window.location = 'Login.php'</script>";
                        echo "rdr";
                    }
                }
            }
            else{
                echo "Your Passwords do not match!";
            }
        }
        else{
            echo "You have to set 2 Passwords!";
        }
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
