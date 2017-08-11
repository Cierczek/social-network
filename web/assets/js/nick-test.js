$(document).ready(function(){
    var home_url = window.location.protocol + "//" + window.location.host + "/social-network//web/app_dev.php";
    
    $(".nick-input").blur(function(){
        var nick = this.value;
        
        $.ajax({
            url: home_url+"/nick-test",
            data: {nick: nick},
            type: "POST",
            success: function(response){
                if(response === "used"){
                    $(".nick-input").css("border", "1px solid red");
                }
                else{
                    $(".nick-input").css("border", "1px solid green");
                }
            }
        });
    });  
});