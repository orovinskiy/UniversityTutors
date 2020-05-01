/**
 * Client-side validation
 * @author Laxmi and keller
 */

console.log("loaded client validation");
//creating an array for validations
let validations = [
    ["val-empty", isEmpty],
    ["val-lessThan255", lessThan255],
    ["val-lessThan10", lessThan10],
    ["val-lessThan9", lessThan9],
    ["val-hasSpaces", hasSpaces],
];

/**
 * Check to see if the text box is left empty
 * @param input user's input
 * @param valClass  array
 * @returns true if the text box is left empty
 */
function isEmpty(input, valClass) {
    let isValid = false;
    if (input.val().trim() === "") {
        isValid = true;
    }
    toggleErrors(input, valClass, !isValid, "Cannot be empty.");
    return isValid;
}

/**
 * Check to see if user input is less than 255 characters
 * @param input user's input
 * @param valClass from array
 * @returns true if the input is less than 255 characters
 */
function lessThan255(input, valClass) {
    $('.err').remove();
    let isValid = false;
    if (input.val().length <= 255 && input.val().trim() != "") {
        isValid = true;
    }
    toggleErrors(input,valClass, isValid, "Cannot be empty and longer than 255 characters.");
    return isValid;
}

/**
 * Check to see if user input is less than 10 digits
 * @param input user's input
 * @param valClass from array
 * @returns true if user's input is less than 10 digits
 */
function lessThan10(input, valClass) {
    $('.err').remove();
    let isValid = false;
    if (input.val().length == 10 && input.val().trim() != "") {
        isValid = true;
    }
    toggleErrors(input, valClass, isValid, "Must be 10 digits");
    return isValid;
}

/**
 * Check to see if user input is less then 9 digits
 * @param input user's input
 * @param valClass from array
 * @returns true if user's input is less then 9 digits
 */
function lessThan9(input, valClass) {
    $('.err').remove();
    let isValid = false;
    if (input.val().length == 9 && input.val().trim()!=="") {
        isValid = true;
    }
    toggleErrors(input, valClass, isValid, "Must be 9 digits");
    return isValid;
}

/**
 * Check to see if user input spaces
 * @param input user's input
 * @param valClass from array
 * @returns true if user's input does not have spaces
 */
function hasSpaces(input, valClass) {
    $('.err').remove();
    let isValid = false;
    if (/\s/.test(input.val())) {
        isValid = true;
    }
    toggleErrors(input, valClass, !isValid, "Cannot contain whitespace.");
    return isValid;

}
    /* --- Helper functions --- */

// sets all of the input and submit event listeners to validate the classes given in validations
    for (let i = 0; i < validations.length; i++) {
        // validation on input
        $("." + validations[i][0]).find(".input").on("input focus blur", function () {
            validations[i][1]($(this), validations[i][0]);
        });
    }

/**
 * Append/ remove the error messages
 * @param object
 * @param valClass from array
 * @param isValid boolean
 * @param message Errors messages
 */
    function toggleErrors(object, valClass, isValid, message) {
        if (!isValid) {
            if ($(object).parent().find(".errors").find("." + valClass + "-error").length == 0) {
                $(object).parent().find(".errors").append("<div class='error " + valClass + "-error'>" + message + "</div>");
                console.log("appended");
            }
        } else {
            $(object).parent().find(".errors").find("." + valClass + "-error").remove();
            console.log("removed");
        }
    }



