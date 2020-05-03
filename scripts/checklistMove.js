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
    let wholeCheckBox = $(this).parents(":eq(3)");
    let dataValue;

    //remove whole checkbox
    wholeCheckBox.remove();

    //check what data to put into the database
    if($(this).is(":checked")){
        dataValue = 1;
        $(".completedBox").append(wholeCheckBox);
    }
    else{
        dataValue = 0;
        $(".notCompletedBox").append(wholeCheckBox);
    }

    if($(this).attr("id") === "ADP Registration"){
        $(this).is(":checked") ? dataValue = "registered" : dataValue = "invited";
    }

    if($(this).attr("id") === "I-9"){
        $(this).is(":checked") ? dataValue = "tutor" : dataValue = "none";
    }

    //Post to makeBox to save the data
    $.post("../makeBox",
        {
            column : $(this).val(),
            value : dataValue,
            year : $('.yearID').attr('data-id')
        });
});
