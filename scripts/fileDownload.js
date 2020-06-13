//Event listener to download all files
$("body").on("click",".downloads",function(){
    let aTag = $(this);
    $.post("../scripts/downloads.php",{
        item: $(this).data("item"),
        fileName: $(this).data("file"),
        ogFile: $(this).data('og')
    });


    setTimeout(function () {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '../scripts/downloads.php', true);
        xhr.responseType = 'blob';

        xhr.onload = function(e) {
            if (this.status === 200) {
                // Note: .response instead of .responseText
                let blob = new Blob([this.response], {type: this.getResponseHeader('Content-Type')}),
                    url = URL.createObjectURL(blob),
                    anchor = document.createElement('a');

                anchor.style.display = 'none';
                anchor.href = url;
                anchor.download = aTag.data("file");
                anchor.click();
                $('.notCompletedBox').append(anchor)
            }
        };
        xhr.send();
    },1000)

});
