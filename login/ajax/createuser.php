<?php
require '../autoload.php';
try {
    //Pull username, generate new ID and hash password
    $newid = uniqid(rand(), false);
    $newuser = str_replace(' ', '', $_POST['newuser']);

    if ($newuser == '') {
        throw new Exception('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Must enter a username</div><div id="returnVal" style="display:none;">false</div>');
  $da = array("method" => "createAddress");
        $da_string = json_encode($da);

        $kech2 = curl_init('http://localhost:8070/json_rpc');
        curl_setopt($kech2, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($kech2, CURLOPT_POSTFIELDS, $da_string);
        curl_setopt($kech2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($kech2, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($da_string))
        );

        $result2 = curl_exec($kech2);
        $det1 = json_decode($result2, true);
        $address = $det1['result']['address'];
        $servername = "localhost";
        $username = "user";
        $password = "pass";
        $dbname = "database";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }

        $sql = "update strm_members set address='$address' where username='$newuser'";

        if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

$conn->close();
    }

    $newemail = $_POST['email'];
    $pw1 = $_POST['password1'];
    $pw2 = $_POST['password2'];
    $userarr = Array(Array('id'=>$newid, 'username'=>$newuser, 'email'=>$newemail, 'pw'=>$pw1));

    $config = new AppConfig;

    $conf = $config->pullMultiSettings(array("password_policy_enforce", "password_min_length", "signup_thanks", "base_url" ));

    $pwresp = PasswordPolicy::validate($pw1, $pw2, (bool) $conf["password_policy_enforce"], (int) $conf["password_min_length"]);

    if (!filter_var($newemail, FILTER_VALIDATE_EMAIL) == true) {

        echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Must provide a valid email address</div><div id="returnVal" style="display:none;">false</div>';

    } else {
        //Validation passed
        if (isset($_POST['newuser']) && !empty(str_replace(' ', '', $_POST['newuser'])) && $pwresp['status'] == 1) {

            $a = new NewUser;

            $response = $a->createUser($userarr);

            //Success
            if ($response == 1) {

                echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $conf['signup_thanks'] .'</div><div id="returnVal" style="display:none;">true</div><form action="'.$conf['base_url'].'/login/index.php"><button class="btn btn-success">Login</button></form><div id="returnVal" style="display:none;">true</div>';

                try { //Send verification email
                    $m = new MailSender;

                    $m->sendMail($userarr, 'Verify');

                } catch (Exception $e) {

                    echo $e->getMessage();
                }

            } else {
                //DB Failure
                MiscFunctions::mySqlErrors($response);

            }
        } else {
            //Password Failure
            echo $pwresp['message'];
        }
    }

} catch (Exception $x) {

    echo $x->getMessage();
}
