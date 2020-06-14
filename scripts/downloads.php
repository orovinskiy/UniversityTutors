<?php
/**This file checks what user is logged in and if the
 * user is admin. Then it checks if the file they want exits
 * for the specific user. (For admins it doesnt check if the file exits since
 * admins can view everyone's file) It checks what type of file
 * it is and sends the appropriate headers to download the file.
 * @author Oleg Rovinskiy
 * @version 1.0
 */
session_start();
require_once("../model/config.php");
include '../model/Database.php';

//sets the variables that were sent
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['item'] = $_POST['item'];
    $_SESSION['file'] = $_POST['fileName'];
    $_SESSION['isOgFile'] = $_POST['ogFile'];
}//creates the file from the previous variables that were sent
else {
    $db = new Database();
    $ext = strtolower(substr($_SESSION['file'], strpos($_SESSION['file'], '.')));
    $type = '';
    $checkFile = '';

    //Checks for what type of file
    switch ($ext) {
        case 'pdf':
            $type = 'application/pdf';
            break;
        case 'docx' || 'doc':
            $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            break;
        case 'zip':
            $type = 'application/zip';
            break;
        case 'jpeg':
            $type = 'image/jpeg';
            break;
        case 'jpg':
            $type = 'image/jpg';
            break;
        case 'png':
            $type = 'image/png';
            break;
    }

    //checks if file exits for the user
    if($_SESSION['isAdmin'] !== 1) {
        if ($_SESSION['isOgFile'] == 1) {
            $checkFile = $db->getOgFile($_SESSION['item']);
        } else {
            $checkFile = $db->getTutorFile($_SESSION['yearID'], $_SESSION['item']);
        }
    }

    //if the correct user is trying to access the correct file send the file over
    if ($checkFile == $_SESSION['file'] || $_SESSION['isAdmin'] === 1) {

        $file = '/var/www/uploads/' . $_SESSION['file'];

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $type);
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        ob_clean();
        flush();

        unset($_SESSION['file']);
        unset($_SESSION['item']);
        return readfile($file);
    }
}
