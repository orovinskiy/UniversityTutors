/**
 * This post data to a ajax function to update the database.
 * It also moves boxes from completed or to not completed.
 * @author Oleg Rovinskiy
 * @version 1.0
 */
//Uploads file if file passes validation
$("body").on("change","#pdf",function(){
    let $file = $(this).attr('id');
    let fd = new FormData();
    let $files = $(this)[0].files[0];
    let input = $(this).parents(':eq(2)').find('.big');
    let name = $(this).parents(':eq(6)').find('.card-header').html();
    let id = input.attr('id');
    let tutorId = input.val();

        fd.append('file',$files);
        fd.append('tutorId',tutorId);
        fd.append('itemId',id);
        fd.append('name',name);

        $.ajax({
            url: '../tutFile',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                alert(response.substring(0,response.indexOf('/')));
                if(response.substring(response.indexOf('/')+1) === "true"){
                    window.location.reload();
                }
            }
        });

});
$("body").on("click",".downloads",function(){
    let aTag = $(this);
    $.post("../sessSett",{
        item: $(this).data("item"),
        fileName: $(this).data("file"),
    });

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
       /* .done(function(data, textStatus, request){
             alert(data);
            //let type = request.getResponseHeader('Content-Type');
            let blob = new Blob([data]);
            let reader = new FileReader();
            reader.onload = e => {
                const anchor = document.createElement('a');
                anchor.style.display = 'none';
                anchor.href = e.target.result;
                anchor.download = aTag.data("file");
                anchor.click();
            };
            reader.readAsDataURL(blob);
        });*/
});
//  puts a listener on the body to see if any inputs
// have been clicked
$("body").on("click",".big",function()
{
    //predefined variables
    let wholeCheckBox = $(this).parents(":eq(6)");
    let dataValue;
    let uploadFile = $(this).parents(":eq(3)");
    let proceed = true;

    //check if the file upload exits
    if(uploadFile.find('.uploadFile').length){
        if(uploadFile.find('.uploads').length === 0){
            proceed = false;
        }
    }

    if(proceed){
        //remove whole checkbox
        wholeCheckBox.remove();

        //check what data to put into the database
        if($(this).is(":checked")){

            dataValue = parseInt($(this).data('order'))+1;
            wholeCheckBox.find('input').attr('data-order',dataValue);
            wholeCheckBox.find('label').html('Completed');
            wholeCheckBox.find('.checkHide').toggleClass('hidden');
            $(".completedBox").append(wholeCheckBox);

        }
        else{
            dataValue = parseInt($(this).data('order'))-1;
            wholeCheckBox.find('input').attr('data-order',dataValue);
            wholeCheckBox.find('.checkHide').toggleClass('hidden');
            wholeCheckBox.find('label').html('Complete');
            $(".notCompletedBox").append(wholeCheckBox);
        }

        //Post to makeBox to save the data
        $.post("../makeBox",
            {
                user: $(this).val(),
                value : dataValue,
                item: $(this).attr('id')
            });
    }
    else{
        alert('Please upload a file before checking off the task.');
        this.checked = false; // reset first
        event.preventDefault();
    }
});
