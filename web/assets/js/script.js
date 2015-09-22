$(document).ready(function(){
    $(".delete").click(function(e){
        e.preventDefault();
        if(confirm("Czy napewno chcesz usunąć?")){
            window.location.href = $(this).attr('href');
        }else{
            return false;
        }
    });
});