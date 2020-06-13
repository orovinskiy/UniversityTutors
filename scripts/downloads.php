<?php
session_start();
require_once("../model/config.php");
include '../model/Database.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['item'] = $_POST['item'];
    $_SESSION['file'] = $_POST['fileName'];
    $_SESSION['isOgFile'] = $_POST['ogFile'];
}
else {
    $db = new Database();
    $ext = strtolower(substr($_SESSION['file'], strpos($_SESSION['file'], '.')));
    $type = '';
    $checkFile = '';
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

    if($_SESSION['isAdmin'] !== 1) {
        if ($_SESSION['isOgFile'] == 1) {
            $checkFile = $db->getOgFile($_SESSION['item']);
        } else {
            $checkFile = $db->getTutorFile($_SESSION['yearID'], $_SESSION['item']);
        }
    }

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
