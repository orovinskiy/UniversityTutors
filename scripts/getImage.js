/**This is a script to retreive the tutors picture and
 * display it. It gets the file from getImage.php
 * @author Oleg Rovinskiy
 * @version 1.0
 */


let tag = $('.image');

//set the image name
$.post("../scripts/getImage.php",{
    fileName: tag.data('img')
});

//get the image and display it on the page
setTimeout(function () {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', '../scripts/getImage.php', true);
    xhr.responseType = 'blob';

    xhr.onload = function(e) {
        if (this.status === 200) {
            // Note: .response instead of .responseText
            let blob = new Blob([this.response], {type: this.getResponseHeader('Content-Type')})
            let urlCreator = window.URL || window.webkitURL;
            let url = urlCreator.createObjectURL(blob);

            tag.attr('src',url);
            tag.attr('alt','Picture of tutor')
        }
    };
    xhr.send();
},1000);