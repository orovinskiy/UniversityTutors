<?php
/**This checks what picture to send over to the page.
 * @author Oleg Rovinskiy
 * @version 1.0
 */
session_start();

//set what picture
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['file'] = $_POST['fileName'];
}
else {//get the set picture if it exists
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

