/**
 * Client-side validation
 * @author Laxmi and keller
 */

console.log("loaded client validation");


//get checkbox using it's id
let bioTextArea = document.getElementsByClassName("checkBio")[0];
//onclick call a function
bioTextArea.onclick = toggleBioTextArea;

/**
 * this function displays/hide text area for bio when checked/unchecked
 */
function toggleBioTextArea() {
    let getBioTextArea = document.getElementsByClassName("bio-block")[0];
    if (bioTextArea.checked) {
        getBioTextArea.style.display = "block";
    } else {
        getBioTextArea.style.display = "none";
        let clearText = document.getElementsByClassName('clearText')[0].value;
        console.log(clearText);
        if(clearText.length<100){
            document.getElementsByClassName('clearText')[0].value = "";
        }
    }
}

//Auto formatting for phone number
$('#phone').keyup(function () {
    let valPhone = this.value.replace(/\D/g, '');
    let newValPhone = '';
    if (valPhone.length > 4) {
        this.value = valPhone;
    }
    if ((valPhone.length > 3) && (valPhone.length < 7)) {
        newValPhone += '(' + valPhone.substr(0, 3) + ') ';
        valPhone = valPhone.substr(3);
    }
    if (valPhone.length > 6) {
        newValPhone += '(' + valPhone.substr(0, 3) + ') ';
        newValPhone += valPhone.substr(3, 3) + '-';
        valPhone = valPhone.substr(6);
    }
    newValPhone += valPhone;
    this.value = newValPhone.substring(0, 14);
});

////Auto formatting for SSN
$('#ssn').keyup(function () {
    let val = this.value.replace(/\D/g, '');
    let newVal = '';
    if (val.length > 4) {
        this.value = val;
    }
    if ((val.length > 3) && (val.length < 6)) {
        newVal += val.substr(0, 3) + '-';
        val = val.substr(3);
    }
    if (val.length > 5) {
        newVal += val.substr(0, 3) + '-';
        newVal += val.substr(3, 2) + '-';
        val = val.substr(5);
    }
    newVal += val;
    this.value = newVal.substring(0, 11);
});

/**
 * function to display image right before submitting form
 * @param file input file
 * @author laxmi
 */
function readURL(input) {
    console.log("reading image");
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#img").change(function () {
    readURL(this);
});
//creating an array for validations
let validations = [
    ["val-empty", isEmpty],
    ["val-lessThan255", lessThan255],
    ["val-lessThan14", lessThan14],
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
    if (input.val().length <= 255) {
        isValid = true;
    } else {
        toggleErrors(input, valClass, isValid, "Can't be longer than 255 characters.");
    }
    if (input.val().length != 0) {
        isValid = true;
    } else {
        toggleErrors(input, valClass, isValid, "Cannot be empty");
    }
    return isValid;
}

/**
 * Check to see if user input valid phone and ssn
 * @param input user's input
 * @param valClass from array
 * @returns true if user's input valid phone and ssn
 */
function lessThan14(input, valClass) {
    $('.err').remove();
    let isValid = false;
    if (input.val().trim() != "") {
        isValid = true;
    }
    toggleErrors(input, valClass, isValid, "Must be valid");
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


//MASKING SSN
//
// $('.ssnInput').on('keydown keyup mousedown mouseup', function () {
//     var res = this.value, //grabs the value
//         len = res.length, //grabs the length
//         max = 9, //sets a max chars
//         stars = len > 0 ? len > 1 ? len > 2 ? len > 3 ? len > 4 ?
//             'XXX-XX-' : 'XXX-X' : 'XXX-' : 'XX' : 'X' : '', //this provides the masking and formatting
//         result = stars + res.substring(5); //this is the result
//     $(this).attr('maxlength', max); //setting the max length
//     $('.output').val(result);
//     console.log("this is it"+$('.output').val());
//     console.log("this is actual user input to pass " + $('.ssnInput').val());
//     // console.log("clear");
//     // console.log($('.ssnInput').val());
//     // $('.ssnInput').val(result); //spits the value into the input
// });


