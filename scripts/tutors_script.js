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

// Event listener for updating inputs
$(".placement").on("blur", function () {

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
        alert("Email was sent. You will need to refresh to page to see the new tutor in the table. [DEBUG] user_id = " + result);
    });
});