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
        dataValue = parseInt($(this).data('state'))+1;
        $(".completedBox").append(wholeCheckBox);
        wholeCheckBox.find('label').html('Completed');

    }
    else{
        wholeCheckBox.children('label').innerHTML = 'Complete';
        dataValue = parseInt($(this).data('state'))-1;
        $(".notCompletedBox").append(wholeCheckBox);
        wholeCheckBox.find('label').html('Complete');
    }

    //Post to makeBox to save the data
    $.post("../makeBox",
        {
            user: $(this).val(),
            value : dataValue,
            item: $(this).attr('id'),
            //year : $('.yearID').attr('data-id')
        });
});
