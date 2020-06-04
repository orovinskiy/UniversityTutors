/**
 * JS functionality for the itemEdit page.
 *
 * @author Keller Flint
 */

// Check if delete button was pressed FOR STATES
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


// Check if delete button was pressed FOR ITEMS
let deleteClickedItem = false;
$("#delete").on("click", function () {
    deleteClickedItem = true;
});

// Confirm user wants to delete state
$("#edit-item").submit(function (e) {
    let title = $("#title").html();
    if (deleteClickedItem) {
        let result = confirm("WARNING: Deleting this column will remove all data for " + title + " for ALL users in ALL years. Are you sure you want to continue?");
        if (!result) {
            e.preventDefault();
        }
    }
});

// toggle display of add new state
$("#add-new-state").hide();
$("#add-toggle").on("click", function () {
    $("#add-new-state").show();
    $("#add-toggle").hide();
});