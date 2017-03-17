<?php
    error_reporting(E_ERROR | E_WARNING | E_PARSE); //doesn't report small errors
    require('PHPMailer/PHPMailerAutoload.php'); //PHPMailer file
    $url = $_GET['id']; //student's hash
    $fullStudentURL = "http://code.bcp.org/yb-dev/q2017/index.php?id=$url"; //the actual link to the student's quotation entry page
    if($url != NULL){ //if the url has the studnet's unique hash
        $db = new SQLite3('quotations2017.sqlite3'); //connect
        //get first name 
        $statement = $db -> prepare('SELECT * FROM quotations WHERE url = :url;'); 
        $statement -> bindValue(':url', $url);
        $result = $statement->execute();
        //get first name
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $firstName = $row['firstName']; 
        }
        //get quotation
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $quotation = $row['quotation']; 
        }
        //get whether student has approved
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $processedStudent = $row['processedStudent']; 
        }
        //get whether teacher has approved
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $processedTeacer = $row['processedTeacher']; 
        }
        //get student's email
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $studentEmail = $row['email']; 
        }

        if(($firstName != "") and isset($firstName)){ //if the hash is found
            function sendMail($to, $subject, $message){ //function to send email
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = 'localhost';
                $mail->Port = 25;
                $mail->CharSet = 'UTF-8';

                //Set initial mail headers
                $mail->From = "carillon@bcp.org";
                $mail->FromName = "The Carillon";
                $mail->AddBCC('carillon@bcp.org');
                $mail->AddAddress($to);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->IsHTML(true);
                
                return $mail->send(); //will return true if sending worked
            }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Bellarmine Senior Quotaion</title>
        <!--Some JS to count the char number in the textarea-->
        <script>
            function charCount() {
                var length = document.getElementById("quotationEntry").value.length; //the number of characters in text area
                var charsLeft = 100 - length; //the number of character left to type out of 100
                if (charsLeft >= 0) { //if student hasn't reached limit
                    document.getElementById("charCount").innerHTML = "Character Count: " + length + "/100"; //put out character count
                }
                else { //if student went over limit
                    document.getElementById("charCount").innerHTML = "Character Count: " + "100/100"; //say that student has gone over limit
                    var quotation = document.getElementById("quotationEntry").value;
                    var newQuotation = quotation.substring(0, 100); //take 1st 100 characters
                    document.getElementById("quotationEntry").value = newQuotation; //stop after first 100 characters
                }
                return length;
            }
            function trimmer() { //trims text in textarea
                var text = document.getElementById("quotationEntry").value;
                var trimmedText = text.trim();
                document.getElementById("quotationEntry").value = trimmedText; 
            }
        </script>
        <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css"> <!--W3.CSS stylesheet-->
    </head>
    <body>
        <?php
            if(!($processedStudent == 1 and $processedTeacer == 1 and $quotation != "")){ //if everything hasn't been approved
            //then allow student to enter quotation
        ?>
        <header class="w3-container w3-blue">
            <h1>Please enter your quotation, <?php echo "$firstName"?></h1>
        </header>
        <form class="w3-container w3-card" method="post" style="margin-top: 20px;"> <!--Form with submit button-->
            <textarea id="quotationEntry" name="quotationEntry" onchange="charCount()" onkeyup="charCount()" onkeydown="charCount()" onblur="charCount()" onfocus="charCount()" rows="3" cols="50">
<?php
    $quotation = trim($quotation); //trim whitespace
    if(isset($quotation) and $quotation != ""){ //if quotation isn't null
        echo "$quotation"; //put quotation in text area 
    }
?>
            </textarea>
            <script>
                trimmer();
            </script>
            <p id="charCount">Character Count: /100</p>
            <script>
                charCount(); 
            </script>
            <p>Be sure to cite your source! Stick to plain text: no emoji or special characters.</p>
            <input type="submit" name="submitQuote" class="w3-btn">
        </form>
        <?php
            if(isset($_POST['quotationEntry'])){ //if the user entered a quotation
                $newQuotation = $_POST['quotationEntry']; //get new quotation
                //set quotation and reset approvals
                $statement = $db -> prepare(
                'UPDATE quotations
                SET quotation = :newQuotation, 
                processedStudent = 0, 
                processedTeacher = 0,
                disapprovalReason = NULL
                WHERE url = :url;'); 
                $statement -> bindValue(':url', $url);
                $statement -> bindValue(':newQuotation', $newQuotation);
                $result = $statement->execute();
                if($result){ //if query worked
                    $emailMessage = 
                    "Hello $firstName, <br><br>
                    Your senior quotation for this year's Carillon Yearbook has been received and is pending approval! <br><br>
                    Your quotation: $newQuotation <br><br>
                    If something looks wrong, reply directly to this email. <br><br>
                    Thanks again, <br><br>
                    The Carillon Staff
                    ";
                    if(sendMail($studentEmail, "Carillon Senior Quotation Confirmation", $emailMessage)){ //if mail is sent successfully
                        echo "<script>
                        window.open('thankYou.html', '_self', false);
                        </script>"; //open a new window to show that quotation has been submitted
                    }
                    else{ //if send fails
                        echo "Oh no! Sending a confirmation email has failed! Plase check with <a href='mailto:carillon@bcp.org'>carillon@bcp.org</a> if your quotation has been received.";
                    }
                }
            }
            }
            else{ //if quotation is already approved
        ?>
        <header class="w3-container w3-blue">
            <h1>You're good to go, <?php echo "$firstName"?>!</h1>
        </header>
        <h2>Your approved quotation: <?php echo "\"$quotation\""?></h2>
        <?php
            }
        ?>
        <p>Please email <a href="mailto:carillon@bcp.org">carillon@bcp.org</a> if you're having any trouble.</p>
    </body>
</html>

<?php 	} else{ //if the hash is not found
			echo "404" ;
        }
    } else {  //if ther is no unique hash at the end
        echo "Please check your email for a customized URL.";
    }
?>