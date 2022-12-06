<?php
    //standard text
    $UserButton = "<input type='submit' name='loginPromt' value='Login' class='noskin palink'>";
    $imageStyle = "display:none;";
    $notbox = "";
    $notIndic = "";
    $promt = "";

    if(isset($_SESSION['UserID'])){
        $name = htmlspecialchars($_SESSION['Username'], ENT_QUOTES, 'UTF-8');
        $id = htmlspecialchars($_SESSION['UserID'], ENT_QUOTES, 'UTF-8');
        $UserButton = "<a href='User.php?u=$id'>Account</a>";

        $ret = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$id) , "s", $conn);

        $i = $ret->fetch_assoc();
        $path = htmlspecialchars($i['ProfilePic'], ENT_QUOTES, 'UTF-8');
        $imageStyle = "background-image: url($path);";

        $ret = SendSQL("SELECT * FROM Notifications WHERE UserID = ?", array(&$id), "s", $conn);

        if(isset($_POST['delete'])){
            SendSQL("DELETE FROM Notifications WHERE UserID = ?", array(), "", $conn);
        }
        if(isset($_POST['noti'])){
            //assemble data
            $ret = SendSQL("SELECT * FROM Notifications WHERE UserID = ?", array(&$id), "s", $conn);
            $notis = "";

            while($i = $ret->fetch_assoc()){
                $header = $i['Headline'];
                $msg = $i['Text'];
                $date = $i['Date'];
                $link = $i['Link'];
                $notis .= "<a href='$link'><div class='notiElement shadow'><section class='top'><h3>$header</h3><h4>$date</h4></section><p>$msg</p></div></a>";
            }
            //assemble panel
            $notbox = "<section class='notibox shadow'><div class='options'><form action='' method='post'><h2>Notifications</h2><button class='noskin' name='delete' value='yes'><i class='fas fa-ban'></i></button></form></div>$notis</section>";
        }

        if($ret->num_rows > 0){
            $notIndic = "<section>$ret->num_rows</section>";
        }
        if(isset($_POST['uploadPromt'])){
            $promt = "<section class='blur promtbg'><div class='promt shadow upload'><form action='' enctype='multipart/form-data' method='post' id='uplform'><h3>Upload</h3><div class='gridlayout'><div class='innerpromt'><div class='smolspace'></div><div class='inputgroup'><p>Title</p><input type='text' name='title' id='title'></div><div class='inputgroup'><p>Description</p><textarea name='desc' rows='14'></textarea></div></div><div class='innerpromt'><div class='smolspace'></div><div class='inputgroup'><p>Tags</p><input type='text' name='tags'></div><div class='inputgroup'><p>Visibility</p><select name='vis'><option value='1'>Visible</option><option value='0'>Invisible</option></select></div><div class='inputgroup'><p>Password</p><select name='pas'><option value='0'>No Password (public)</option><option value='1'>Password</option></select></div><div class='inputgroup'><p>Password</p><input type='text' name='password'></div><div class='inputgroup'><p>Video</p><input type='file' name='video' accept='video/*' id='video'></div><div class='inputgroup'><p>Thumbnail</p><input type='file' name='thumbnail' accept='image/*'></div></div></div><input type='hidden' name='upload' value='upload lol'><button class='noskin uploadButton' id='ubtn' name='upload' value='salami upload'>Upload</button><img src='Icons/auxzload.gif' alt='' class='uploadButton' id='bar'><style id='stl'>#bar{display:none}</style></form></div></section>";
        }
        if(isset($_POST['uploadPromt']) && isset($_POST['error'])){
            $msg = htmlspecialchars($_POST['error'], ENT_QUOTES, 'UTF-8');
            $promt = "<section class='blur promtbg'><div class='promt shadow upload'><form action='' enctype='multipart/form-data' method='post' id='uplform'><h3>Upload</h3><div class='gridlayout'><div class='innerpromt'><div class='errorMsg'>$msg</div><div class='smolspace'></div><div class='inputgroup'><p>Title</p><input type='text' name='title' id='title'></div><div class='inputgroup'><p>Description</p><textarea name='desc' rows='14'></textarea></div></div><div class='innerpromt'><div class='smolspace'></div><div class='inputgroup'><p>Tags</p><input type='text' name='tags'></div><div class='inputgroup'><p>Visibility</p><select name='vis'><option value='1'>Visible</option><option value='0'>Invisible</option></select></div><div class='inputgroup'><p>Password</p><select name='pas'><option value='0'>No Password (public)</option><option value='1'>Password</option></select></div><div class='inputgroup'><p>Password</p><input type='text' name='password'></div><div class='inputgroup'><p>Video</p><input type='file' name='video' accept='video/*' id='video'></div><div class='inputgroup'><p>Thumbnail</p><input type='file' name='thumbnail' accept='image/*'></div></div></div><input type='hidden' name='upload' value='upload lol'><button class='noskin uploadButton' id='ubtn' name='upload' value='salami upload'>Upload</button><img src='Icons/auxzload.gif' alt='' class='uploadButton' id='bar'><style id='stl'>#bar{display:none}</style></form></div></section>";
        }
    }else{
        if(isset($_POST['loginPromt'])){
            $promt = "<section class='blur promtbg'><div class='promt shadow'><form action='' method='post'><h3>Login</h3><div class='innerpromt'><div class='smolspace'></div><div class='inputgroup'><p>Username</p><input type='text' name='name'></div><div class='inputgroup'><p>Password</p><input type='password' name='pass'></div><input type='submit' name='registerPromt' value='Register Instead' class='noskin palink'></div><button class='noskin' name='login' value='salami is good for you'>Login</button></form></div></section>";
        }
        if(isset($_POST['registerPromt'])){
            $promt = "<section class='blur promtbg'><div class='promt shadow'><form action='' method='post'><h3>Register</h3><div class='innerpromt'><div class='smolspace'></div><div class='inputgroup'><p>Username</p><input type='text' name='name'></div><div class='inputgroup'><p>Email</p><input type='text' name='mail'></div><div class='inputgroup'><p>Password</p><input type='password' name='pass1'></div><div class='inputgroup'><p>Password</p><input type='password' name='pass2'></div><div class='inputgroup'><input type='checkbox' name='tos' id='tos' class='check'><label for='tos'>I agree to the Auxzil Terms of Service</label></div><input type='submit' name='loginPromt' value='Login Instead' class='noskin palink'></div><input type='hidden'><button class='noskin' name='register' value='yes pleeease'>Register</button></form></div></section>";
        }
        if(isset($_POST['registerPromt']) && isset($_POST['error'])){
            $msg = htmlspecialchars($_POST['error'], ENT_QUOTES, 'UTF-8');
            $promt = "<section class='blur promtbg'><div class='promt shadow'><form action='' method='post'><h3>Register</h3><div class='innerpromt'><div class='errorMsg'>$msg</div><div class='smolspace'></div><div class='inputgroup'><p>Username</p><input type='text' name='name'></div><div class='inputgroup'><p>Email</p><input type='text' name='mail'></div><div class='inputgroup'><p>Password</p><input type='password' name='pass1'></div><div class='inputgroup'><p>Password</p><input type='password' name='pass2'></div><div class='inputgroup'><input type='checkbox' name='tos' id='tos' class='check'><label for='tos'>I agree to the Auxzil Terms of Service</label></div><input type='submit' name='loginPromt' value='Login Instead' class='noskin palink'></div><input type='hidden'><button class='noskin' name='register' value='yes pleeease'>Register</button></form></div></section>";
        }
    }
?>
<!--Promt handlers-->
<?php //LOGIN
    if(isset($_POST['login'])){
        $name = $_POST['name'];
        $pw = $_POST['pass'];

        $ret = SendSQL("SELECT * FROM Users WHERE Name = ?", array(&$name), "s", $conn);

        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                $userSalt = $i['Salt'];
                $hashedPw = $i['Password'];
                $compPw = hash("sha512", $pw . $userSalt);
                if($hashedPw == $compPw){
                    $_SESSION['UserID'] = $i['UserID'];
                    $_SESSION['Username'] = $i['Name'];
                    echo '<script>window.location="index.php"</script>';
                    exit;
                }
            }
		}
    }
?>
<?php //REGISTER
    if(isset($_POST['register'])){
        $username = $_POST['name'];
        $email = $_POST['mail'];
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];

        //check ToS
        if(!isset($_POST['tos'])){
            throwError("You have to agree to the Terms of service!", "registerPromt");
            exit;
        }

        //check if password matches
        if($pass1 != $pass2){
            throwError("Your Passwords do not match!", "registerPromt");
            exit;
        }

        //check if usernsame is taken
        $ret = SendSQL("SELECT * FROM Users WHERE Name = ?", array(&$username), "s", $conn);

        if($ret->num_rows > 0){
            throwError("The Username you chose is already taken!", "registerPromt");
            exit;
        }

        //check if email is already registered
        $ret = SendSQL("SELECT * FROM Users WHERE EMail= ?", array(&$email), "s", $conn);

        if($ret->num_rows > 0){
            throwError("The Email you entered is already registered!", "registerPromt");
            exit;
        }

        //check if email is valid
        $check = strpos($email, "@");
        if($check === false){
            throwError("The Email you entered is not valid!", "registerPromt");
            exit;
        }

        $check = strpos($email, ".");
        if($check === false){
            throwError("The Email you entered is not valid!", "registerPromt");
            exit;
        }

        //actually do the register thingy
        $uid = generateRandomString(25);
        $salt = generateRandomString(1024);
        $pfp = "/Icons/stpfp.jpg";
        $banner = "/Icons/nobanner.jpg";
        $follows = "a:0:{}";
        $Date = date("Y-m-d H:i:s");
        $pw = hash("sha512", $pwIn . $salt);

        SendSQL("INSERT INTO Users (UserID, Name, EMail, Password, Salt, ProfilePic, Banner, JoinDate, Follows) VALUES (?,?,?,?,?,?,?,?,?)", array(&$uid, &$username, &$email, &$pw, &$salt, &$pfp, &$banner, &$Date, &$follows), "sssssssss", $conn);

        $_SESSION['UserID'] = $uid;
        $_SESSION['Username'] = $name;
        echo '<script>window.location="index.php"</script>';
    }
?>
<?php //UPLOAD
    if(isset($_POST['upload'])){
        $Title = $_POST['title'];
        $Desc = $_POST['desc'];
        $Tags = $_POST['tags'];
        $Pass = $_POST['password'];
        $Visibility = $_POST['vis'];
        $Password = $_POST['pas'];
        $Video = $_FILES['video'];
        $Image = $_FILES['thumbnail'];
        $VideoID = generateRandomString(25);
        $UserID = $_SESSION['UserID'];
        $Salt = generateRandomString(16);
        $Hash = hash("sha512", $Password.$Salt);
        $Date = date("Y-m-d H:i:s");

        //check if title and video are provided

        if(empty(str_replace(" ","",$Title))){
            throwError("You need a Video Title!", "uploadPromt");
            exit;
        }
        if(empty($Video['name'])){
            throwError("You need to provide a Video!", "uploadPromt");
            exit;
        }

        //upload video
        $fileName = $Video['name'];
        $fileTmpName = $Video['tmp_name'];
        $fileSize = $Video['size'];
        $fileError = $Video['error'];
        $fileType = $Video['type'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('mp4', 'mkv');

        if(!in_array($fileActualExt, $allowed)){
            throwError("Auxzil only supports mp4 or mkv!", "uploadPromt");
            exit;
        }
        if($fileError !== 0){
            throwError("An unknown error occured while uploading!", "uploadPromt");
            exit;
        }
        if($fileSize > 10000000000){
            throwError("Your file is too big! [max 10gb]", "uploadPromt");
            exit;
        }

        //do the uploading
        $fileNameNew = uniqid('', true).".".$fileActualExt;
        $fileDestination = "VidUploads/".$fileNameNew;
        move_uploaded_file($fileTmpName, $fileDestination);
        $vURL = $fileDestination;

        //upload thumnail
        $fileName = $Image['name'];
        $fileTmpName = $Image['tmp_name'];
        $fileSize = $Image['size'];
        $fileError = $Image['error'];
        $fileType = $Image['type'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $tURL = "";

        //generate thumbnail
        if(empty($Image["name"])){
            $tURL = "PicUploads/".uniqid('', true).".jpg";
            echo shell_exec("../ffmpeg/ffmpeg -i '$vURL' -ss 0.5 -vframes 1 '$tURL'");
        }
        else{ //upload
            if(!in_array($fileActualExt, $allowed)){
                throwError("Auxzil only supports jpg, png, or gif!", "uploadPromt");
                exit;
            }
            if($fileError !== 0){
                throwError("An unknown error occured while uploading!", "uploadPromt");
                exit;
            }
            if($fileSize > 10000000000){
                throwError("Your file is too big! [max 10gb]", "uploadPromt");
                exit;
            }

            $fileNameNew = uniqid('', true).".".$fileActualExt;
            $fileDestination = "PicUploads/".$fileNameNew;
            move_uploaded_file($fileTmpName, $fileDestination);
            $tURL = $fileDestination;

            if($fileActualExt == "png"){
                $tURL = JpegCompress($tURL);
            }
        }

        //Finalize Upload
        SendSQL("INSERT INTO Videos (VideoID, URL, ThumbnailURL, Title, Description, UserID, Date, Tags, Visible, Password, Hash, Salt) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)", array(&$VideoID,&$vURL,&$tURL,&$Title,&$Desc,&$UserID,&$Date, &$Tags,&$Visibility,&$Password,&$Hash,&$Salt), "ssssssssiiss", $conn);

        //Notify Followers
        $ret = SendSQL("SELECT * FROM Users WHERE UserID= ?", array(&$UserID), "s", $conn);

        $value = "%\"$UserID\"%";
        $ret = SendSQL("SELECT * FROM Users WHERE Follows LIKE ?", array(&$value), "s", $conn);

        $hdln = "New Upload!";
        $vT = $Title;
        $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
        $usr = htmlspecialchars($usr, ENT_QUOTES, 'UTF-8');
        $txt = "<b>$usr</b> has uploaded a new Video called <b>$vT</b>!";
        $link = "watch.php/v=$VideoID";

        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                $cID = $i['UserID'];
                $nID = generateRandomString(64);
                SendSQL("INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Link, Date) VALUES (?,?,?,?,?,?)", array(&$nID,&$hdln,&$txt,&$cID,&$link,&$Date), "ssssss", $conn);
            }
        }

        echo "<script>window.location='watch.php?v=$VideoID'</script>";
        exit;
    }
?>
<?php //Fucntions
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function throwError($message, $reason) {
        echo "<form action='' method='post' id='errorForm'><input type='hidden' name='$reason' value='here we go again'><input type='hidden' name='error' value='$message'></form>";
        echo "<script>document.getElementById('errorForm').submit();</script>";
    }
    function JpegCompress($path) {
        $filePath = $path;
        $image = imagecreatefrompng($filePath);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 50; // 0 = worst / smaller file, 100 = better / bigger file
        imagejpeg($bg, $filePath . ".jpg", $quality);
        imagedestroy($bg);
        unlink($filePath);
        return $filePath. ".jpg";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="Redesign.css">
    <script src="https://kit.fontawesome.com/f3ebd80316.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
</head>
<body>
    <section class="headbar shadow">
        <div class="c1">
            <a href="index.php">Home</a>
            <a href="discover.php">Discover</a>
        </div>
        <div class="c2">
            <a href="index.php" class="headA"><img src="Auxzil.png" alt="auxzil" class="logo"></a>
        </div>
        <div class="c3">
            <section class="links">
                <form action="" method="post">
                <?php echo $UserButton;?>
                <input type='submit' name='uploadPromt' value='Upload' class='noskin palink'>
                </form>
            </section>
            <section class="user">
                <form action="" method="post" class="notiform">
                    <button class="noskin" name="noti" value="salami"><div class="pimage shadow" style="<?php echo $imageStyle;?>"><?php echo $notIndic;?></div></button>
                </form>
            </section>
        </div>
    </section>
    <?php echo $notbox;?>
    <?php echo $promt;?>
    <script>
    document.getElementById("ubtn").addEventListener("click", function(event){event.preventDefault(); abtnclick()});
    function abtnclick () {
        if(document.getElementById("title").value.replace(" ","") != "" && document.getElementById("video").value != ""){
            document.getElementById("stl").innerHTML = "#ubtn{display:none;}";
            document.getElementById("uplform").submit();
        }
    }
</script>
</body>
</html>