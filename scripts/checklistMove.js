$(".get").on("click",function()
{
    console.log($(this).val());
    console.log($(this).attr("id"));
    console.log($(this).is(":checked"));
    let wholeCheckBox = $(this).parents(":eq(3)");
    console.log(wholeCheckBox);
    wholeCheckBox.remove();

    let dataValue;
    if($(this).is(":checked")){
        dataValue = 1;
        $(".completedBox").append(wholeCheckBox);
    }
    else{
        dataValue = 0;
        $(".notCompletedBox").append(wholeCheckBox);
    }

    if($(this).attr("id") === "ADP Registration"){
        dataValue = ""
    }

    $.post("../makeBox",
        {
            form : $(this).attr("id"),
            value : dataValue
        });
});
