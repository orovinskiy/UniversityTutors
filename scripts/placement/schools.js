/**
 * @author Oleg
 * @version 1.0
 * Logic for the school view as well as the validation
 * for the page
 **/
let position = 0;

$('#down').on('click',function(){
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
            }
        });
    }
    else {
        $('#school-err').html('*Please enter a valid school')
    }
});

function validSchool(school) {
    if(school.trim() !== ''){
        return true;
    }
    return false
}