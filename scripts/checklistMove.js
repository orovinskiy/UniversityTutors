/**
 * This post data to a ajax function to update the database.
 * It also moves boxes from completed or to not completed.
 * @author Oleg Rovinskiy
 * @version 1.0
 */

//  puts a listener on the body to see if any inputs
// have been clicked
$("body").on("change","#pdf",function(){
    let $file = $(this).attr('id');
    let fd = new FormData();
    let $files = $(this)[0].files[0];
    let input = $(this).parents(':eq(2)').find('.big');
    let id = input.attr('id');
    let tutorId = input.val();

    console.log(id);
    fd.append('file',$files);
    fd.append('tutorId',tutorId);
    fd.append('itemId',id);

    $.ajax({
        url: '../tutFile',
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        success: function (response){
            alert('Succesfully uploaded file');
        }
    });
});

$("body").on("click",".big",function()
{
    //predefined variables
    let wholeCheckBox = $(this).parents(":eq(5)");
    let dataValue;

    //remove whole checkbox
    wholeCheckBox.remove();

    //check what data to put into the database
    if($(this).is(":checked")){

        dataValue = parseInt($(this).data('order'))+1;
        wholeCheckBox.find('input').attr('data-order',dataValue);
        wholeCheckBox.find('label').html('Completed');
        $(".completedBox").append(wholeCheckBox);

    }
    else{
        dataValue = parseInt($(this).data('order'))-1;
        wholeCheckBox.find('input').attr('data-order',dataValue);
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
});
