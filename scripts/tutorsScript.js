/**
 * Scripts for the tutors.html onboarding management page
 * @author Keller Flint
 */

let table;

// Creates tutors DataTable
$(document).ready(function () {
    let checkboxArray = []
    let selectArray = []
    let add = 0;
    $("th").each(function ($item) {
        if ($(this).data("type") === "select") {
            selectArray.push($item);
        } else if ($(this).data("type") === "checkbox") {
            checkboxArray.push($item);
        } else {
            add = $item;
        }
    });
    console.log(checkboxArray);
    console.log(selectArray);
    table = $('#tutors-table').DataTable({
        //autoWidth: false,
        scrollX: true,
        columnDefs: [
            {width: "150px", "targets": selectArray},
            {width: "50px", "targets": checkboxArray},
            {width: "1500px", "targets": add}
        ],
        fixedColumns:   {
            leftColumns: 1
        }
    });
});

// redraw table on size change
$(window).resize(function(){
    table.draw();
});

// Event listener for updating checkboxes
$(".checkbox-big").on("click", function () {

    // Get data for update
    let itemId = $(this).data("item-id");
    let tutorYearId = $(this).data("tutor-year-id");
    let stateOrder = $(this).is(":checked") ? 2 : 1;

    // Update database
    updateCheckbox($(this), itemId, tutorYearId, stateOrder);

});

// Event listener for updating selects
$(".tutor-select").on("change", function () {

    // Get data for update
    let itemId = $(this).data("item-id");
    let tutorYearId = $(this).data("tutor-year-id");
    let stateId = $(this).val();

    // Update database
    updateSelect($(this), itemId, tutorYearId, stateId);

});

/**
 * Updates selects via ajax
 *
 * @param element The JQuery object that was clicked on
 * @param itemId The id of the item being updated
 * @param tutorYearId The id of the tutor year being updated
 * @param stateId The id of the state the item is being updated to
 * @author Keller Flint
 */
function updateSelect(element, itemId, tutorYearId, stateId) {
    $.post("../tutorsAjax", {
        itemId: itemId,
        tutorYearId: tutorYearId,
        stateId: stateId
    }, function (result) {
        element.attr("data-is-done", result);
    });
}

/**
 * Updates checkboxes via ajax
 *
 * @param element The JQuery object that was clicked on
 * @param itemId The id of the item being updated
 * @param tutorYearId The id of the tutor year being updated
 * @param stateOrder The order of the state the item is being updated to
 * @author Keller Flint
 */
function updateCheckbox(element, itemId, tutorYearId, stateOrder) {
    $.post("../tutorsAjax", {
        itemId: itemId,
        tutorYearId: tutorYearId,
        stateOrder: stateOrder
    }, function (result) {
        element.attr("data-is-done", result);
    });
}

// Event listener for adding new tutors on click
$("#add-tutor-button").on("click", function () {

    // Get data for update
    let email = $("#add-tutor-input").val();
    let year = $(this).data("year");

    let emailStatus = setInterval(function () {
        $("#email-status").html("Sending email...");
    });

    // Update database via ajax
    $.post("../tutorsAjax", {
        email: email,
        year: year
    }, function (result) {
        $("#email-status").html();
        alert(result);
        //refresh page to load new user into table
        let year = $("#year-current").data("year");
        window.location.href = ("../tutors/" + year);
    });
});

//Event listener for changing year to next year
//@author Dallas Sloan
$(".year-change").on("click", function () {
    //get current year from year param
    let year = $("#year-current").data("year");

    //get which button was changed
    let change = $(this).attr("id");

    //changing the year param to correct year
    if (change === "year-last") {
        year--;
    } else {
        year++;
    }
    //testing info, can delete once confirmed working
    //alert("This is the current year: "+ year + "Did we get the right button? : " +change);

    //refresh page with new year
    window.location.href = ("../tutors/" + year);
});

// hide delete and import buttons on window click
$(window).on("click", function () {
    $(".delete").addClass("d-none");
    $(".import").addClass("d-none");
    $(".remove").addClass("d-none");
});

// show delete, remove and import buttons on email click
$(".email").on("click", function (event) {

    // hide any open buttons
    $(".delete").addClass("d-none");
    $(".import").addClass("d-none");
    $(".remove").addClass("d-none");

    // show current buttons
    $(this).find(".delete").removeClass("d-none");
    $(this).find(".import").removeClass("d-none");
    $(this).find(".remove").removeClass("d-none");

    // stop window event propagation to keep the window event listener from hiding the button again
    event.stopPropagation();
});

// Event listener for remove button clicked
$(".remove").on("click", function () {
    let result = confirm("Are you sure you want to remove this user and all their data for this year?");
    if (result) {
        let tutorYear_id = $(this).data("tutoryearid");

        // ajax deletion
        $.post("../tutorsAjax", {
            remove: true,
            tutorYear_id: tutorYear_id
        }, function () {
            let year = $("#year-current").data("year");
            window.location.href = ("../tutors/" + year);
        });
    }
});

// Event listener for delete button clicked
$(".delete").on("click", function () {
    let result = confirm("Are you sure you want to delete this user and all data associated with them?");
    if (result) {
        let user_id = $(this).data("userid");

        // ajax deletion
        $.post("../tutorsAjax", {
            delete: true,
            user_id: user_id
        }, function () {
            let year = $("#year-current").data("year");
            window.location.href = ("../tutors/" + year);
        });
    }
});

// Event listener to import the user to the current year on click
$(".import").on("click", function () {
    let result = confirm("Are you sure you want import this user to the current year?");
    if (result) {
        let user_id = $(this).data("userid");

        // ajax importing
        $.post("../tutorsAjax", {
            user_id: user_id
        }, function (result) {
            //hide Import button after displaying success message
            if (result === "true") {
                $(".import").hide();
            } else {
                alert("Import failed: " + result);
            }

        });

    }
});

// event listener for setting the current year
$("#current-year").on("click", function () {

    let year = $("#year-current").data("year");

    $.post("../tutorsAjax", {
        current_year: year
    }, function () {
        // refresh data
        window.location.href = ("../tutors/" + year);
    });
});

//event listener to save default email information
//@author Dallas Sloan
$("#save-default").on('click', function () {
    //console.log("Did this work?");

    //getting th email and file values
    let newSubject = $("#email-subject").val();
    let newBody = $("#email-body").val();
    //ajax call to update subjec and body of email
    $.post("../tutorsAjax", {
        subject: newSubject,
        body: newBody,
    }, function () {
        alert("Changes have been saved");
        $("#email-modal").modal('hide');
    });

});


//event listener uploading multiple files
//@author Laxmi & Dallas
$.fn.fileUploader = function (filesToUpload) {
    this.closest(".files").change(function (evt) { //  div class on change run function
        for (var i = 0; i < evt.target.files.length; i++) {
            filesToUpload.push(evt.target.files[i]);

            //ajax call for file uploads will uploads each file individually
            let fd = new FormData(); //creating FormData object
            let files = $('#files')[0].files[i];
            fd.append('file', files);
            //making ajax call to updated email json
            $.ajax({
                url: '../tutorsAjax',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    //console.log(response);
                    if (response == 1) {
                        //alert("File uploaded");
                        console.log(response);
                    } else if (response == 0) {
                        alert('Changes not saved');
                    } else if (response == 2) {
                        alert("File Already Exists")
                    }
                }
            });
        }
        //console.log(filesToUpload);


        var output = [];
        for (var i = 0, f; f = evt.target.files[i]; i++) {
            var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + i + "\">Remove</a>";

            output.push("<li><strong>", escape(f.name), "</strong> - ",
                f.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
        }
        //append the remove link in each li
        $(this).children(".fileList")
            .append(output.join(""));
    });
};

var filesToUpload = [];

//remove files for the list
$(document).on("click", ".removeFile", function (e) {
    e.preventDefault();
    var fileName = $(this).parent().children("strong").text();
    // loop through the files array and check if the name of that file matches FileName
    // and get the index of the match
    for (i = 0; i < filesToUpload.length; ++i) {
        if (filesToUpload[i].name == fileName) {
            //console.log("match at: " + i);
            // remove the one element at the index where we get a match
            filesToUpload.splice(i, 1);
        }
    }
    //console.log(filesToUpload);
    // remove the <li> element of the removed file from the page DOM
    $(this).parent().remove();
});


//stores all the files selected in ul list
//@author Dallas Sloan
$(".fileList").fileUploader(filesToUpload);

//event listener to delete file from ajax document
//@author Dallas Sloan
$(".removeFile").on("click", function () {
    let $fileToDelete = $(this).parent().attr("value");
    //console.log($fileToDelete); //used for testing
    $.post("../tutorsAjax", {
        fileToDelete: $fileToDelete,
    }, function (response) {
        alert("File Deleted");
        console.log(response);
    });
});

// Filtering

/**
 * Sets all filter buttons to btn-secondary
 * @author Keller Flint
 */
function resetFilterButtons() {
    $("#filter-all").removeClass("btn-primary");
    $("#filter-all").addClass("btn-secondary");

    $("#filter-incomplete").removeClass("btn-primary");
    $("#filter-incomplete").addClass("btn-secondary");

    $("#filter-complete").removeClass("btn-primary");
    $("#filter-complete").addClass("btn-secondary");
}

// show incomplete rows on click of incomplete filter button
$("#filter-incomplete").on("click", function () {
    resetFilterButtons();
    $(this).removeClass("btn-secondary");
    $(this).addClass("btn-primary");
    $(".user").each(function () {
        $(this).show();
        let id = $(this).attr("id");
        let doneArray = [];
        $("#" + id + " .item-input").each(function () {
            doneArray.push(parseInt($(this).attr("data-is-done")));
        });
        console.log(doneArray);
        if (!doneArray.includes(0)) {
            $(this).hide();
        }
    });
});

// show complete rows on click of complete filter button
$("#filter-complete").on("click", function () {
    resetFilterButtons();
    $(this).removeClass("btn-secondary");
    $(this).addClass("btn-primary");
    $(".user").each(function () {
        $(this).show();
        let id = $(this).attr("id");
        let doneArray = [];
        $("#" + id + " .item-input").each(function () {
            doneArray.push(parseInt($(this).attr("data-is-done")));
        });
        console.log(doneArray);
        if (doneArray.includes(0)) {
            $(this).hide();
        }
    });
});

// show all rows on click of all filter buttons
$("#filter-all").on("click", function () {
    resetFilterButtons();
    $(this).removeClass("btn-secondary");
    $(this).addClass("btn-primary");
    $(".user").each(function () {
        $(this).show();
    });
});