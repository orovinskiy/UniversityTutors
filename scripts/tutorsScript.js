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
    let itemId = $(this).data("item-id");
    let tutorYearId = $(this).data("tutor-year-id");
    let stateOrder = $(this).is(":checked") ? 2 : 1;

    // Update database
    updateCheckbox(itemId, tutorYearId, stateOrder);

});

// Event listener for updating selects
$(".tutor-select").on("change", function () {

    // Get data for update
    let itemId = $(this).data("item-id");
    let tutorYearId = $(this).data("tutor-year-id");
    let stateId = $(this).val();

    // Update database
    updateSelect(itemId, tutorYearId, stateId);

});

/**
 * Updates selects via ajax
 *
 * @param itemId The id of the item being updated
 * @param tutorYearId The id of the tutor year being updated
 * @param stateId The id of the state the item is being updated to
 * @author Keller Flint
 */
function updateSelect(itemId, tutorYearId, stateId) {
    $.post("../tutorsAjax", {
        itemId: itemId,
        tutorYearId: tutorYearId,
        stateId: stateId
    });
}

/**
 * Updates checkboxes via ajax
 *
 * @param itemId The id of the item being updated
 * @param tutorYearId The id of the tutor year being updated
 * @param stateOrder The order of the state the item is being updated to
 * @author Keller Flint
 */
function updateCheckbox(itemId, tutorYearId, stateOrder) {
    $.post("../tutorsAjax", {
        itemId: itemId,
        tutorYearId: tutorYearId,
        stateOrder: stateOrder
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

// Event listener for delete button clicked
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
        window.location.href = ("../tutors/" + year);
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
        window.location.href = ("../tutors/" + year);
    });
});

// show/hide edit item buttons
$("#enable-edit").on("click", function () {
    $(".edit-item").toggleClass("d-none");
});

//event listener to save default email information
$("#save-default").on('click', function () {
    console.log("Did this work?");
    let newSubject = $("#email-subject").val();
    let newBody = $("#email-body").val();
    let att = $("#attachments").val();
    console.log(att);
    //making ajax call to updated email json
    $.post("../tutorsAjax", {
        subject: newSubject,
        body: newBody,
        attachment:att
    }, function () {

        alert("Changes have been saved");

        $("#email-modal").modal('hide');
        alert("Changes have been saved");
        $("#email-modal").modal('hide');
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
            doneArray.push($(this).data("is-done"));
        });
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
            doneArray.push($(this).data("is-done"));
        });
        if (!doneArray.includes(1)) {
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
