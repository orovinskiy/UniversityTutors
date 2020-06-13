<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['file'] = $_POST['fileName'];
}
else {
    $ext = strtolower(substr($_SESSION['file'], strpos($_SESSION['file'], '.')));
    $type = '';
    switch ($ext) {
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


        $file = '/var/www/uploads/' . $_SESSION['file'];

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
        return readfile($file);

}

