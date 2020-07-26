/**
 * @author Oleg
 * @version 1.0
 * Logic for the school view as well as the validation
 * for the page
 **/
let current = 5;
let prev = 0;
let max = 5;
$('.delete').hide();

//this is to go down the school list
$('#down').on('click',function(){
    scrollers();

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
    scrollers();

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
    resetErrors();
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

//This is to get job roles for a school
$('body').on('click','.schools',function(){
    resetErrors();
    let school = $(this).data('name');
    let input =  $('#addJob');

    input.data('id',$(this).data('id'));
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
});

//This adds a job role
$('body').on('click','#button-addJob',function(){
    resetErrors();
    let input = $('#addJob');

    if(validSchool($('#addJobInput').val())) {
        $.post('/insertJobRoles', {
            schoolId: input.data('id'),
            jobName: $('#addJobInput').val().trim()
        })
            .done(function(data){
                $('#schoolJobs').append(data);
            });
    }
    else{
        $('#job-err').html('*Input box can\'t be empty');
    }
});

$('body').on('click','.fa-sort-down',function (){
    schoolDelete($(this),'fa-sort-up', 'fa-sort-down').show(500);
});

$('body').on('click','.fa-sort-up',function (){
    schoolDelete($(this),'fa-sort-down','fa-sort-up').hide(500);
});

$('body').on('click','.delete',function(){
    if(confirm('By deleting this school will delete all the job roles that correspond. Do you wish to continue?')){
        $.post('deleteSchool',{
            schoolId:  parseInt($(this).parent().data('id')),
        }).done(function(){
            window.location.reload();
        });
    }
});

function schoolDelete(selector, firstClass, secondClass){
    let div = selector.parents(':eq(1)');
    div = div.children('button');
    selector.addClass(firstClass);
    selector.removeClass(secondClass);
    return div;

}


function validSchool(school) {
    if(school.trim() !== ''){
        return true;
    }
    return false
}

function resetErrors(){
    $('#job-err').html('');
    $('#school-err').html('');
}

function scrollers(){
    resetErrors();
    $('.delete').hide();
    $('#schoolHeader').html('');
    $('#schoolJobs').html('');
    $('#addJob').attr('hidden',true);

    let icon = $('.fa-sort-up');
    icon.addClass('fa-sort-down');
    icon.removeClass('fa-sort-up');
}