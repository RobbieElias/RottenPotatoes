<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

if (!$loggedIn) {
    header('Location: index.php');
    die();
}

$tab = "account";
if (!empty($_GET['tab'])) {
    if ($_GET['tab'] === 'profile')
        $tab = 'profile';
} 

$userid = $_SESSION['userid'];
$messageAccount = null;
$messageProfile = null;
$messagePassword = null;
$accountUpdated = false;
$profileUpdated = false;
$passwordUpdated = false;
$ageranges = [
    ['text' => '0-17', 'value' => '[0,17]'],
    ['text' => '18-25', 'value' => '[18,25]'],
    ['text' => '26-39', 'value' => '[26,39]'],
    ['text' => '40+', 'value' => '[40,]']
];

if (isset($_POST['account'])) {

    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $country = filterString(trim($_POST['country']), 100);
    $province = filterString(trim($_POST['province']), 100);
    $city = filterString(trim($_POST['city']), 100);

    if (validateEmail($email) && validateName($name)) {

        try {

            $uniqueEmail = true;
            $db->bindMore(array('email'=>$email,'userid'=>$userid));
            $exists = $db->single('SELECT 1 FROM movieuser WHERE email = :email AND userid != :userid');
            if (!empty($exists)) 
                $uniqueEmail = false;

            if ($uniqueEmail) {
                list($firstname, $lastname) = explode(' ', $name);
                $update = $db->query('UPDATE movieuser SET lastname = :lastname, firstname = :firstname, email = :email, city = :city, province = :province, country = :country WHERE userid = :userid', array('lastname'=>$lastname,'firstname'=>$firstname,'email'=>$email,'city'=>$city,'province'=>$province,'country'=>$country,'userid'=>$userid));

                if ($update > 0) {
                    $_SESSION['firstname'] = $firstname;
                    $accountUpdated = true;
                    $messageAccount = "Your account has been successfully updated.";
                }
                else {
                    $messageAccount = "Your account could not be updated.";   
                }
            }
            else {
                $messageAccount = 'Sorry, this email is already in use.';
            }
        }
        catch (PDOException $e) {
            $messageAccount = 'Your account could not be updated.';
        }

    }

}
else if (isset($_POST['profile'])) {

    $tab = 'profile';

    $gender = !empty($_POST['gender']) ? $_POST['gender'] : 'male';
    $agerangeIndex = !empty($_POST['age']) ? $_POST['age'] : 0;
    $occupation = filterString(trim($_POST['occupation']), 50);
    $device = filterString(trim($_POST['device']), 20);

    if (validateGender($gender) && !empty($ageranges[$agerangeIndex])) {

        try {
            $hasProfile = false;
            $db->bind('userid',$userid);
            $exists = $db->single('SELECT 1 FROM profile WHERE userid = :userid');
            if (!empty($exists)) 
                $hasProfile = true;

            if ($hasProfile) {
                $update = $db->query('UPDATE profile SET agerange = :agerange, gender = :gender, occupation = :occupation, deviceused = :device WHERE userid = :userid', array('agerange'=>$ageranges[$agerangeIndex]['value'],'gender'=>$gender,'occupation'=>$occupation,'device'=>$device,'userid'=>$userid));

                if ($update > 0) {
                    $profileUpdated = true;
                    $messageProfile = "Your profile has been successfully updated.";
                }
            }
            else {
                $insert = $db->query('INSERT INTO profile(userid, agerange, gender, occupation, deviceused) VALUES (:agerange, :gender, :occupation, :device)', array('agerange'=>$ageranges[$agerangeIndex]['value'],'gender'=>$gender,'occupation'=>$occupation,'device'=>$device));

                if ($insert > 0) {
                    $profileUpdated = true;
                    $messageProfile = "Your profile has been successfully updated.";
                }
            }

            if (!$profileUpdated)
                $messageProfile = 'Your profile could not be updated.';
        }
        catch (PDOException $e) {
            $messageProfile = 'Your profile could not be updated.';
        }

    }

} else if (isset($_POST['password'])) {

    $tab = 'password';

    $oldPassword = !empty($_POST['old-password']) ? $_POST['old-password'] : null;
    $newPassword = !empty($_POST['new-password']) ? $_POST['new-password'] : null;

    if (validatePassword($oldPassword) && validatePassword($newPassword)) {

        try {
            $db->bindMore(array('newpassword'=>$newPassword,'userid'=>$userid,'oldpassword'=>$oldPassword));
            $update = $db->query('UPDATE movieuser SET password = :newpassword WHERE userid = :userid AND password = :oldpassword');

            if ($update > 0) {
                $passwordUpdated = true;
                $messagePassword = "Your password has been successfully updated.";
            }
            else {
                $messagePassword = 'The old password you specified is incorrect.';    
            }
        }
        catch (PDOException $e) {
            $messagePassword = 'Your password could not be updated.';
        }

    }

}

$db->bind('userid',$userid);
$user = $db->row('SELECT lastname, firstname, password, email, city, province, country FROM movieuser WHERE userid = :userid');

$db->bind('userid',$userid);
$profile = $db->row('SELECT lower(agerange) AS lowerrange, upper(agerange) AS upperrange, gender, occupation, deviceused FROM profile WHERE userid = :userid');

$agerange = 0;
if (!empty($profile)) {
    switch ($profile['lowerrange']) {
        default:
        case '0':
            $agerange = 0;
            break;
        case '18':
            $agerange = 1;
            break;
        case '26':
            $agerange = 2;
            break;
        case '40':
            $agerange = 3;
            break;
    }

    $gender = strtolower($profile['gender']);
    $occupation = $profile['occupation'];
    $device = $profile['deviceused'];
}

function validateEmail($email) {
    if (empty($email) || strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

function validateName($name) {
    if (empty($name) || strlen($name) > 200 || !preg_match("/(\w+)( )(\w+)/", $name)) {
        return false;
    }
    return true;
}

function validatePassword($password) {
    if (empty($password) || strlen($password) > 20) {
        return false;
    }
    return true;
}

function validateGender($gender) {
    if ($gender === 'male' || $gender === 'female' || $gender === 'other') {
        return true;
    }
    return false;
}

function filterString($string, $maxLength) {
    if (empty($string) || strlen($string) > $maxLength) {
        return null;
    }
    return $string;
}

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title>Account Settings - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container container-account">
        <h1>Account Settings <span class="title-view-all">(<a href="profile.php">Profile</a>)</span></h1>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" <?php if ($tab === 'account') echo 'class="active"' ?>><a id="#tab-link-account" href="#account" aria-controls="account" role="tab" data-toggle="tab">Account</a></li>
            <li role="presentation" <?php if ($tab === 'profile') echo 'class="active"' ?>><a id="#tab-link-profile" href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
            <li role="presentation" <?php if ($tab === 'password') echo 'class="active"' ?>><a id="#tab-link-password" href="#change-password" aria-controls="change-password" role="tab" data-toggle="tab">Change Password</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane <?php if ($tab === 'account') echo 'active' ?>" id="account">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <form id="registerForm" method="post" action="account.php" data-toggle="validator" role="form">
                            <?php if ($accountUpdated) { ?>
                            <h4 class="text-success"><?php echo $messageAccount ?></h4>
                            <?php } else if (!empty($messageAccount)) { ?>
                            <h4 class="text-danger"><?php echo $messageAccount ?></h4>
                            <?php } ?>
                            <div class="form-group">
                                <label class="control-label">Email Address*</label>
                                <input id="email" type="email" class="form-control" name="email" maxlength="150" value="<?php echo $user['email'] ?>" required />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Name*</label>
                                <input id="name" type="text" class="form-control" name="name" maxlength="200" pattern="(\w+)( )(\w+)" value="<?php echo $user['firstname'] . ' ' . $user['lastname'] ?>" required />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Country</label>
                                <select id="country" class="form-control" name="country">
                                    <option value="Afganistan">Afghanistan</option>
                                    <option value="Albania">Albania</option>
                                    <option value="Algeria">Algeria</option>
                                    <option value="American Samoa">American Samoa</option>
                                    <option value="Andorra">Andorra</option>
                                    <option value="Angola">Angola</option>
                                    <option value="Anguilla">Anguilla</option>
                                    <option value="Antigua & Barbuda">Antigua &amp; Barbuda</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="Armenia">Armenia</option>
                                    <option value="Aruba">Aruba</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Austria">Austria</option>
                                    <option value="Azerbaijan">Azerbaijan</option>
                                    <option value="Bahamas">Bahamas</option>
                                    <option value="Bahrain">Bahrain</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Barbados">Barbados</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Belgium">Belgium</option>
                                    <option value="Belize">Belize</option>
                                    <option value="Benin">Benin</option>
                                    <option value="Bermuda">Bermuda</option>
                                    <option value="Bhutan">Bhutan</option>
                                    <option value="Bolivia">Bolivia</option>
                                    <option value="Bonaire">Bonaire</option>
                                    <option value="Bosnia & Herzegovina">Bosnia &amp; Herzegovina</option>
                                    <option value="Botswana">Botswana</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                    <option value="Brunei">Brunei</option>
                                    <option value="Bulgaria">Bulgaria</option>
                                    <option value="Burkina Faso">Burkina Faso</option>
                                    <option value="Burundi">Burundi</option>
                                    <option value="Cambodia">Cambodia</option>
                                    <option value="Cameroon">Cameroon</option>
                                    <option value="Canada" selected="selected">Canada</option>
                                    <option value="Canary Islands">Canary Islands</option>
                                    <option value="Cape Verde">Cape Verde</option>
                                    <option value="Cayman Islands">Cayman Islands</option>
                                    <option value="Central African Republic">Central African Republic</option>
                                    <option value="Chad">Chad</option>
                                    <option value="Channel Islands">Channel Islands</option>
                                    <option value="Chile">Chile</option>
                                    <option value="China">China</option>
                                    <option value="Christmas Island">Christmas Island</option>
                                    <option value="Cocos Island">Cocos Island</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Comoros">Comoros</option>
                                    <option value="Congo">Congo</option>
                                    <option value="Cook Islands">Cook Islands</option>
                                    <option value="Costa Rica">Costa Rica</option>
                                    <option value="Cote DIvoire">Cote D'Ivoire</option>
                                    <option value="Croatia">Croatia</option>
                                    <option value="Cuba">Cuba</option>
                                    <option value="Curaco">Curacao</option>
                                    <option value="Cyprus">Cyprus</option>
                                    <option value="Czech Republic">Czech Republic</option>
                                    <option value="Denmark">Denmark</option>
                                    <option value="Djibouti">Djibouti</option>
                                    <option value="Dominica">Dominica</option>
                                    <option value="Dominican Republic">Dominican Republic</option>
                                    <option value="East Timor">East Timor</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="Egypt">Egypt</option>
                                    <option value="El Salvador">El Salvador</option>
                                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                                    <option value="Eritrea">Eritrea</option>
                                    <option value="Estonia">Estonia</option>
                                    <option value="Ethiopia">Ethiopia</option>
                                    <option value="Falkland Islands">Falkland Islands</option>
                                    <option value="Faroe Islands">Faroe Islands</option>
                                    <option value="Fiji">Fiji</option>
                                    <option value="Finland">Finland</option>
                                    <option value="France">France</option>
                                    <option value="French Guiana">French Guiana</option>
                                    <option value="French Polynesia">French Polynesia</option>
                                    <option value="French Southern Ter">French Southern Ter</option>
                                    <option value="Gabon">Gabon</option>
                                    <option value="Gambia">Gambia</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Germany">Germany</option>
                                    <option value="Ghana">Ghana</option>
                                    <option value="Gibraltar">Gibraltar</option>
                                    <option value="Great Britain">Great Britain</option>
                                    <option value="Greece">Greece</option>
                                    <option value="Greenland">Greenland</option>
                                    <option value="Grenada">Grenada</option>
                                    <option value="Guadeloupe">Guadeloupe</option>
                                    <option value="Guam">Guam</option>
                                    <option value="Guatemala">Guatemala</option>
                                    <option value="Guinea">Guinea</option>
                                    <option value="Guyana">Guyana</option>
                                    <option value="Haiti">Haiti</option>
                                    <option value="Hawaii">Hawaii</option>
                                    <option value="Honduras">Honduras</option>
                                    <option value="Hong Kong">Hong Kong</option>
                                    <option value="Hungary">Hungary</option>
                                    <option value="Iceland">Iceland</option>
                                    <option value="India">India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Iran">Iran</option>
                                    <option value="Iraq">Iraq</option>
                                    <option value="Ireland">Ireland</option>
                                    <option value="Isle of Man">Isle of Man</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Jamaica">Jamaica</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Jordan">Jordan</option>
                                    <option value="Kazakhstan">Kazakhstan</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="Kiribati">Kiribati</option>
                                    <option value="Korea North">Korea North</option>
                                    <option value="Korea Sout">Korea South</option>
                                    <option value="Kuwait">Kuwait</option>
                                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                                    <option value="Laos">Laos</option>
                                    <option value="Latvia">Latvia</option>
                                    <option value="Lebanon">Lebanon</option>
                                    <option value="Lesotho">Lesotho</option>
                                    <option value="Liberia">Liberia</option>
                                    <option value="Libya">Libya</option>
                                    <option value="Liechtenstein">Liechtenstein</option>
                                    <option value="Lithuania">Lithuania</option>
                                    <option value="Luxembourg">Luxembourg</option>
                                    <option value="Macau">Macau</option>
                                    <option value="Macedonia">Macedonia</option>
                                    <option value="Madagascar">Madagascar</option>
                                    <option value="Malaysia">Malaysia</option>
                                    <option value="Malawi">Malawi</option>
                                    <option value="Maldives">Maldives</option>
                                    <option value="Mali">Mali</option>
                                    <option value="Malta">Malta</option>
                                    <option value="Marshall Islands">Marshall Islands</option>
                                    <option value="Martinique">Martinique</option>
                                    <option value="Mauritania">Mauritania</option>
                                    <option value="Mauritius">Mauritius</option>
                                    <option value="Mayotte">Mayotte</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Midway Islands">Midway Islands</option>
                                    <option value="Moldova">Moldova</option>
                                    <option value="Monaco">Monaco</option>
                                    <option value="Mongolia">Mongolia</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Morocco">Morocco</option>
                                    <option value="Mozambique">Mozambique</option>
                                    <option value="Myanmar">Myanmar</option>
                                    <option value="Nambia">Nambia</option>
                                    <option value="Nauru">Nauru</option>
                                    <option value="Nepal">Nepal</option>
                                    <option value="Netherland Antilles">Netherland Antilles</option>
                                    <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                    <option value="Nevis">Nevis</option>
                                    <option value="New Caledonia">New Caledonia</option>
                                    <option value="New Zealand">New Zealand</option>
                                    <option value="Nicaragua">Nicaragua</option>
                                    <option value="Niger">Niger</option>
                                    <option value="Nigeria">Nigeria</option>
                                    <option value="Niue">Niue</option>
                                    <option value="Norfolk Island">Norfolk Island</option>
                                    <option value="Norway">Norway</option>
                                    <option value="Oman">Oman</option>
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="Palau Island">Palau Island</option>
                                    <option value="Palestine">Palestine</option>
                                    <option value="Panama">Panama</option>
                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Peru">Peru</option>
                                    <option value="Phillipines">Philippines</option>
                                    <option value="Pitcairn Island">Pitcairn Island</option>
                                    <option value="Poland">Poland</option>
                                    <option value="Portugal">Portugal</option>
                                    <option value="Puerto Rico">Puerto Rico</option>
                                    <option value="Qatar">Qatar</option>
                                    <option value="Republic of Montenegro">Republic of Montenegro</option>
                                    <option value="Republic of Serbia">Republic of Serbia</option>
                                    <option value="Reunion">Reunion</option>
                                    <option value="Romania">Romania</option>
                                    <option value="Russia">Russia</option>
                                    <option value="Rwanda">Rwanda</option>
                                    <option value="St Barthelemy">St Barthelemy</option>
                                    <option value="St Eustatius">St Eustatius</option>
                                    <option value="St Helena">St Helena</option>
                                    <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                    <option value="St Lucia">St Lucia</option>
                                    <option value="St Maarten">St Maarten</option>
                                    <option value="St Pierre & Miquelon">St Pierre &amp; Miquelon</option>
                                    <option value="St Vincent & Grenadines">St Vincent &amp; Grenadines</option>
                                    <option value="Saipan">Saipan</option>
                                    <option value="Samoa">Samoa</option>
                                    <option value="Samoa American">Samoa American</option>
                                    <option value="San Marino">San Marino</option>
                                    <option value="Sao Tome & Principe">Sao Tome &amp; Principe</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="Senegal">Senegal</option>
                                    <option value="Serbia">Serbia</option>
                                    <option value="Seychelles">Seychelles</option>
                                    <option value="Sierra Leone">Sierra Leone</option>
                                    <option value="Singapore">Singapore</option>
                                    <option value="Slovakia">Slovakia</option>
                                    <option value="Slovenia">Slovenia</option>
                                    <option value="Solomon Islands">Solomon Islands</option>
                                    <option value="Somalia">Somalia</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="Spain">Spain</option>
                                    <option value="Sri Lanka">Sri Lanka</option>
                                    <option value="Sudan">Sudan</option>
                                    <option value="Suriname">Suriname</option>
                                    <option value="Swaziland">Swaziland</option>
                                    <option value="Sweden">Sweden</option>
                                    <option value="Switzerland">Switzerland</option>
                                    <option value="Syria">Syria</option>
                                    <option value="Tahiti">Tahiti</option>
                                    <option value="Taiwan">Taiwan</option>
                                    <option value="Tajikistan">Tajikistan</option>
                                    <option value="Tanzania">Tanzania</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Togo">Togo</option>
                                    <option value="Tokelau">Tokelau</option>
                                    <option value="Tonga">Tonga</option>
                                    <option value="Trinidad & Tobago">Trinidad &amp; Tobago</option>
                                    <option value="Tunisia">Tunisia</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Turkmenistan">Turkmenistan</option>
                                    <option value="Turks & Caicos Is">Turks &amp; Caicos Is</option>
                                    <option value="Tuvalu">Tuvalu</option>
                                    <option value="Uganda">Uganda</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Erimates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States of America">United States of America</option>
                                    <option value="Uraguay">Uruguay</option>
                                    <option value="Uzbekistan">Uzbekistan</option>
                                    <option value="Vanuatu">Vanuatu</option>
                                    <option value="Vatican City State">Vatican City State</option>
                                    <option value="Venezuela">Venezuela</option>
                                    <option value="Vietnam">Vietnam</option>
                                    <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                    <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                    <option value="Wake Island">Wake Island</option>
                                    <option value="Wallis & Futana Is">Wallis &amp; Futana Is</option>
                                    <option value="Yemen">Yemen</option>
                                    <option value="Zaire">Zaire</option>
                                    <option value="Zambia">Zambia</option>
                                    <option value="Zimbabwe">Zimbabwe</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">State/Province</label>
                                <input id="province" type="text" class="form-control" name="province" maxlength="100" value="<?php echo $user['province'] ?>" />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">City</label>
                                <input id="city" type="text" class="form-control" name="city" maxlength="100" value="<?php echo $user['city'] ?>" />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <input id="btn-save-account" type="submit" class="btn btn-success" name="account" value="Save" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane <?php if ($tab === 'profile') echo 'active' ?>" id="profile">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <form id="profileForm" method="post" data-toggle="validator" role="form">
                            <?php if ($profileUpdated) { ?>
                            <h4 class="text-success"><?php echo $messageProfile ?></h4>
                            <?php } else if (!empty($messageProfile)) { ?>
                            <h4 class="text-danger"><?php echo $messageProfile ?></h4>
                            <?php } ?>
                            <div class="form-group">
                                <label class="control-label">Gender</label><br />
                                <label class="radio-inline">
                                  <input type="radio" name="gender" value="male" <?php if ($gender === 'male') echo 'checked' ?>> Male
                                </label>
                                <label class="radio-inline">
                                  <input type="radio" name="gender" value="female" <?php if ($gender === 'female') echo 'checked' ?>> Female
                                </label>
                                <label class="radio-inline">
                                  <input type="radio" name="gender" value="other" <?php if ($gender === 'other') echo 'checked' ?>> Other
                                </label>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Age Group</label><br />
                                <?php foreach ($ageranges as $key => $range) { ?>
                                <label class="radio-inline">
                                  <input type="radio" name="age" value="<?php echo $key ?>" <?php if ($key === $agerange) echo 'checked' ?>> <?php echo $range['text'] ?>
                                </label>
                                <?php } ?>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Occupation</label>
                                <input type="text" class="form-control" name="occupation" maxlength="50" value="<?php echo $occupation ?>" />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Preferred Device</label>
                                <input type="text" class="form-control" placeholder="PC, Mac, Mobile, etc..." name="device" maxlength="20" value="<?php echo $device ?>" />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <input id="btn-save-profile" type="submit" class="btn btn-success" name="profile" value="Save" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane <?php if ($tab === 'password') echo 'active' ?>" id="change-password">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <form id="passwordForm" method="post" action="account.php" data-toggle="validator" role="form" novalidate="false">
                            <?php if ($passwordUpdated) { ?>
                            <h4 class="text-success"><?php echo $messagePassword ?></h4>
                            <?php } else if (!empty($messagePassword)) { ?>
                            <h4 class="text-danger"><?php echo $messagePassword ?></h4>
                            <?php } ?>
                            <div class="form-group">
                                <label class="control-label">Old Password</label>
                                <input id="old-password" type="password" class="form-control" name="old-password" data-minlength="5" maxlength="20" required />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">New Password</label>
                                <input id="new-password" type="password" class="form-control" name="new-password" data-minlength="5" maxlength="20" required />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control" name="password-confirm" data-minlength="5" maxlength="20" data-match="#new-password" required />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <input id="btn-save-password" type="submit" class="btn btn-success" name="password" value="Save" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script src="js/validator.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.validator.Constructor.INPUT_SELECTOR = ':input:not([type="submit"], button):enabled';
            $('#country').val('<?php echo $user['country'] ?>');
        });
    </script>
  </body>
</html>
