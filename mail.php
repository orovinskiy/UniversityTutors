<?php
require "PHPMailer/PHPMailerAutoload.php";

/**
 * Function that creates and sends an email with an attachment optional
 * @param String $to recipient of email
 * @param String $from Sender of email
 * @param String $from_name Name of sender
 * @param String $subject Subject line of email
 * @param String $body Body of email, just be in HTML format
 * @return bool true if email was sent successfully, false otherwise
 * @throws phpmailerException
 * @author Dallas Sloan and GitHub
 */
function smtpmailer($to, $from, $from_name, $subject, $body)
{
    //instantiating new mailer object
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;

    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'kold-tutors.greenriverdev.com';
    $mail->Port = 465;
    $mail->Username = 'universitytutors@kold-tutors.greenriverdev.com';
    $mail->Password = 'Monday!99';

    //adding attachments to email
    //$path = 'attachments/IT355.zip';
    //$path2 = 'attachments/PyramidMod.png';
    //$mail->AddAttachment($path);
    //$mail->AddAttachment($path2);

    //email information
    $mail->IsHTML(true);
    $mail->From = "universitytutors@kold-tutors.greenriverdev.com";
    $mail->FromName = $from_name;
    $mail->Sender = $from;
    $mail->AddReplyTo($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    //checking whether or not email was successful
    if (!$mail->Send()) {
        //$error ="Please try Later, Error Occurred while Processing...";
        //return $error;
        $success = false;
        return $success;
    } else {
        //$error = "Thanks You!! Your email is sent.";
        //return $error;
        $success = true;
        return $success;
    }
}

