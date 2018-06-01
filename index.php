<?php
$title = 'Home';
include "login/misc/pagehead.php";
?>
</head>
<body>
    <div class="container">

<?php

$auth = new AuthorizationHandler;

if ($auth->isLoggedIn()){

    echo '<div class="jumbotron text-center"><h1>Hi, '.$_SESSION['username'].'!</h1>
    <p>Click on your username in the top right corner to expose menu options</p></div>
    <div class="col-lg-2"></div><div class="col-lg-8">
    <h2>Menu Items:</h2>
    
//begin mysql

$servername = "localhost";
$username = "username";
$password = "pass";
$dbname = "database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username,address FROM strm_members WHERE username='".$_SESSION['username']."' ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<img src="picture of coin"></img>';
        echo "<h3>Your forknotecoin deposit and withdrawal Address: ". $row["address"]. "  </h3>";
         $address1 = $row["address"];
        $da8 = array("method" => "getBalance", "params" => array('address' => $address1));
//$dat = array("method" => "createAddress");
$da8_string = json_encode($da8);

$kech8 = curl_init('http://localhost:8070/json_rpc');
curl_setopt($kech8, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($kech8, CURLOPT_POSTFIELDS, $da8_string);
curl_setopt($kech8, CURLOPT_RETURNTRANSFER, true);
curl_setopt($kech8, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($da8_string))
);

$result8 = curl_exec($kech8);
//echo $result8;
$det8 = json_decode($result8, true);
$balance = $det8['result']['availableBalance'];
$normalbalance = $balance / "100000000000";




        }
} else {
    echo "0 results";
}
    

    <p><b><em>Edit Profile</em></b> - Edit your own user profile information including your name, contact info, avatar, etc</p>

    <p><b><em>Account Settings</em></b> - Change your email address and/or password</p>';

    if ($auth->isAdmin()) {
        echo '<p><b><em>Verify/Delete Users</em></b> - Admin mass verify or delete new user requests</p>';
    }

    if ($auth->isSuperAdmin()) {
        echo '<p><b><em>Edit Site Config</em></b> - Superadmin edit site configuration in one page</p>';
        echo '<p><b><em>Mail Log</em></b> - Superadmin mail status logging</p>';
    }

} else {

    echo '<div class="jumbotron text-center"><h1 class="display-1">Homepage</h1>
    <small>This is your homepage. You are currently signed out.</small><br><br>
    <p>You can sign in or create a new account by clicking "Sign In" in the top right corner!</p>';
}

?>

        </div><div class="col-lg-2"></div>



    </div>
</body>
</html>
