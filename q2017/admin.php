<?php
    error_reporting(E_ERROR | E_WARNING | E_PARSE); //doesn't report small errors
    $url = $_GET['id']; //get admin's unique hash
    if(($url != NULL) and ($url != "")){ //if the url has the admin's unique hash
        $db = new SQLite3('quotations2017.sqlite3'); //connect
        //get entire row 
        $statement = $db -> prepare('SELECT * FROM admin WHERE url = :url;'); 
        $statement -> bindValue(':url', $url);
        $result = $statement->execute();
        //get name
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $name = $row['name']; 
        }
        if(($name != "") and isset($name)){ //if the hash is found
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Admin</title>
        <script type="text/javascript">
            function loginAdmin() {
                document.getElementById("container").innerHTML = ""; //delete login form
            }
        </script>
        <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css"> <!--W3.CSS stylesheet-->
        <style>
            /*
                Radio button style from: http://stackoverflow.com/questions/16242980/making-radio-buttons-look-like-buttons-instead
            */
            .radioButtons {
                 list-style-type:none;
                 margin:25px 0 0 0;
                 padding:0;
            }

            .radioButtons li {
                 float:left;
                 margin:0 5px 0 0;
                width:100px;
                height:40px;
                position:relative;
            }

            .radioButtons label, .radioButtons input {
                display:block;
                position:absolute;
                top:0;
                left:0;
                right:0;
                bottom:0;
            }

            .radioButtons input[type="radio"] {
                opacity:0.01;
                z-index:100;
            }

            .radioButtons input[type="radio"]:checked + label,
            .Checked + label {
                background:#39B7CD;
            }

            .radioButtons label {
                 padding:5px;
                 border:1px solid #CCC; 
                 cursor:pointer;
                z-index:90;
            }

            .radioButtons label:hover {
                 background:#DDD;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <!--W3.CSS Login page example template-->
            <header class="w3-container w3-blue">
                <h1>Please Login</h1>
            </header>

            <div class="w3-container w3-half w3-margin-top">
                <form class="w3-container w3-card-4" name="auth" method="post">
                  <h2 class="w3-text-theme">Login</h2>
                  <div class="w3-group">      
                    <input class="w3-input" type="text" name="email" required>
                    <label class="w3-label">Email</label>
                  </div>
                  <br><br>
                  <input type="submit" name="submit" value="Log in" class="w3-btn w3-theme">
                  <br><br>
                </form>
                <!--End W3.CSS Login page example template-->
            </div>
        </div>
        <?php
            if(isset($_POST['email']) and $_POST['email'] != ""){ //if admin entered email
                $email = $_POST['email']; //get email entered
                //get the true email from the database
                while($row = $result->fetchArray(SQLITE3_ASSOC)){
                    $dbEmail = $row['email']; 
                }

                //get whether or not anyone else is logged in 
                $statement2 = $db -> prepare('SELECT * FROM admin'); 
                $result2 = $statement2->execute();

                //create an array for all of the isLoggedIn column values
                $isLoggedIn[] = [];
                while($row = $result2->fetchArray(SQLITE3_ASSOC)){
                    array_push($isLoggedIn, $row['isLoggedIn']); //set the values in to the array
                }

                //check to make sure everything is ok
                if($email == $dbEmail){ //if emails match
                    if(sizeof(array_keys($isLoggedIn, 1)) > 0){ //if someone is logged in
                        //get name of person logged in
                        $statement0 = $db -> prepare('SELECT name FROM admin WHERE isLoggedIn = 1;'); 
                        $result0 = $statement0->execute();
                        //set name 
                        while($row = $result0->fetchArray(SQLITE3_ASSOC)){
                            $loggedInName = $row['name']; 
                        }
                        echo "$loggedInName is logged in right now, please wait a moment."; //tell user someone is logged in
                    }
                    else{ //if someone is not logged in already
                        //put that you are logged in into the db
                        $statement3 = $db -> prepare('UPDATE admin SET isLoggedIn = 1 WHERE url = :url'); 
                        $statement3 -> bindValue(':url', $url);
                        $result3 = $statement3->execute();
                    
        ?>
        <script>
            loginAdmin(); //call function to change page once admin logs in successfully
        </script>

        <!--HTML to disaplay if admin logs in succussfully-->
        <div id="container2">
            <header class="w3-container w3-blue" style="padding-top: 10px;">
                <!--Log out button-->
                <form name="logout" action="logOut.php" method="post" style="float: left; padding-right: 20px;">
                    <input type="hidden" name="URL" value=<?php echo "\"$url\""; //send the url?>>
                    <input type="submit" name="logOutSubmit" value="Log Out" class="w3-btn">
                </form> 
                <!--Title-->
                <h1 style="float: left; margin-top: -15px;">Senior Quotations</h1>
                <!--Name-->
                <h1 style="float: right; margin-top: -15px;"><?php echo "Signed in as: $name"?></h1>
            </header>

            <!--
            Form for all of the quotation approvals
            Must go to a separate handler
            The number of entrie quotations on this page will be set with via GET to the handler
            -->
            <form name="quotationApproval" method="post" action=<?php
                //get everything
                $statement4 = $db -> prepare('SELECT * FROM quotations;'); 
                $result4 = $statement4 -> execute();

                //create an array for all of the quotations
                $allQuotations[] = [];
                while($row = $result4->fetchArray(SQLITE3_ASSOC)){
                    array_push($allQuotations, $row['quotation']); //set the values in to the array
                }
                
                //count total number of quotations on page 
                $i = 0; 
                foreach($allQuotations as $eachQuotation){
                    if(($eachQuotation != "") and ($eachQuotation != "Array") and !(($isProcessedStudent == -1) or ($isProcessedStudent == -2) or ($isProcessedTeacher == -1) or ($isProcessedTeacher == -2)) and !((($isStudentAdmin == 1) and ($isProcessedStudent == 1)) or (($isStudentAdmin == 0) and ($isProcessedTeacher == 1)))){ //counts number of quotations ready to be approved (logic explained below)
                        $i = $i + 1; //increment number of quotations ready to be approved
                    }
                }
                $i2 = $i - 1; //subtract out the 1st quotations (which never gets displayed)
                echo "quotationHandler.php?i=$i2";?>>
            <?php  
                //show HTML ones that aren't null or ""
                $i = 0; //number of quotations to be displayed 
                foreach($allQuotations as $eachQuotation){
                    if(($eachQuotation != "") and ($eachQuotation != "Array")){ //if not "" of "Array"
                        //get name of person with quoation
                        $statement5 = $db -> prepare('SELECT * FROM quotations WHERE quotation = :eachQuotation;'); 
                        $statement5 -> bindValue(':eachQuotation', $eachQuotation);
                        $result5 = $statement5->execute();

                        //set name
                        while($row = $result5->fetchArray(SQLITE3_ASSOC)){
                            $studentFirstName = $row['firstName']; 
                        } 
                        while($row = $result5->fetchArray(SQLITE3_ASSOC)){
                            $studentLastName = $row['lastName']; 
                        } 

                        //set url
                        while($row = $result5->fetchArray(SQLITE3_ASSOC)){
                            $studentURL = $row['url']; 
                        } 

                        //get whether the student has been processed by another teacher or student admin
                        while($row = $result5->fetchArray(SQLITE3_ASSOC)){
                            $isProcessedStudent = $row['processedStudent']; 
                        }  

                        while($row = $result5->fetchArray(SQLITE3_ASSOC)){
                            $isProcessedTeacher= $row['processedTeacher']; 
                        }

                        //use the very 1st query to get whether the admin is a student or a teacher
                        while($row = $result->fetchArray(SQLITE3_ASSOC)){
                            $isStudentAdmin = $row['isStudent']; 
                        } 

                        if(($i != 0) and !(($isProcessedStudent == -1) or ($isProcessedStudent == -2) or ($isProcessedTeacher == -1) or ($isProcessedTeacher == -2)) and !((($isStudentAdmin == 1) and ($isProcessedStudent == 1)) or (($isStudentAdmin == 0) and ($isProcessedTeacher == 1)))){ 
                            /*
                            doesn't show:
                                the first quotation, which is always "Array"
                                quotations which have been disapproved already or have been approved already
                                quotations that have already been approved by a student admin if admin is a student
                                quotations that have already been apporved by a teacher admin if admin is a teacher 
                            */

                            //name of the radio button fields
                            //it is incremented so that each quotation has it's own set of radio buttons
                            $radioName = "radioSet$i";
                            $radioId = "radioId$i"; 
            ?> 
                <!--Show quotation-->
                <div class="w3-card" style="padding-top: 40px;">
                <header class="w3-container w3-teal">
                    <h3><?php echo "$studentFirstName $studentLastName's"?> Quotation</h3>
                </header>
                <div class="w3-container">
                     <?php 
                        echo "\"$eachQuotation\"";
                     ?>
                    <br>
                    
                    <!--Radio button appearance from: http://stackoverflow.com/questions/16242980/making-radio-buttons-look-like-buttons-instead-->
                    <div class="w3-container">
                        <ul class="radioButtons">
                        <!--Form stuff-->
                            <li>
                        <!--Approve:--> <input type="radio" id=<?php echo "\"approve$radioId\""?> name=<?php echo "\"$radioName\"";?> value="1">
                                <label for=<?php echo "\"approve$radioId\""?>>Approve</label>
                            </li>

                            <li>
                    <!--Disapprove:--> <input type="radio" id=<?php echo "\"disapprove$radioId\""?> name=<?php echo "\"$radioName\"";?> value="-1">
                                <label for=<?php echo "\"disapprove$radioId\""?>>Disapprove</label>
                            </li>

                            <li>
                    <!--Clear:--> <input type="radio" id=<?php echo "\"clear$radioId\""?> name=<?php echo "\"$radioName\"";?> value="0">
                                <label for="<?php echo "clear$radioId"?>">Clear</label>
                            </li>
                        </ul>

                        <textarea name=<?php echo "\"disapprovalReason$i\""?> placeholder="Disapproval Reason" rows="1" cols="40"></textarea>
                        <input type="hidden" name=<?php echo "\"studentURL$i\""?> value=<?php echo "\"$studentURL\"";?>> <!--Sends the URL of the student whose quotation is being looked at-->
                        <input type="hidden" name=<?php echo "\"isStudentAdmin$i\""?> value=<?php echo "\"$isStudentAdmin\""?>> <!--Send whether or not it's a student admin-->
                    </div>
                </div>
                </div>
            <?php
                        //close curly braces
                        }
                        $i = $i + 1; //increment number of
                    }
                }
            ?>
                <input type="hidden" name=<?php echo "\"adminURL\""?> value=<?php echo "\"$url\""?>> <!--Send admin url to log out-->
                <input type="submit" name="submit2" class="w3-btn" style="margin-top: 40px; margin-left: 20px;" >
            </form> 
        </div>
        <?php
                    }            
                }
                else{ //if passwords don't match
                    echo "Email or password incorrect, please try again.";
                }
            }
        ?>
    </body>
    
<?php 	} else{ //if the hash is not found
			echo "404" ;
        }
    }
    else {
        echo "Please use your customized URL";
    }
?>
</html>