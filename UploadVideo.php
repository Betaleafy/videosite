<?php session_start();?>
<?php
    if(!isset($_SESSION['UserID'])){
        echo "<script>window.location='index.php'</script>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
    <link rel="stylesheet" href="Design.css">
    <link rel="stylesheet" href="pgb.css">
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
</head>
<body>
<?php include 'header.php';?>
<?php echo $styleprint;?>
<style id="stl">#uploadgif{display:none;}</style>
    <div>
        <div class="loginbox" id="upl">
            <p>Upload!</p>
            <form action="" enctype="multipart/form-data" id="uplform" method="post">
                <div id="uploadgif">
                    <style>b, strong {font-weight: bold;}</style>
                    <label id="upllabel">Uploading...</label>
                    <section class="spacer"></section>
                </div>
                <div class= "regForm uploadbutton">
                <div class="labels">
                    <section class="spacer"></section>
                    <label>Title</label>
                    <section class="spacer"></section>
                    <label for="pw1">Description</label>
                    <section class="bigspacer"></section>
                    <label for="video">Video</label>
                    <section class="spacer"></section>
                    <label for="thumbnail">Thumbnail</label>
                </div>
                <div class ="inputs">
                    <input type="text" id = "titleinput" name="title">
                    <textarea name="description" rows="10" cols="30"></textarea>
                    <input name="video" type="file" accept="video/*" id="videoinput">
                    <input name="thumbnail" type="file" accept="image/*">
                    <input type="hidden" name="upload" value="1">
                </div>
                </div>
                <button class="uploadbutton" id="abtn">Upload!</button>
                <img src="Icons/upl.gif" alt="" id="uploadgif">
              </form>
        </div>
    </div>
    <section class="spacer"></section>
</body>
</html>
<script>
    document.getElementById("abtn").addEventListener("click", function(event){event.preventDefault(); abtnclick()});
    function abtnclick () {
        if(document.getElementById("titleinput").value.replace(" ","") != "" && document.getElementById("videoinput").value != ""){
            document.getElementById("stl").innerHTML = ".uploadbutton{display:none;}";
            document.getElementById("upllabel").innerHTML = "Uploading <b>"+ document.getElementById("titleinput").value +"</b>...";
            document.getElementById("uplform").submit();
        }
    }
</script>
<?php
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

    $VideoID = generateRandomString(25);
    $vURL = "";
    $ThumbnailURL = "";
    $Title = mysqli_real_escape_string($conn, $_POST['title']);
    $Description = mysqli_real_escape_string($conn, $_POST['description']);
    $UserID = $_SESSION['UserID'];
    $Coins = 0;
    $Likes = 0;
    $DisLikes = 0;
    $Date = date("Y-m-d H:i:s");
    if(isset($_POST['upload']) && isset($_SESSION['UserID'])){
        $parsedTitle = str_replace(" ","",$Title);
        if($parsedTitle != "" && !empty($_FILES["video"]["name"])){
            $file = $_FILES['video'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];
            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));
            $allowed = array('mp4', 'mkv', 'avi');
            if(in_array($fileActualExt, $allowed)){
                if($fileError === 0){
                    if($fileSize < 10000000000){
                        $fileNameNew = uniqid('', true).".".$fileActualExt;
                        $fileDestination = "VidUploads/".$fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        $vURL = $fileDestination;
                        uploadThumbnail($VideoID, $vURL, $ThumbnailURL, $Title, $Description, $_SESSION['UserID'], $Coins, $Likes, $Dislikes, $Date, $conn);
                    }else{
                        echo "Your file is too big!";
                    }
                }else{
                    echo "There was an error while uploading the file!";
                }
            }else{
                echo "Cant upload files of this type! | ". $fileActualExt." | ".$fileName;
            }
        }
    }
    function uploadThumbnail($v1, $v2, $v3, $v4, $v5, $v6, $v7, $v8, $v9, $v10, $con) {
        if(empty($_FILES["thumbnail"]["name"])){
            $ThumbnailURL = "PicUploads/".uniqid('', true).".jpg";
            echo shell_exec("../ffmpeg/ffmpeg -i '$v2' -ss 0.5 -vframes 1 '$ThumbnailURL'");
            finalizeUpload($v1, $v2, $ThumbnailURL, $v4, $v5, $v6, $v7, $v8, $v9, $v10, $con);
        }
        else{
            $file = $_FILES['thumbnail'];
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
                    if($fileSize < 10000000000){
                        $fileNameNew = uniqid('', true).".".$fileActualExt;
                        $fileDestination = "PicUploads/".$fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        //Compression
                        $ThumbnailURL = $fileDestination;
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
                            $ThumbnailURL = $filePath. ".jpg";
                            unlink($filePath);
                        }
                        //Compression
                        finalizeUpload($v1, $v2, $ThumbnailURL, $v4, $v5, $v6, $v7, $v8, $v9, $v10, $con);
                    }else{
                        echo "Your file is too big!";
                    }
                }else{
                    echo "There was an error while uploading the file!";
                }
            }else{
                echo "Cant upload files of this type! | ". $fileActualExt." | ".$fileName;
            }
        }
    }
    function finalizeUpload($v1, $v2, $v3, $v4, $v5, $v6, $v7, $v8, $v9, $v10, $con){
        $sql = "INSERT INTO Videos (VideoID, URL, ThumbnailURL, Title, Description, UserID, Coins, Likes, Dislikes, Date) VALUES ('$v1','$v2','$v3','$v4','$v5','$v6', '$v7','$v8','$v9','$v10')";
        $ret = $con->query($sql);

        //Notify Followers
        $sql = "SELECT * FROM Users WHERE UserID = '$v6'";
        $ret = $con->query($sql);
        $info = $ret->fetch_assoc();
        $usr = $info['Name'];

        $sql = "SELECT * FROM Users WHERE Follows LIKE '%\"$v6\"%'";
        $ret = $con->query($sql);

        $hdln = "New Upload!";
        $vT = $v4;
        $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
        $usr = htmlspecialchars($usr, ENT_QUOTES, 'UTF-8');
        $txt = "<b>$usr</b> has uploaded a new Video called <b>$vT</b>!";
        $txt = mysqli_real_escape_string($con, $txt);
        $date = date("Y-m-d H:i:s");
        $link = "watch.php/v=$v1";

        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                $cID = $i['UserID'];
                $nID = generateRandomString(64);
                $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Link, Date) VALUES ('$nID','$hdln','$txt','$cID','$link','$date')";
                $ret2 = $con->query($sql);
            }
        }

        echo '<script>window.location="watch.php?v='.$v1.'"</script>';
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
