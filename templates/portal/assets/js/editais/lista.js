/**
 * Atualiza um container via ajax 
 */
function setContainer(container, data){
    
    jQuery('select', '#form-editais').each(function(id, el){
        
        var select = jQuery(el);
        var value = select.val();
        
        if(value) {
            
            data[select.attr('name')] = value;
        }        
    });
        
    var url = window.location.pathname.replace(/\/$/, '') + '/paginar';
    
    jQuery.ajax({

        url:url, 
        method:'GET',
        dataType:'html',
        context:container,
        data:data,
        success: function(result){

            container.html(result);
        }
    });
}

/**
 * Atualiza todos os containers 
 */
function setContainers(){
    
    jQuery.each(jQuery('div[data-paginar]'), function(e){
        
        var container = jQuery(this);
        
        setContainer(container, {
            'status':container.attr('data-paginar')
        }); 
    });
}

jQuery(document).ready(function(){
    
    jQuery('#categoria').change(setContainers);
    
    jQuery('#status').change(function(){
        jQuery('#form-editais').submit();
    });  
    
    jQuery('div[data-paginar]').delegate('a[data-limit]', 'click', function(e){
        
        e.preventDefault();

        var link = jQuery(this);
        var container = link.parents('div[data-paginar]:first');
        
        
        setContainer(container, {
            'limit':link.attr('data-limit'),
            'status':container.attr('data-paginar')
        });         
    });
    
    setContainers();
});




