<?php session_start();?>
<?php
    $label = array("Name", "About", "Profile Pic", "Banner", "About");
    $funcNames = array("changename", "changeabout");
    $pageName = "Manage Account";
    $styleStuff = "";
    $styleCorn = "<style>#dB{display:none}</style>";
    $DmStatus = "On";
    $redirect = "";

    if(isset($_POST['changemode'])){
        if(!isset($_COOKIE['lightmode'])){
            setcookie("lightmode", true);
        }
        else{
            setcookie("lightmode", !$_COOKIE['lightmode']);
        }
        $redirect = "<script>window.location='index.php'</script>";
    }

    if(isset($_COOKIE['lightmode'])){
        if($_COOKIE['lightmode']){
            $DmStatus = "Off";
        }
    }

    if(isset($_POST['Creator'])){
        if($_POST['Creator'] != $_SESSION['UserID']){
            echo "<script>window.location='index.php'</script>";
        }
    }

    if(isset($_POST['EditVideo'])){
        $label = array("Title", "Description", "", "", "Desc");
        $funcNames = array("changetitle", "changedesc");
        $pageName = "Edit Video";
        $styleStuff = "<style>.hwe{display:none}</style>";
        $styleCorn = "";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxzil</title>
    <link rel="stylesheet" href="Design.css">
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
</head>
<body>
    <?php include 'header.php';?>
    <?php echo $styleStuff; echo $styleCorn?>
    <?php echo $redirect;?>
    <div class="loginbox" id="man">
            <p><?php echo $pageName;?></p>
            <form action="" enctype="multipart/form-data">
                <div class= "manForm">
                    <div class="labels">
                        <section class="spacer"></section>
                        <label for="uName"><?php echo $label[0]?></label>
                        <section class="spacer"></section>
                        <label for="pw1"><?php echo $label[1]?></label>
                        <section class="bigspacer"></section>
                        <label for="video"><?php echo $label[2]?></label>
                        <section class="spacer"></section>
                        <label for="thumbnail"><?php echo $label[3]?></label>
                        <section class="spacer"></section>
                        <label class="hwe">Darkmode</label>
                    </div>
                    <div class ="inputs">
                        <input type="text" id = "uName" name="newName">
                        <textarea name="newAbout" rows="10" cols="30"></textarea>
                        <input name="newPfp" type="file" accept="image/*" class="hwe">
                        <input name="newBanner" type="file" accept="image/*" class="hwe">
                        <input type="hidden" name="videoEditID" value="<?php echo $_POST['VideoToEdit']?>">
                        <input type="text" readonly value="<?php echo $DmStatus;?>" class="hwe">
                    </div>
                    <div>
                        <button name="<?php echo $funcNames[0]?>" type="submit" formmethod="post" class="updtButton">Update <?php echo $label[0]?></button>
                        <button name="<?php echo $funcNames[1]?>" type="submit" formmethod="post" class="updtButton">Update <?php echo $label[4]?></button>
                        <button name="deleteVideo" type="submit" formmethod="post" id="dB">Delete Video</button>
                        <section class="bigspacer2"></section>
                        <button name="changepfp" type="submit" formmethod="post" class="updtButton hwe" id="littlespacerpleasehelpme">Update PFP</button>
                        <button name="changesbanner" type="submit" formmethod="post" class="updtButton hwe">Update Banner</button>
                        <button name="changemode" type="submit" formmethod="post" class="updtButton hwe" id="swbtn">Switch!</button>
                    </div>
                </div>
                <section class="spacer"></section>
              </form>
        </div>
</body>
</html>

<?php
    if(!isset($_SESSION['UserID'])){
        echo '<script>window.location="index.php"</script>';
    }

    $server = "localhost";
    $user = "auxzilco_ServerRegister";
    $pass = "7g(c_vRa#i?M";
    $db = "auxzilco_CentralDB";

    $conn = new mysqli($server, $user, $pass, $db);

    if($conn->connect_error)
    {
      die("Couldnt connect to SQL Server! (big oof)<br>");
    }

    $uID = $_SESSION['UserID'];

    if(isset($_POST['videoEditID'])){
        $veID = $_POST['videoEditID'];
        if(isset($_POST['changetitle'])){
            $newTitle = mysqli_real_escape_string($conn, $_POST['newName']);
            $sql = "UPDATE Videos SET Title = '$newTitle' WHERE VideoID = '$veID';";
            $ret = $conn->query($sql);
            //Redirecting to Userpage
            echo '<script>window.location="watch.php?v='.$veID.'"</script>';
            die();
        }
        if(isset($_POST['changedesc'])){
            $newDesc = mysqli_real_escape_string($conn, $_POST['newAbout']);
            $sql = "UPDATE Videos SET Description = '$newDesc' WHERE VideoID = '$veID';";
            $ret = $conn->query($sql);
            //Redirecting to Userpage
            echo '<script>window.location="watch.php?v='.$veID.'"</script>';
            die();
        }
        if(isset($_POST['deleteVideo'])){
            $sql = "DELETE FROM Videos WHERE VideoID = '$veID';";
            $ret = $conn->query($sql);
            //Redirecting to Userpage
            echo '<script>window.location="User.php?u='. $_SESSION['UserID'] .'"</script>';
            die();
        }
    }

    if(isset($_POST['changename']))
    {
        $newUsername = mysqli_real_escape_string($conn, $_POST['newName']);
        $sql = "UPDATE Users SET Name = '$newUsername' WHERE UserID = '$uID';";
        $ret = $conn->query($sql);
        $_SESSION['Username'] = $newUsername;
        //Redirecting to Userpage
        echo '<script>window.location="User.php?u='.$uID.'"</script>';
    }
    if(isset($_POST['changeabout']))
    {
        $newAbout = mysqli_real_escape_string($conn, $_POST['newAbout']);
        $sql = "UPDATE Users SET About = '$newAbout' WHERE UserID = '$uID';";
        $ret = $conn->query($sql);
        //Redirecting to Userpage
        echo '<script>window.location="User.php?u='.$uID.'&ab=1"</script>';
    }
    if(isset($_POST['changepfp']))
    {
        $file = $_FILES['newPfp'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        if(in_array($fileActualExt, $allowed)){
            if($fileError === 0){
                if($fileSize < 100000000){
                    $fileNameNew = uniqid('', true).".".$fileActualExt;
                    $fileDestination = "PFPUploads/".$fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    //Compression
                    $PFPPath = $fileDestination;
                    if($fileActualExt == "png"){
                        $filePath = $fileDestination;
                        $image = imagecreatefrompng($filePath);
                        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                        imagealphablending($bg, TRUE);
                        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                        imagedestroy($image);
                        $quality = 50; // 0 = worst / smaller file, 100 = better / bigger file 
                        imagejpeg($bg, $filePath . ".jpg", $quality);
                        imagedestroy($bg);
                        $PFPPath = $filePath. ".jpg";
                        unlink($filePath);
                    }
                    //\Compression

                    //Delete old profile pic
                    $sql = "SELECT * FROM Users WHERE UserID = '$uID'";
                    $ret = $conn->query($sql);

                    $info = $ret->fetch_assoc();

                    if($info['ProfilePic'] != "/Icons/stpfp.jpg"){
                        unlink($info['ProfilePic']);
                    }

                    $sql = "UPDATE Users SET ProfilePic = '$PFPPath' WHERE UserID = '$uID';";
                    $ret = $conn->query($sql);
                    //Redirecting to Userpage
                    echo '<script>window.location="User.php?u='.$uID.'"</script>';
                }else{
                    echo "Your file is too big!";
                }
            }else{
                echo "There was an error while uploading the file!";
            }
        }
    }
    if(isset($_POST['changesbanner']))
    {
        $file = $_FILES['newBanner'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        if(in_array($fileActualExt, $allowed)){
            if($fileError === 0){
                if($fileSize < 100000000){
                    $fileNameNew = uniqid('', true).".".$fileActualExt;
                    $fileDestination = "BannerUploads/".$fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    //Compression
                    $PFPPath = $fileDestination;
                    if($fileActualExt == "png"){
                        $filePath = $fileDestination;
                        $image = imagecreatefrompng($filePath);
                        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                        imagealphablending($bg, TRUE);
                        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                        imagedestroy($image);
                        $quality = 50; // 0 = worst / smaller file, 100 = better / bigger file 
                        imagejpeg($bg, $filePath . ".jpg", $quality);
                        imagedestroy($bg);
                        $PFPPath = $filePath. ".jpg";
                        unlink($filePath);
                    }
                    //\Compression

                    //Delete old profile pic
                    $sql = "SELECT * FROM Users WHERE UserID = '$uID'";
                    $ret = $conn->query($sql);

                    $info = $ret->fetch_assoc();

                    if($info['Banner'] != "/Icons/nobanner.jpg"){
                        unlink($info['Banner']);
                    }

                    $sql = "UPDATE Users SET Banner = '$PFPPath' WHERE UserID = '$uID';";
                    $ret = $conn->query($sql);
                    //Redirecting to Userpage
                    echo '<script>window.location="User.php?u='.$uID.'"</script>';
                }else{
                    echo "Your file is too big!";
                }
            }else{
                echo "There was an error while uploading the file!";
            }
        }
    }
?>
