$(".get").on("click",function(){
    console.log(this.value);

    let $sideCheck = $("#"+this.value);
    if($sideCheck.attr("checked")){
        $sideCheck.attr("checked",false);
    }
    else{
        $sideCheck.attr("checked",true)
    }
});

$(".side").on("click",function(){
    console.log(this.value);

    let $box = $("#"+this.value+"box");
    if($box.attr("checked")){
        $box.attr("checked",false);
    }
    else{
        $box.attr("checked",true)
    }
});