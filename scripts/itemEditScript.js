/**
 * JS functionality for the itemEdit page.
 *
 * @author Keller Flint
 */

// Check if delete button was pressed
let deleteClicked = false;
$(".delete").on("click", function () {
    deleteClicked = true;
});

// Confirm user wants to delete state
$(".edit-state").submit(function (e) {
    let title = $("#title").html();
    if (deleteClicked) {
        let result = confirm("WARNING: Deleting this state will reset " + title + " to the default state for ALL users. Are you sure you want to continue?");
        if (!result) {
            e.preventDefault();
        }
    }
});