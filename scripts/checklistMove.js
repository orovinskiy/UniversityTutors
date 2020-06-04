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
    let name = $(this).parents(':eq(5)').find('.card-title').html();
    let id = input.attr('id');
    let tutorId = input.val();
    console.log($files);

    //$(this).parents(':eq(2)').find('.upload').attr('href','uploads/'+name+'-'+id+'-'+tutorId+'');

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
                alert(response);
            }
        });

});

//  puts a listener on the body to see if any inputs
// have been clicked
$("body").on("click",".big",function()
{
    //predefined variables
    let wholeCheckBox = $(this).parents(":eq(6)");
    let dataValue;

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
});
