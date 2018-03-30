$(document).ready(function(){
    var home_url = window.location.protocol + "//" + window.location.host + "/web/app_dev.php";
    
    $(".form-email").blur(function(){
        var email = this.value;
        
        $.ajax({
            url: home_url+"/email-test",
            data: {email: email},
            type: "POST",
            success: function(response){
                if(response === "used"){
                    $(".form-email").css("border", "1px solid red");
                }
                else{
                    $(".form-email").css("border", "1px solid green");
                }
            }
        });
    }); 
});