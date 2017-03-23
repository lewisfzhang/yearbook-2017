<!DOCTYPE HTML>
<html>
    <head>
        <title>Test Emailer</title>
    </head>
    <body>
        <p>Some words</p>
        <?php
            //PHPMailer using local PHP SMTP server
	        require('PHPMailer/PHPMailerAutoload.php');
			
	        $mail = new PHPMailer;
	        //$mail->isSMTP();
	        $mail->Host = 'smtp.bcp.org';
	        $mail->Port = 25;
            
	        //Get data from form
	        $subject = "Test from Carillon";
	        //$body = htmlspecialchars($_POST['message'])
	        //echo $_POST['message'];
	        $body = nl2br("Test");
            
	        //Set initial mail headers
	        $mail->From = "carillon@bcp.org";
	        $mail->FromName = "The Carillon";
			//$mail->AddAddress('kgred9@gmail');
	        $mail->AddBCC('carillon@bcp.org');
            $mail->AddAddr	ess('kevin.gottlieb19@bcp.org');
	        $mail->Subject = $subject;
	        $mail->Body = $body;
	        $mail->IsHTML(true);
        
			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo . '<br>';
			} else {
				echo 'All messages have been sent.';
			}
			/*
			require('PHPMailer/PHPMailerAutoload.php'); //PHPMailer file
			/*$db = new SQLite3('quotations2017.sqlite3'); //connect

			function sendMail($to, $subject, $message){ //send email
				$mail = new PHPMailer;
				//$mail->isSMTP();
				$mail->Host = 'smtp.bcp.org';
				$mail->Port = 25;
				$mail->CharSet = 'UTF-8';

				//Set initial mail headers
				$mail->From = "carillon@bcp.org";
				$mail->FromName = "The Carillon";
				$mail->AddBCC('carillon@bcp.org');
				$mail->AddBCC('kgred9@gmail.com');
				$mail->AddAddress($to);
				$mail->Subject = $subject;
				$mail->Body = $message;
				$mail->IsHTML(true);
						
				return $mail->send(); //will return true if sending worked
			}
			if(sendMail('kevin.gottlieb19@bcp.org', 'Test', 'Test')){
				echo 'Sent!';
			}
			else{
				echo 'Fail?';
			}
			echo '<br>Meow';
			
			$mail = new PHPMailer;
			//$mail->isSMTP();
			$mail->Host = 'smtp.bcp.org';
			$mail->Port = 25;
			$mail->CharSet = 'UTF-8';

			//Set initial mail headers
			$mail->From = "carillon@bcp.org";
			$mail->FromName = "The Carillon";
			$mail->AddBCC('carillon@bcp.org');
			$mail->AddBCC('kgred9@gmail.com');
			$mail->AddAddress($to);
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->IsHTML(true);
					
			$mail->send();
			*/
        ?>
    </body>
</html>