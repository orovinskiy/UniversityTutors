/**
 * Scripts for the tutors.html onboarding management page
 * @author Keller Flint
 */

// Creates tutors DataTable
$(document).ready(function () {
    $('#tutors-table').DataTable({
        responsive: true
    });
});

// Stores current filter so when page is reloaded, it is reloaded with the appropriate filter
let status = $("#status").val();

// Event listener for updating checkboxes
$(".checkbox-big").on("click", function () {

    // Get data for update
    let column = $(this).data("column");
    let value = $(this).is(":checked") ? 1 : 0;
    let yearId = $(this).data("yearid");

    // Update database via ajax
    $.post("../tutorsAjax", {
        column: column,
        value: value,
        yearId: yearId
    });
});

// Event listener for updating selects
$(".tutor-select").on("change", function () {

    // Get data for update
    let column = $(this).data("column");
    let value = $(this).val();
    let yearId = $(this).data("yearid");

    // Update database via ajax
    $.post("../tutorsAjax", {
        column: column,
        value: value,
        yearId: yearId
    });
});

// Event listener to show/hide the save button when the placement text is changed.
$(".placement").on("keyup", function () {
    let button = $(this).parent().find(".placement-button");

    if ($(this).attr("data-original") === $(this).val()) {
        button.addClass("d-none");
    } else {
        button.removeClass("d-none");
    }
});


// Event listener for updating inputs
$(".placement-button").on("click", function () {
    let input = $(this).parent().find(".placement");
    let button = $(this);
    // Get data for update
    let column = input.data("column");
    let value = input.val();
    let yearId = input.data("yearid");

    // Update database via ajax
    $.post("../tutorsAjax", {
        column: column,
        value: value,
        yearId: yearId
    }, function (result) {
        input.attr("data-original", value);
        button.addClass("d-none");
    });
});

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
        window.location.href = ("../tutors/" + year + "&" + status);
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
    window.location.href = ("../tutors/" + year + "&all");
});

// hide delete and import buttons on window click
$(window).on("click", function () {
    $(".delete").addClass("d-none");
    $(".import").addClass("d-none");
});

// show delete and import buttons on email click
$(".email").on("click", function (event) {

    // hide any open buttons
    $(".delete").addClass("d-none");
    $(".import").addClass("d-none");

    // show current buttons
    $(this).find(".delete").removeClass("d-none");
    $(this).find(".import").removeClass("d-none");

    // stop window event propagation to keep the window event listener from hiding the button again
    event.stopPropagation();
});

// event listener for delete button clicked
$(".delete").on("click", function () {
    let result = confirm("Are you sure you want to delete this user and all data associated with them?");
    if (result) {

        let user_id = $(this).data("userid");

        // ajax deletion
        $.post("../tutorsAjax", {
            delete: true,
            user_id: user_id
        });

        let year = $("#year-current").data("year");
        window.location.href = ("../tutors/" + year + "&" + status);
    }
});

// event listener to import the user to the current year on click
$(".import").on("click", function () {
    let result = confirm("Are you sure you want import this user to the current year?");
    if (result) {
        let user_id = $(this).data("userid");

        // ajax importing
        $.post("../tutorsAjax", {
            user_id: user_id
        });
        //hide Import button after displaying success message
        let results = confirm("Tutor imported successfully ");
        if (results) {
            $(".import").hide();
        }
    }
});

// event listener for setting the current year
$("#current-year").on("click", function () {

    let year = $("#year-current").data("year");

    $.post("../tutorsAjax", {
        current_year: year
    }, function () {
        // refresh data
        window.location.href = ("../tutors/" + year + "&" + status);
    });
});

// show/hide edit item buttons
$("#enable-edit").on("click", function () {
    $(".edit-item").toggleClass("d-none");
});

//event listener to save default email information
$("#save-default").on('click', function () {
    console.log("Did this work?");

    //getting th email and file values
    let newSubject = $("#email-subject").val();
    let newBody = $("#email-body").val();
    let fileChosen = $("#files").val();

    //file is been chosen
    if (fileChosen) {
        //ajax call for file upload
        let fd = new FormData(); //creating FormData object
        let files = $('#files')[0].files[0];
        //console.log(files);
        fd.append('file', files);
        //making ajax call to updated email json
        $.ajax({
            url: '../tutorsAjax',
            type: 'post',
            // subject: newSubject,
            // body: newBody,
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response != 0) {
                    // alert("Upload saved");
                    alert("File been upload");
                    $("#email-modal").modal('hide');
                    console.log(response);
                    // Show image preview
                    //$('#preview').append("<img src='" + response + "' width='100' height='100' style='display: inline-block;'>");
                } else {
                    alert('Changes not saved');
                }
            }
        });
    }
    $.post("../tutorsAjax", {
        subject: newSubject,
        body: newBody,
    }, function () {
        alert("Changes have been saved");
        $("#email-modal").modal('hide');
    });

});


//event listener uploading multiple files
$.fn.fileUploader = function (filesToUpload) {
    this.closest(".files").change(function (evt) { //  div class on change run function
        for (var i = 0; i < evt.target.files.length; i++) {
            filesToUpload.push(evt.target.files[i]);
            console.log(filesToUpload);
        }
        ;

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
$(".fileList").fileUploader(filesToUpload);

//delete file from ajax document
$(".removeFile").on("click", function () {
   let $fileToDelete = $(this).parent().attr("value");
   console.log($fileToDelete); //used for testing
    $.post("../tutorsAjax", {
       fileToDelete: $fileToDelete,
    }, function (response) {
        alert("File Deleted");
        console.log(response);
    });
});



