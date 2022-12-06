<?php session_start();?>
<?php
	$server = "localhost";
	$user = "auxzilco_ServerRegister";
	$pass = "7g(c_vRa#i?M";
	$db = "auxzilco_CentralDB";
	$table = "Videos";

	$conn = new mysqli($server, $user, $pass, $db);
	
	if($conn->connect_error)
	{
		die("Couldnt connect to SQL Server! (big oof)<br>");
	}
	
	
	if(isset($_GET['check']))
    {
        $db = mysqli_real_escape_string($conn, $_GET['db']);
        $cn = mysqli_real_escape_string($conn, $_GET['cName']);
        $cv = mysqli_real_escape_string($conn, $_GET['cValue']);

        $sql = "SELECT ". $cn ." FROM ". $db ." WHERE ". $cn ." = '". $cv ."'";
        $ret = $conn->query($sql);
        if($ret->num_rows > 0) { //DO NOT CHANGE THESE VALUES!
            echo("data found");
        } else {
            echo("data not found");
        }

        die();
    }

	/*echo "Succesfully connected to server!<br>";
	echo "Connected to '" . $db . "' database<br><br>";

	echo "Preparing your corn, give us a second...<br><br><br><br><br>";
	
	$sql = "SELECT * FROM Videos";
	$ret = $conn->query($sql);
	
	if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
        	echo("VideoID: " . $i["VideoID"] . "<br>" . " URL: " . $i["URL"] . "<br>" . " ThumbnailURL: " . $i["ThumbnailURL"] . "<br>" . " Title: " . $i["Title"] . "<br>" . " Description: " . $i["Description"] . "<br>" . " Info: " . $i["Info"] . "<br>" . "<br><br><br>");
    	}
    }
	
	$sql = "SELECT * FROM Users";
	$ret = $conn->query($sql);
	
	if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
        	echo("User_ID: " . $i["UserID"] . "<br>" . "User_Name: " . $i["Name"] . "<br>" . "User_Email: " . $i["EMail"] . "<br>" . "User_Address: " . $i["IPAddress"] . "<br>" . "User_Coins: " . $i["Coins"] . "<br>" . "User_Pass: " . $i["Password"] . "<br>" . "User_Salt: " . $i["Salt"] . "<br>" . "User_Pic: " . $i["ProfilePic"] . "<br>" . "User_Banner: " . $i["Banner"] . "<br>" . "User_Info: " . $i["About"] . "<br>");
    	}
    }
	
	if(isset($_POST["CreateUser"])) {
        echo("Writing stuff...");
		
		//Commented out bc something was broken
        //$sql = "INSERT INTO Users (UserID, Name, Email, IPAddress, Coins, Pass, Salt, PFP, Banner, Info) VALUES ('$_POST['UserID']','$_POST['Name']','$_POST['EMail']','$_POST['IPAddress']','$_POST['Coins']','$_POST['Password']','$_POST['Salt']','$_POST['ProfilePic']','$_POST['Banner']','$_POST['About']')";
        //$ret = $connection->query($sql);
    }*/
	
?>