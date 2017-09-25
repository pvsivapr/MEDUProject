<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of send-email
 *
 * @author Administrator
 */
class mail {
    
    const SMTPUSER = 'vinod4b4@gmail.com';
    const SMTPPWD = 'Vinod@123';
    const SMTPSERVER = 'smtp.gmail.com';    
    const SMTPPORT = 465;#587
    public static function sendMail($to,$to_name,$from,$from_name,$subject,$body)
    {
        require_once ('../classes/phpmailer.php');
        $mail = new PHPMailer();
        $mail -> IsSMTP();
        $mail->SetLanguage("en");
        $mail -> ContentType = 'text/html';
        $mail -> SMTPAuth = true;
        $mail -> SMTPSecure = 'ssl';#'tls';
        // secure transfer enabled REQUIRED for GMail
        $mail -> Host = self::SMTPSERVER;
        $mail -> Port = self::SMTPPORT;
        $mail -> Username = self::SMTPUSER;
        $mail -> Password = self::SMTPPWD;   
        $mail -> SetFrom($from, $from_name);
        $mail -> Subject = $subject;
        $mail -> Body = $body;
        $mail -> AddBCC('vinod.alampally@devrabbit.com','Vinod');
        $mail -> AddAddress($to);
        if (!$mail -> Send()) {
                $error = 'Mail error: ' . $mail -> ErrorInfo;
                return false;
        } else {
                //echo "Message sent";
                return true;       
	}	        
    }
}
