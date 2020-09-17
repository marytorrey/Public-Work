
<?php
$eventID = $_GET['eventID'];
$eventTime = $_GET['eventTime'];
$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
mysql_select_db("fma_1", $con);
$query = "SELECT * FROM events WHERE ID = $eventID";
$result = mysql_query($query) or die(mysql_error());
while($row2 = mysql_fetch_assoc($result)){
	$eventName = $row2['name'];
	$eventDate = $row2['date'];
	if($eventTime == 1){
		$eventStartTime  = $row2['start_time'];
		$eventEndTime = $row2['end_time'];
	}
	elseif($eventTime == 2){
		$eventStartTime  = $row2['alt_start_time'];
		$eventEndTime = $row2['alt_end_time'];
	}
	$eventAddress = $row2['address'];
	$eventCity = $row2['city'];
	$eventState = $row2['state'];
	$eventZip = $row2['zip'];
	$eventPhone =  $row2['phone'];
	$eventMaxAttendees = $row2['max_attendees'];
}
	
	$timeStamp  = strtotime($eventStartTime) - 60*60;
	$eventRegTime = date('H:i A',  $timeStamp);
	$time = strtotime($eventDate);
	$eventDate = ($time === false) ? '0000-00-00' : date('F d, Y', $time);
	if ($eventID == '3'){
		$image = './php/resize.php?w=123&amp;h=123&amp;img=./images/scotchpines.jpg';
	}
	elseif($eventID == '2'){
		$image = './php/resize.php?w=220&amp;h=130&amp;img=./images/Allenmore.jpg';
	}

mysql_close($con);	
$MAX_GOLFERS = $eventMaxAttendees;
$MAX_TEAMS = $eventMaxAttendees/4;
	

	//Checks DB for any entry that has not been confirmed ie payment made
	
	// removes Empty Teams


	// IF Teams are full allows for signup for Newsletter
	if(isset($_POST['news_submit'])){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);	
		$email  = $_POST["news_email"];
		
		$sql="INSERT INTO pommo_subscribers(email, status) VALUES('$newsEmail', '1')";	
		mysql_query($sql) or die(mysql_error());
		mysql_close($con);
		header("Location: http://www.fmafoundation.org/newsLetterConfirm.php");
		
	}
	//final validation for Golfers 
	if(isset($_POST['Submit'])){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		
		$firstName = $_POST["first_name"];
		$lastName= $_POST["last_name"];
		$address = $_POST["address"];
		$city = $_POST["city"];
		$state = $_POST["state"];
		$zip = $_POST["zip"];
		$phone = $_POST["phone"];
		$email  = $_POST["email"];
		$optionTeam = $_POST["optionTeam"];
		$teamName = $_POST["teamName"];		
		
		if(checkDuplicateTeams(strtolower($teamName))&& (strlen($teamName) > 0) && ($teamName  != " ")){
			if(!checkDuplicateNames(strtolower($firstName), strtolower($lastName), $phone)){
				echo "<script language=javascript>alert('You have already registered for this event.)'</script>";
			}
			else{
				$sql="INSERT INTO Golfers (FIRST_NAME, LAST_NAME, ADDRESS, CITY, STATE, ZIP, PHONE, EMAIL, EVENT, EVENT_TIME) VALUES ('$firstName', '$lastName', '$address', '$city', '$state', '$zip', '$phone', '$email', '$eventID', '$eventTime')"; 
	
				mysql_query($sql) or die(mysql_error()); 
				
				$sql = "INSERT INTO Teams (TEAM_NAME, EVENT, EVENT_TIME) VALUES ('$teamName', '$eventID', '$eventTime')";
				mysql_query($sql) or die(mysql_error());
				$query = "SELECT * from Golfers WHERE FIRST_NAME LIKE '$firstName' AND LAST_NAME LIKE '$lastName'";
				$result  = mysql_query($query);
				while($row = mysql_fetch_assoc($result)){
					$playerId = $row['ID'];
				}
				session_start();
				$_SESSION['FMA_golferID'] = $playerId;
				$_SESSION['FMA_teamName'] = $teamName;
				$sql = "UPDATE Teams SET PLAYER_1 = '$playerId' WHERE TEAM_NAME = '$teamName'";
				mysql_query($sql) or die(mysql_error());
				echo '<META HTTP-EQUIV="Refresh" Content="0; URL=/golferConfirm.php?golferID='.$playerId.'">'; 
				
			}
		}
		elseif($optionTeam != NULL && $teamName  == NULL){
			if(!checkDuplicateNames(strtolower($firstName), strtolower($lastName), $phone)){
				echo "<script language=javascript>alert('You have already registered for this event.')</script>";
			}
			else{
				$sql = "INSERT INTO Golfers (FIRST_NAME, LAST_NAME, ADDRESS, CITY, STATE, ZIP, PHONE, EMAIL, EVENT) VALUES ('$firstName', '$lastName', '$address', '$city', '$state', '$zip', '$phone', '$email', '$eventID')";
				mysql_query($sql) or die(mysql_error());
			
				$query = "SELECT * FROM Golfers WHERE FIRST_NAME LIKE '$firstName' AND LAST_NAME LIKE '$lastName' AND PHONE LIKE '$phone'";
				$result  = mysql_query($query) or die(mysql_error());
			
				while($row = mysql_fetch_assoc($result)){
					$playerId = $row['ID'];
				}
				$_SESSION['FMA_golferID'] = $playerId;
				$_SESSION['FMA_teamName'] = $optionTeam;
				$query = "Select * FROM Teams WHERE TEAM_NAME = '$optionTeam'";
				$result  = mysql_query($query) or die(mysql_error());
			
			while($row = mysql_fetch_assoc($result)){
				$player1 = $row['PLAYER_1'];
				$player2 = $row['PLAYER_2'];
				$player3 = $row['PLAYER_3'];
				$player4 = $row['PLAYER_4'];
			}
			
				if($player1 == 0){
					$sql = "UPDATE Teams SET PLAYER_1 = '$playerId' WHERE TEAM_NAME = '$optionTeam'";
					mysql_query($sql) or die(mysql_error());
					echo '<META HTTP-EQUIV="Refresh" Content="0; URL=/golferConfirm.php?golferID='.$playerId.'">';
				}
				elseif($player2 == 0){
					$sql = "UPDATE Teams SET PLAYER_2 = '$playerId' WHERE TEAM_NAME = '$optionTeam'";
					mysql_query($sql) or die(mysql_error());
					echo '<META HTTP-EQUIV="Refresh" Content="0; URL=/golferConfirm.php?golferID='.$playerId.'">';
				}
				elseif($player3 == 0){
					$sql = "UPDATE Teams SET PLAYER_3 = '$playerId' WHERE TEAM_NAME = '$optionTeam'";
					mysql_query($sql) or die(mysql_error());
					echo '<META HTTP-EQUIV="Refresh" Content="0; URL=/golferConfirm.php?golferID='.$playerId.'">';
				}
				elseif($player4 == 0){
					$sql = "UPDATE Teams SET PLAYER_4 = '$playerId' WHERE TEAM_NAME = '$optionTeam'";
					mysql_query($sql) or die(mysql_error());					
					echo '<META HTTP-EQUIV="Refresh" Content="0; URL=/golferConfirm.php?golferID='.$playerId.'">';
				}
				else{
					echo  $optionTeam."  is full";
				}
			}					
		}
		elseif(($teamName == NULL) && ($optionTeam == "") || (!preg_match('/[A-Za-z]/', $teamName)) || (!preg_match('/[0-9]/', $teamName))){
			echo "<script language=javascript>alert('You must select a team or create a new one.')</script>";
		}
		elseif(!checkDuplicateTeams(strtolower($teamName))){
			echo "<script language=javascript>document.getElementById('#Error').innerHTML='".$teamName."has already been registered'</script>";
		}
		else {
			echo "<script language=javascript>document.getElementById('#Error').innerHTML='".$teamName."is not a valid name.'</script>";
		}
	}
	function getImage($boxID, $eventID)
	{
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		//$query = sprintf("SELECT * FROM Sponsor WHERE BOXID ='%s'", mysql_real_escape_string($boxID))  or die(mysql_error());
		$query = "SELECT * FROM Sponsor WHERE EVENT = $eventID";
		$result = mysql_query($query);
		$default = '"../images/BECOME A SPONSOR ICON.jpg"';
		while($row = mysql_fetch_assoc($result)){
			$companyName = $row['COMPANY_NAME'];
			$logoName = $row['LOGO_NAME'];
			$boxId  = $row['BOXID'];
			$boxId2 = $row['BOXID2'];
			$event = $row['EVENT'];
		}

		if (($boxID == $boxId) || ($boxID == $boxId2)){
			$logo_picture = '"./php/resize.php?w=152&amp;h=130&amp;img=./images/logos/'.$logoName.'" title="'.$companyName.'"';
			echo $logo_picture;
		}

		else {
			echo $default;
		}
		mysql_free_result($result);
		mysql_close($con);
	}
	function getURL($boxID, $eventID){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		//$query = sprintf("SELECT * FROM Sponsor WHERE BOXID ='%s'", mysql_real_escape_string($boxID))  or die(mysql_error());
		$query = "SELECT * FROM Sponsor WHERE EVENT = $eventID";
		$result = mysql_query($query);
		$default = '"../sponsor.php?boxId='.$boxID.'&eventID='.$eventID.'"';
		while($row = mysql_fetch_assoc($result)){
			$url = $row['URL'];
			$boxId  = $row['BOXID'];
			$boxId2 = $row['BOXID2'];
			$event = $row['EVENT'];
		}
		if (($boxID == $boxId) || ($boxID == $boxId2)){
			echo '"'.$url.'" target = "_blank"';
		}
		else{
			echo $default;
		}
		mysql_free_result($result);
		mysql_close($con);
		
	}
	function getOptions($eventID, $eventTime){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);	
		$query = "SELECT * FROM Teams WHERE EVENT = $eventID AND EVENT_TIME = $eventTime";
		$result  = mysql_query($query);
		while($row = mysql_fetch_assoc($result)){
			$teamNames = $row['TEAM_NAME'];
			$player1 = $row['PLAYER_1'];
			$player2 = $row['PLAYER_2'];
			$player3 = $row['PLAYER_3'];
			$player4 = $row['PLAYER_4'];			
			if($player1 == 0 || $player2 == 0 || $player3 == 0 || $player4 == 0){
				echo '<option value="'.$teamNames.'">'.$teamNames.'</option>' ;
			}
		}
		mysql_close($con);
	}
	function getTeamNames($eventID){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		$query = "SELECT * FROM Teams WHERE EVENT = $eventID";
		$result  = mysql_query($query);
		$count = 1;
		for($i = 1; $i <= 5; $i++){
			echo '<div class="teamNames"> ';
			for($j = 1; $j <= 10; $j++){
				$row = mysql_fetch_assoc($result);
				$teamNames = $row['TEAM_NAME'];
				$player1 = $row['PLAYER_1'];
				$player2 = $row['PLAYER_2'];
				$player3 = $row['PLAYER_3'];
				$player4 = $row['PLAYER_4'];
				if($player1 > 0 && $player2 > 0 && $player3>0 && $player4 > 0){
					
					$query2 = "SELECT * FROM Golfers WHERE ID LIKE $player1";
					$result2  = mysql_query($query2);
					while($row = mysql_fetch_assoc($result2)){
						$firstName1 = $row['FIRST_NAME'];
						$lastName1  = $row['LAST_NAME'];
					}
					$query3 = "SELECT * FROM Golfers WHERE ID LIKE $player2";
					$result3  = mysql_query($query3);
					while($row = mysql_fetch_assoc($result3)){
						$firstName2 = $row['FIRST_NAME'];
						$lastName2  = $row['LAST_NAME'];
					}
					$query4 = "SELECT * FROM Golfers WHERE ID LIKE $player3";
					$result4  = mysql_query($query4);
					while($row = mysql_fetch_assoc($result4)){
						$firstName3 = $row['FIRST_NAME'];
						$lastName3  = $row['LAST_NAME'];
					}
					$query5 = "SELECT * FROM Golfers WHERE ID LIKE $player4";
					$result5  = mysql_query($query5);
					while($row = mysql_fetch_assoc($result5)){
						$firstName4 = $row['FIRST_NAME'];
						$lastName4  = $row['LAST_NAME'];
					}	
					$file = "./teams/team".$count.".html";
					$fh = fopen($file, 'w') or die("can't open file");
					$html = "<html>\n<body>\n<h4>Registerd Players</h4>\n<p>".$firstName1." ".$lastName1."<br />".$firstName2." ".$lastName2."<br />".$firstName3." ".$lastName3."<br />".$firstName4." ".$lastName4."<br />\n</p></body>\n</html>";
					fwrite($fh, $html);
					fclose($fh);
					echo '<a href="/teams/team'.$count.'.html?width=250" id="'.$count.'" name="'.$teamNames.'" class="jTip teamNamesBottom">'.$teamNames.'</a><br />';
					$count += 1;
				}			
			}
			echo '</div>';		
		}
		
		mysql_close($con);
	}
	function getPlayersRemaining($eventID, $eventTime){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		$query = "SELECT * FROM Golfers WHERE EVENT = $eventID AND EVENT_TIME = $eventTime";
		$result  = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		global $MAX_GOLFERS;
		if($eventID == 2){
			echo 100-$num_rows;
		}
		else{
			echo $MAX_GOLFERS - $num_rows;
		}
		
		mysql_close($con);	
	}
	
	function getButton(){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		global $MAX_TEAMS;
		$query = "SELECT * FROM Teams";
		$result  = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		if($num_rows < $MAX_TEAMS){
			echo '<button onclick="toggleForm(event)">New</button>';
		}
		mysql_close($con);	
	}
	function getDiscription($eventID){
		if($eventID == '3'){
			echo '<p>The Scotch Pines Golf Course has been kind enough to offer  the use of their course for a fun packed day of golf to support the FMA Foundation  in raising money for the Terminally Ill. The proceeds of this event will help  people in the treasure valley that have a terminal disease as well as their families.  The player cost will be $125 per player and will include golfer goodie bags,  lunch, dinner, cart rental, green fees, shirt and much more.</p>
			<p style="text-align:left;">For course info and layout visit <a href="http://scotchpinesgolf.com/">Scotch Pines Website</a>.  For Contest rules please visit <a href="#">this page.</a></p>';
		}
		elseif($eventID == '2'){
			echo 'new Discription for '.$eventID;
		}
	}
	function readyForm(){
		$eventID = $_GET['eventID'];
		$eventTime = $_GET['eventTime'];
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);
		$query = "SELECT * FROM Golfers";
		$result  = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		global $MAX_GOLFERS;
		$numberPlayers = $num_rows;
		
		mysql_close($con);
		
		if($numberPlayers < $MAX_GOLFERS){
			echo '<form action="golf1.php?eventID='.$eventID.'&eventTime='.$eventTime.'" method="post" id="commentForm" autocomplete="off">
                        <input type="hidden" name="Language" value="English">
						<table>
						    <tr>
                            	<td>
                                <label for="team">Team Name:</label>
                                </td>
                                <td>
                                	<select style="width:205px;" name="optionTeam" class="team"> 
                                    	<option value="">Please Select Your Team
                                        </option>';
                                         getOptions($eventID, $eventTime);
                                    echo '</select>
                                </td>
                                <td>';
                                getButton();
                                echo'</td>
                                <td></td>
                            </tr>
                            <tr id="newTeam" class="HidingWrapper">
                            <td>
                            New Team Name:
                            </td>
                            <td>
                            <input  type="text" name="teamName"  maxlength="50" size="30" value="" id="teamName" onblur="return check_userName();" class="team">
                            </td>
							<td id="msgbox" style="display:none"></td> 
                            </tr>
                        	<tr>
                            	<td>
                                <label for="first_name">First Name:</label>
                                </td>
                                <td>
                                <input  type="text" name="first_name" maxlength="50" size="30" class="required noSpecialChars" value="">
                                </td>
                                <td id="Error2" style="color:red;"></td>
                            </tr>
                            <tr>
                            	<td>
                                <label for="last_name">Last Name:</label>
                                </td>
                                <td>
                                <input  type="text" name="last_name" maxlength="50" size="30" class="required noSpecialChars" value="">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                            	<td>
                                <label for="street">Street Address:</label>
                                </td>
                                <td>
                                <input  type="text" name="address" maxlength="50" size="30" class="required noSpecialChars" value="">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                            	<td>
                                <label for="city">City:</label>
                                </td>
                                <td>
                                <input  type="text" name="city" maxlength="50" size="30" class="required" value="">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                            	<td>
                                	<label for="state">State:</label>
                                </td>
                                <td>
                                	<select name="state" class="required">
                                    <option value="">Please Select</option>';
                                    	
$default = $state;
$states = array('AL'=>"Alabama",  
            'Alaska'=>"Alaska",  
            'Arizona'=>"Arizona",  
            'Arkansas'=>"Arkansas",  
            'California'=>"California",  
            'Colorado'=>"Colorado",  
            'Connecticut'=>"Connecticut",  
            'Delaware'=>"Delaware",  
            'District Of Columbia'=>"District Of Columbia",  
            'Florida'=>"Florida",  
            'Georgia'=>"Georgia",  
            'Hawaii'=>"Hawaii",  
            'Idaho'=>"Idaho",  
            'Illinois'=>"Illinois",  
            'Indiana'=>"Indiana",  
            'Iowa'=>"Iowa",  
            'Kansas'=>"Kansas",  
            'Kentucky'=>"Kentucky",  
            'Louisiana'=>"Louisiana",  
            'Maine'=>"Maine",  
            'Maryland'=>"Maryland",  
            'Massachusetts'=>"Massachusetts",  
            'Michigan'=>"Michigan",  
            'Minnesota'=>"Minnesota",  
            'Mississippi'=>"Mississippi",  
            'Missouri'=>"Missouri",  
            'Montana'=>"Montana",
            'Nebraska'=>"Nebraska",
            'Nevada'=>"Nevada",
            'New Hampshire'=>"New Hampshire",
            'New Jersey'=>"New Jersey",
            'New Mexico'=>"New Mexico",
            'New York'=>"New York",
            'North Carolina'=>"North Carolina",
            'North Dakota'=>"North Dakota",
            'Ohio'=>"Ohio",  
            'Oklahoma'=>"Oklahoma",  
            'Oregon'=>"Oregon",  
            'Pennsylvania'=>"Pennsylvania",  
            'Rhode Island'=>"Rhode Island",  
            'South Carolina'=>"South Carolina",  
            'South Dakota'=>"South Dakota",
            'Tennessee'=>"Tennessee",  
            'Texas'=>"Texas",  
            'Utah'=>"Utah",  
            'Vermont'=>"Vermont",  
            'Virginia'=>"Virginia",  
            'Washington'=>"Washington",  
            'West Virginia'=>"West Virginia",  
            'Wisconsin'=>"Wisconsin",  
            'Wyoming'=>"Wyoming");

foreach($states as $key=>$val) {
    echo ($key == $default) ? "<option selected=\"selected\" value=\"$key\">$val</option>":"<option value=\"$key\">$val</option>";
}

                                    echo '</select>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                            	<td>
                                	<label for="zip">Zip Code:</label>
                                </td>
                                <td>
                                	<input  type="text" name="zip" maxlength="5" size="30" class="required zip" value="">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                            	<td>
                                	<label for="phone">Phone Number:</label>
                                </td>
                                <td>
                                	<input  type="text" name="phone" class="required phoneUS" id="phone" maxlength="50" size="30" value="">
                                </td>
                                <td id="msgbox2"></td>
                            </tr>
                            <tr>
                            	<td>
                                	<label for="email">Email Address:</label>
                                </td>
                                <td>
                                	<input  type="text" name="email" class="required email" maxlength="50" size="30" value="">
                                </td>
                                <td></td>
                            </tr>
                        
                            <tr>
                            	<td>
                                </td>
                                <td><input type="submit" value="Submit" name="Submit" class="button"></td>
                            </tr>
                        </table>
                        </form>';
		}
		else{
			echo '<p style="margin-top:50px; margin-left:20px;">We'.'re sorry, our golfer sign up is full. If you would like to get a jumpstart on next years golf event or would like information on future events please sign up below.</p>
			<h4 style="margin-left:20px;">NEWSLETTER SIGN UP</h4>
			<form action = "golf1.php" id="commentForm" method="post">
			<table>	
				<tr>
					<td>
                    	<label for="email">Email Address:</label>
                    </td>
                    <td>
                    	<input  type="text" name="news_email" class="required email" maxlength="50" size="30">
                   	</td>
					<td></td>
				</tr>
				<tr>
                	<td>
                	</td>
               		<td><input type="submit" value="sign up" name="news_submit" class="button"></td>
            	</tr>
			</table>
			</form><br />
			<p style="margin-left:20px;">Please note: We do not sell, trade or publish your email address, and you can unsubscribe at any time.</p>';
		}
	}
	function checkDuplicateNames($firstName, $lastName, $phone){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);	
		
		$query = "SELECT * from Golfers";
		$result = mysql_query($query) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)){
			$golferFirstName = $row['FIRST_NAME'];
			$golferLastName = $row['LAST_NAME'];
			$golferphone = $row['PHONE'];
			if(strtolower($golferFirstName) == $firstName && strtolower($golferLastName) == $lastName && $golferphone == $phone){
				return false;
			}
		}
		return true;
		mysql_close($con);
	}
	function checkDuplicateTeams($teamName){
		$con = mysql_connect('gtorreyjr69.globatmysql.com', 'gtorreyjr', 'tower69') or die(mysql_error());
		mysql_select_db("fma_1", $con);	
		$query = "SELECT * FROM Teams";
		$result = mysql_query($query) or die(mysql_error);
		while($row = mysql_fetch_array($result)){
			$team = $row['TEAM_NAME'];
			if(strtolower($team) == $teamName){
				return false;
			}
		}
		return true;
		mysql_close($con);
	}
	include 'header.php';
?>
        
        <div class="headingbuffer">
        </div>
    </header>
    
	<!--==============================content================================-->
    <script type="text/javascript">
	
		var validator;
		
		$(document).ready(function(){
			jQuery.validator.addMethod(
				"phoneUS",
				function(phone_number, element) {
					phone_number = phone_number.replace(/\s+/g, ""); 

					return this.optional(element) || phone_number.length > 9 && phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
				}, "Please specify a valid phone number"
			);
			jQuery.validator.addMethod(
				"zip",
				function(zip, element) {

					return this.optional(element) || zip.length > 4 && zip.match('^(0|[1-9][0-9]*)$');
				}, "Please enter a valid zip code."
			);

			jQuery.validator.addMethod(
				"noSpecialChars",
				function(field_value, element) {
					field_value = field_value.replace(/\s+/g, ""); 

					return this.optional(element) || !field_value.match(/[^A-Za-z0-9#.]/);
				}, "No special letters"
			);
			jQuery.validator.addMethod(
				"team",
				function(optionTeam, teamName, element){
					optionTeam = optionTeam;
					teamName = teamName;
					if(optionTeam == "" && teamName == ""){
						return this.optional(element) || optionTeam == "" && teamName == "";
					}
				},"You must choose a Team."
			);

			$("#commentForm").bind(
				"invalid-form.validate", 
				function() {
					console.error("OH NOES!!!  YOU HAVE " + validator.numberOfInvalids() + " ERRORS!");
				}
			);
		
			validator = $("#commentForm").validate({
				debug: false,
				errorElement: "em",
				errorContainer: $("#summary"),
				errorPlacement: function(error, element) {
					error.appendTo( element.parent("td").next("td") );
				}, success: function(label) {
					label.text("ok!").addClass("success");
				}, rules: {
					company_name: {
						required:true,
						minlength:3,
						maxlength:25 
					}
				}
			});
			
			$("input").bind("keydown", function(){
				$(this).parent("td").next("td").empty();	
			});
			
			$("#phone").mask("(999) 999-9999");
			
			$('input[autocomplete]').removeAttr('autocomplete');
			
			$("#teamName").blur(function()
{
 //remove all the class add the messagebox classes and start fading
 $("#msgbox").removeClass().text('Checking...').fadeIn("slow");
 //check the username exists or not from ajax
 $.post("teamNameCheck.php",{ teamName:$(this).val() } ,function(data)
 {
  if(data=='no') //if username not avaiable
  {
   $("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
   {
    //add message and change the class of the box and start fading
    $(this).html('This Team name Already exists').addClass('error').fadeTo(900,1);
   //$(this).html(data).addClass('messageboxerror').fadeTo(900,1);
   });
  }
  else
  {
   $("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
   {
    //add message and change the class of the box and start fading
    $(this).html('Team Name available to register').addClass('success').fadeTo(900,1);
	//$(this).html(data).addClass('messageboxerror').fadeTo(900,1);
   });
  }
 });
});
		
		});
 
function finishAjax(id, response){
 
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn(1000);
}
					
		function toggleForm(e)
		{
   			e.preventDefault();
    		$('#newTeam').slideToggle('slow');
			var input = document.getElementById("teamName");
			input.value = "";
		}
</script>
     <div class="contentBody">
        <div class="GolfContentCenter">
<!--==============================Top Sponsor Boxes Picture size no bigger than ~~ Height : 130px    Width:152px================================-->        
        	<div class="sponsorTop">
				<div class="sponsorBox2">
                	<div class="sponsorHeader2">
                    	<h4 style="color:#000; height:20px;">	Ace
                        </h4>
                        <a href=<?php getURL('ace', $eventID)?> class="sponsorPictureBox2" id="Ace"><img src=<?php getImage('ace', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Eagle
                        </h4>
                        <a href=<?php getURL('eagle', $eventID)?> class="sponsorPictureBox2" id="eagle"><img src=<?php getImage('eagle', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Birdie
                        </h4>
                        <a href=<?php getURL('birdie', $eventID)?> class="sponsorPictureBox2" id="birdie"><img src=<?php getImage('birdie', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Awards & Dinner
                        </h4>
                        <a href=<?php getURL('awards', $eventID)?> class="sponsorPictureBox2" id="awards"><img src=<?php getImage('awards', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Shirt Sponsor
                        </h4>
                        <a href=<?php getURL('shirt', $eventID)?> class="sponsorPictureBox2" id="shirt"><img src=<?php getImage('shirt', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Sponsor
                        </h4>
                        <a href="#" class="sponsorPictureBox2" id="example"><img src=<?php getImage('example', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
             </div>             
            <div class="middleContainer">
<!--==============================Left Side Sponsor boxes Picture sizes no bigger than ~~Height: 112px Width:200px================================-->
            	<div class="sponsorLeft sideBySide">
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 1 Sponsor
                       	  </h4>
                            <a href=<?php getURL('hole1', $eventID)?> class="sponsorPictureBox2" id="hole1"><img src=<?php getImage('hole1', $eventID)?> height="130" width="152" /></a>	
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 2 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole2', $eventID)?> class="sponsorPictureBox2" id="hole2"><img src=<?php getImage('hole2', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 3 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole3', $eventID)?> class="sponsorPictureBox2" id="hole3"><img src=<?php getImage('hole3', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 4 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole4', $eventID)?> class="sponsorPictureBox2" id="hole4"><img src=<?php getImage('hole4', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 5 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole5', $eventID)?> class="sponsorPictureBox2" id="hole5"><img src=<?php getImage('hole5', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 6 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole6', $eventID)?> class="sponsorPictureBox2" id="hole6"><img src=<?php getImage('hole6', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 7 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole7', $eventID)?> class="sponsorPictureBox2" id="hole7"><img src=<?php getImage('hole7', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 8 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole8', $eventID)?> class="sponsorPictureBox2" id="hole8"><img src=<?php getImage('hole8', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 9 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole9', $eventID)?> class="sponsorPictureBox2" id="hole9"><img src=<?php getImage('hole9', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
            	</div>
<!--==============================Middle Content Area================================-->
                <div class="sponsorMiddle sideBySide">
                	<div class="info">
                    	<h3 align="center">inaugural FMA Foundation Golf Classic
                        </h3>
                    	<h4 style="text-align:center;">Hosted by <?php echo $eventName?> Golf Course</h4>
                      <span><center><img src="<?php echo $image?>" alt="" align="middle"></center></span>
                        <?php getDiscription($eventID);?>
                      
                      <h4>Location :</h4> 
                      <p><?php echo $eventAddress?>, <?php echo $eventCity?>, <?php echo $eventState?> <?php echo $eventZip ?> </p>
                        <h4>Tournament Date and Time : </h4><?php echo $eventDate?><br />
                        <p>Time : <?php echo $eventRegTime?> - <?php echo $eventEndTime?><br />
                        Shotgun start : <?php echo $eventStartTime?><br />
                        Dinner and Awards Ceremony: <?php echo $eventEndTime?></p>                      
                        <h4>Format :</h4> 
                        <p>4 Person Scramble</p>
                    
                    	<h4> Events :
                        </h4>
                        <p>Raffle: $2.00 Ticket <br />
                        Auction: Silent auction </p>
                    
					
                    <h4>
                    	Contests:
                    </h4>
                    <div class="left" style="width:40%;">
                    	 <b style="text-decoration:underline">1st place :</b><br>
                        <b style="text-decoration:underline">2nd place :</b><br>
                        <b style="text-decoration:underline">3rd place :</b><br>
                      <b style="text-decoration:underline">4th place :</b></p>
                    </div>
                    <div class="right" style="width:60%">
                    	<b style="text-decoration:underline">Longest Putt Contest: </b><br>
                        <b style="text-decoration:underline">Closest to the Pin Contest:</b><br>
                        <b style="text-decoration:underline">Longest Drive Contest:</b><br>
             		<b style="text-decoration:underline">Hole in One: </b><br>
                    </div>
                                      
                    <span style="float:left; display:block; clear:both;">
                    	The Awards/contest/raffle/auction winners will be handed out at the dinner. 
                        </span>
                    </div>
<!--==============================Golfer Form area================================-->
                    <div class="form">
                    	<h3 style="margin-bottom:5px;">
                        	<span style="float:left;">Player Sign Up</span>
                            <span style="float:right; display:inline-block; margin-right:10px;"> Spots Remaining : <span style="color:#F00;"><?php getPlayersRemaining($eventID, $eventTime) ?></span></span>
                        </h3>
                    	<?php readyForm()?>  
                    </div>
                    <div style="margin-left:auto; margin-right:auto;">
                		<h4>Full Teams</h4>
						<?php getTeamNames($eventID) ?>
                	</div>
                </div>
<!--==============================Right Sponsor Boxes Picture sizes no bigger than ~~Height: 112px Width:200px================================-->
                <div class="sponsorRight sideBySide">
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 10 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole10', $eventID)?> class="sponsorPictureBox2" id="hole10"><img src=<?php getImage('hole10', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 11 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole11', $eventID)?> class="sponsorPictureBox2" id="hole11"><img src=<?php getImage('hole11', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 12 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole12', $eventID)?> class="sponsorPictureBox2" id="hole12"><img src=<?php getImage('hole12', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 13 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole13', $eventID)?> class="sponsorPictureBox2" id="hole13"><img src=<?php getImage('hole13', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 14 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole14', $eventID)?> class="sponsorPictureBox2" id="hole14"><img src=<?php getImage('hole14', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole In One Sponsor
                        	</h4>
                            <a href="#" class="sponsorPictureBox2" id="hole15"><img src=<?php getImage('hole15', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 16 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole16', $eventID)?> class="sponsorPictureBox2" id="hole16"><img src=<?php getImage('hole16', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 17 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole17', $eventID)?> class="sponsorPictureBox2" id="hole17"><img src=<?php getImage('hole17', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>
                    <div class="sponsorBox">
                    	<div class="sponsorHeader mediaText">
                        	<h4 style="color:#000; height:20px;">	Hole 18 Sponsor
                        	</h4>
                            <a href=<?php getURL('hole18', $eventID)?> class="sponsorPictureBox2" id="hole18"><img src=<?php getImage('hole18', $eventID)?> height="130" width="152" /></a>
                        </div>
                    </div>               
                </div>
            </div>
<!--==============================Bottom Sponsor Boxes Picture size no bigger than ~~ Height : 130px    Width:152px================================-->
            <div class="sponsorBottom">
				<div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Beverage Cart
                        </h4>
                        <a href=<?php getURL('beverage', $eventID)?> class="sponsorPictureBox2" id="beverage"><img src=<?php getImage('beverage', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Putting Green
                        </h4>
                        <a href=<?php getURL('green', $eventID)?> class="sponsorPictureBox2" id="green"><img src=<?php getImage('green', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Driving Range
                        </h4>
                        <a href=<?php getURL('driving', $eventID)?> class="sponsorPictureBox2" id="driving"><img src=<?php getImage('driving', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Longest Putt
                        </h4>
                        <a href=<?php getURL('longest', $eventID)?> class="sponsorPictureBox2" id="longest"><img src=<?php getImage('longest', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Closest to the pin
                        </h4>
                        <a href=<?php getURL('closest', $eventID)?> class="sponsorPictureBox2" id="closest"><img src=<?php getImage('closest', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
                <div class="sponsorBox2">
                	<div class="sponsorHeader2 mediaText">
                    	<h4 style="color:#000; height:20px;">	Longest Drive
                        </h4>
                        <a href=<?php getURL('drive', $eventID)?> class="sponsorPictureBox2" id="drive"><img src=<?php getImage('drive', $eventID)?> height="130" width="152" /></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
	include 'footer.php';
?>