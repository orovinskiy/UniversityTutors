<?php
require "PHPMailer/PHPMailerAutoload.php";

class Mail {
    private $_subject;
    private $_body;
    private $_defaultAttachments;
    private $_JSONData;

    /**
     * Mail constructor which pulls default subject, body, and attachments from emailTemplate.json
     */
    public function __construct()
    {
        $this->pullFromJSON();
        $this->_subject = $this->_JSONData['subject'];
        $this->_body = $this->_JSONData['body'];
        $this->_defaultAttachments = $this->_JSONData['attachment'];
    }

    /**
     * Getter for subject field
     * @return String default subject of email
     */
    public function getSubject()
    {
        $this->pullFromJSON();
        return $this->_subject;
    }

    /**
     * Setter of subject field. Will re-write JSON file accordingly
     * @param String $subject default subject of email
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        //saving new subject to JSONData variable and re-writing to json
        $this->_JSONData['subject'] = $subject;
        //encoding
        $updatedJSON = json_encode($this->_JSONData);
        file_put_contents('emailTemplate.json', $updatedJSON);
    }

    /**
     * Getter for default email body
     * @return String default body for email
     */
    public function getBody()
    {
        $this->pullFromJSON();
        return $this->_body;
    }

    /**
     * setter of body field for email. Will re-write JSON file accordingly
     * @param String $body default body of email
     */
    public function setBody($body)
    {
        $this->_body = $body;
        //saving new subject to JSONData variable and re-writing to json
        $this->_JSONData['body'] = $body;
        //encoding
        $updatedJSON = json_encode($this->_JSONData);
        file_put_contents('emailTemplate.json', $updatedJSON);
    }

    /**
     * Getter for default attachments for email
     * @return String default name of attachment
     */
    public function getDefaultAttachments()
    {
        $this->pullFromJSON();
        return $this->_defaultAttachments;
    }

    /**
     * setter of default attachments for email. Will re-write JSON file accordingly
     * @param String $defaultAttachments path for file attachment
     * @return int returns whether the file is saved (1), the file is not saved (0) or if the file is not saved due
     * to the file alraedy existing (2)
     */
    public function setDefaultAttachments($defaultAttachments)
    {
        //add attachment to array
        //first check to see if attachment already exists
        $attachmentArray = $this->_JSONData['attachment'];
        for ($i = 0; $i < count($attachmentArray); $i++) {
            if ($attachmentArray[$i] == $defaultAttachments) {
                return 2; //2 indicates that file already exists
            }
        }

        array_push($this->_defaultAttachments, $defaultAttachments);
        //$this->_defaultAttachments = $defaultAttachments;
        //saving new subject to JSONData variable and re-writing to json
        $this->_JSONData['attachment'] = $this->_defaultAttachments;
        //encoding
        $updatedJSON = json_encode($this->_JSONData);
        if (file_put_contents('emailTemplate.json', $updatedJSON)) {
            return 1;
        } else return 0;



    }

    public function deleteDefaultAttachment($attachment) {
        $attachmentArray = $this->_JSONData['attachment'];
        $deleted = '';
        for ($i = 0; $i < count($attachmentArray); $i++) {
            if ($attachmentArray[$i] == $attachment) {
                unset($attachmentArray[$i]);
                if (unlink($attachment)) {
                    $deleted = true;
                } else {
                    $deleted = "File not deleted from directoy";
                }
            } else{
                $deleted = false;
            }
        }
        //normalize integer values
        $attachmentArray = array_values($attachmentArray);
        //saving updates to JSON file
        $this->_JSONData['attachment'] = $attachmentArray;
        //encode
        $updatedJSON = json_encode($this->_JSONData);
        file_put_contents('emailTemplate.json', $updatedJSON);
        return $deleted;
        //return $attachmentArray;
    }

    /**
     * Function that creates and sends an email with an attachment optional
     * @param String $to recipient of email
     * @param String $from Sender of email
     * @param String $from_name Name of sender
     * @param String $loginBody login portion of welcome email
     * @param string $type type of email being send, 'normal' is default parameter
     * @return bool true if email was sent successfully, false otherwise
     * @throws phpmailerException
     * @author Dallas Sloan and GitHub
     */
    function smtpmailer($to, $from, $from_name, $loginBody, $type = 'normal')
    {
        $this->pullFromJSON();
        //instantiating new mailer object
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;

        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'kold-tutors.greenriverdev.com';
        $mail->Port = 465;
        $mail->Username = 'universitytutors@kold-tutors.greenriverdev.com';
        $mail->Password = 'Monday!99';

        //checking to see what type of email to send
        if ($type == "normal") {
            //check to see if there are any attachments to be added if there are, add attachment to email
            if (!empty($this->_defaultAttachments)) {
                for ($i = 0; $i < count($this->_defaultAttachments); $i++) {
                    $mail->addAttachment($this->_defaultAttachments[$i]);
                }
            }


            //email information
            $mail->IsHTML(true);
            $mail->From = $from;
            $mail->FromName = $from_name;
            $mail->Sender = $from;
            $mail->AddReplyTo($from, $from_name);
            $mail->Subject = $this->_subject;
            $mail->Body = "<p>" . $this->_body . "</p>" . "<p>$loginBody</p>";
            $mail->AddAddress($to);
        } else {
            //email information
            $mail->IsHTML(true);
            $mail->From = $from;
            $mail->FromName = $from_name;
            $mail->Sender = $from;
            $mail->AddReplyTo($from, $from_name);
            $mail->Subject = "Temporary Password";
            $mail->Body = "<p>" . $loginBody . "</p>";
            $mail->AddAddress($to);

        }

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

    /**
     *Function to pull the most up to date information from JSON when accessing JSONData variable
     */
    private function pullFromJSON() {
        //pulling in the JSON file to be read and decoding info
        $JSONFile = 'emailTemplate.json';
        //get the json file data store them as string
        $this->_JSONData = file_get_contents($JSONFile);
        $this->_JSONData = json_decode($this->_JSONData, true);
    }


}


