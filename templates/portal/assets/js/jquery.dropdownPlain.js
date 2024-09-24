$(function(){

    $("ul.dropdown li").hover(function(){
    
        $(this).addClass("hover");
        $("ul:first",this).css("visibility", "visible");
    
    }, function(){
    
        $(this).removeClass("hover");
        $("ul:first",this).css("visibility", "hidden");
    
    });

    $("ul.dropdown li a").focus(function(){
    	$(this).parent().addClass("hover");
    	if($(this).parent().find("ul:first")){
    		$(this).parent().find("ul:first").css("visibility", "visible");
    		$(this).parent().find(".sub_menu a:last").focusout(function(){
    			$(this).parent().parent().parent().removeClass("hover");
    			$(this).parent().parent().parent().find("ul:first").css("visibility", "hidden");
    		});
    	}
    	$("ul.dropdown li a").focusout(function(){
			$(this).parent().removeClass("hover");
		}); 	
    });    
    
//    $("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");

});