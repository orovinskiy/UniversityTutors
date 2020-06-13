let tag = $('.image');
console.log(tag.data('img'));
$.post("../scripts/getImage.php",{
    fileName: tag.data('img')
});


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