/**
 * @author Oleg
 * @version 1.0
 * Logic for the school view as well as the validation
 * for the page
 **/
let current = 5;
let prev = 0;
let max = 5;

//this is to go down the school list
$('#down').on('click',function(){
    $('#schoolHeader').html('');
    $('#schoolJobs').html('');
    $('#addJob').attr('hidden',true);

    for(let i = current; i < max+6; i++){
        let next = i+1;
        if($('#'+next).length !== 0){
            current++;
            $('#'+next).attr('hidden',false);
            $('#'+(prev)).attr('hidden',true);
            prev++;
        }
        else{
            $(this).attr('hidden',true);
            $('#up').attr('hidden',false);
            break;
        }
    }
    max = current;
    if($('#'+(current+1)).length === 0){
        $(this).attr('hidden',true);
    }
    $('#up').attr('hidden',false);
});

//this is to go up the school list
$('#up').on('click',function(){
    $('#schoolHeader').html('');
    $('#schoolJobs').html('');
    $('#addJob').attr('hidden',true);

    for(let i = current; i > max-6; i--){
        let next = i-6;
        if($('#'+next).length !== 0){
            current--;
            $('#'+next).attr('hidden',false);
            $('#'+i).attr('hidden',true);
            prev--;
        }
        else{
            $(this).attr('hidden',true);
            $('#down').attr('hidden',false);
            break;
        }
    }
    max = current;
    if($('#'+(max-6)).length === 0){
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
    let school = $(this).data('name');
    let input =  $('#addJob');

    input.data('school',school);
    input.attr('hidden',false);
    $('#schoolHeader').html(school);

    let allData = new FormData();
    allData.append('school',$(this).data('id'));

    $.ajax({
        url: '/getJobRoles',
        type: 'post',
        data: allData,
        contentType: false,
        processData: false,
        success: function(response){
            $('#schoolJobs').html(response);
        }
    });
    console.log($('#addJob').data('school'));
    //$.post()
});

function validSchool(school) {
    if(school.trim() !== ''){
        return true;
    }
    return false
}