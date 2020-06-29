/**
 * @author Oleg
 * @version 1.0
 * Logic for the school view as well as the validation
 * for the page
 **/
let position = 0;

//this is to go down the school list
$('#down').on('click',function(){
    $('#addJob').attr('hidden',true);
    for(let i = position; i < position+6; i++){
        let next = i+6;
        if($('#'+next).length !== 0){
            $('#'+next).attr('hidden',false);
            $('#'+i).attr('hidden',true);
        }
        else{
            $(this).attr('hidden',true);
            $('#up').attr('hidden',false);
        }
    }
    position += 6;
    if($('#'+(position+6)).length === 0){
        $(this).attr('hidden',true);
    }
    $('#up').attr('hidden',false);
});

//this is to go up the school list
$('#up').on('click',function(){
    $('#addJob').attr('hidden',true);
    for(let i = position; i > position-6; i--){
        let next = i-6;
        if($('#'+next).length !== 0){
            $('#'+next).attr('hidden',false);
            $('#'+i).attr('hidden',true);
        }
        else{
            $(this).attr('hidden',true);
            $('#down').attr('hidden',false);
        }
    }
    position -= 6;
    if($('#'+(position-6)).length === 0){
        $(this).attr('hidden',true);
    }
    $('#down').attr('hidden',false);
});

//this will add a school
$('#button-addSchool').on('click',function(){
    let school = $('#school').val();
    if(validSchool(school)){
        $.post('/addSchool',{
            school: school.toLowerCase().trim(),
        })
            .done(function(data){
            if(data==="false"){
                $('#school-err').html('*School already exists');
            }
            else{
                $('#school-err').html('');
                window.location.reload();
            }
        });
    }
    else {
        $('#school-err').html('*Please enter a valid school')
    }
});

$('body').on('click','.schools',function(){
    let school = $(this).data('id');
    let input =  $('#addJob');

    input.data('school',school);
    input.attr('hidden',false);
    console.log($('#addJob').data('school'));
    //$.post()
});

function validSchool(school) {
    if(school.trim() !== ''){
        return true;
    }
    return false
}