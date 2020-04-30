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