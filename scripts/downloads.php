<?php
session_start();
require_once("../model/config.php");
include '../model/Database.php';

$db = new Database();
$ext = substr( $_SESSION['file'],strpos($_SESSION['file'],'.'));
$type = '';
switch ($ext){
    case 'pdf':
        $type = 'application/octane-stream';
        break;
    case 'docx' || 'doc':
        $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        break;
}


if($db->getTutorFile($_SESSION['yearID'],$_SESSION['item']) == $_SESSION['file']) {

    $file =  '/var/www/uploads/' . $GLOBALS['dirName'] . $_SESSION['file'];

    header('Content-Description: File Transfer');
    header('Content-Type: '.$type);
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));

    ob_clean();
    flush();

    return readfile($file);
}

