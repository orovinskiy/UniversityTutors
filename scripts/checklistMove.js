/**
 * This post data to a ajax function to update the database.
 * It also moves boxes from completed or to not completed.
 * @author Oleg Rovinskiy
 * @version 1.0
 */

//  puts a listener on the body to see if any inputs
// have been clicked
$("body").on("click","input",function()
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
