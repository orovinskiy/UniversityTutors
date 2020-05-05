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

// Event listener for updating checkboxes
$(".checkbox-big").on("click", function () {

    // Get data for update
    let column = $(this).data("column");
    let value = $(this).is(":checked") ? 1 : 0;
    let yearId = $(this).data("yearid");

    // Update database via ajax
    $.post("/tutorsAjax", {
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
    $.post("/tutorsAjax", {
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
    $.post("/tutorsAjax", {
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

    // Update database via ajax
    $.post("/tutorsAjax", {
        email: email,
        year: year
    }, function (result) {
        alert("Email was sent (in theory). You will need to refresh to page to see the new tutor in the table. [DEBUG] user_id = " + result);

        //refresh page to load new user into table
        let year = $("#year-current").data("year");
        window.location.href = ("/tutors/" + year);
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
    window.location.href = ("/tutors/" + year);
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
        $.post("/tutorsAjax", {
            delete: true,
            user_id: user_id
        });

        let year = $("#year-current").data("year");
        window.location.href = ("/tutors/" + year);
    }
});

// event listener to import the user to the current year on click
$(".import").on("click", function () {
    let result = confirm("Are you sure you want import this user to the current year?");
    if (result) {
        let user_id = $(this).data("userid");

        // ajax deletion
        $.post("/tutorsAjax", {
            user_id: user_id
        });

        let year = $("#year-current").data("year");
        window.location.href = ("/tutors/" + year);
    }
});

// event listener for setting the current year
$("#current-year").on("click", function () {

    let year = $("#year-current").data("year");

    $.post("/tutorsAjax", {
        current_year: year
    });

    // refresh data
    window.location.href = ("/tutors/" + year);
});