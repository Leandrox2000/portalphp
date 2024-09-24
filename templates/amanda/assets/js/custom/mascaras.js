/**
 * Várias máscaras
 * @author Guilherme Tutilo
 */

//mascara para telefone
jQuery(document).on("focus", ".telefone", function(event)
{ 
    var target, phone, element;
    target = (event.currentTarget) ? event.currentTarget : event.srcElement;
    phone = target.value.replace(/\D/g, '');
    element = jQuery(target);
    element.unmask();
    if (phone.length > 10) {
        element.mask("(99) 99999-999?9");
    } else {
        element.mask("(99) 9999-9999?9");
    }
});

jQuery(document).on("focus", ".dinheiro2", function(event)
{ 
    var target, element;
    target = (event.currentTarget) ? event.currentTarget : event.srcElement;
    element = jQuery(target);
    element.unmask();
    element.priceFormat(
    {
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.'
    });
});

jQuery(document).on("focus", ".cep2", function(event)
{ 
    var target, element;
    target = (event.currentTarget) ? event.currentTarget : event.srcElement;
    element = jQuery(target);
    element.unmask();
    element.mask("99999-999");
});

jQuery(document).on("focus", ".cpf2", function(event)
{ 
    var target, element;
    target = (event.currentTarget) ? event.currentTarget : event.srcElement;
    element = jQuery(target);
    element.unmask();
    element.mask("999.999.999-99");
});

jQuery(document).on("focus", ".cnpj2", function(event)
{ 
    var target, element;
    target = (event.currentTarget) ? event.currentTarget : event.srcElement;
    element = jQuery(target);
    element.unmask();
    element.mask("99.999.999/9999-99");
});

jQuery(document).ready(function() 
{
    //mascara para data
    jQuery(".data").datepicker().mask("99/99/9999"); 
    
    //mascara para cpf
    jQuery(".cpf").mask("999.999.999-99");
    
    //mascara para cnpj
    jQuery(".cnpj").mask("99.999.999/9999-99");
    
    //mascara para cep
    jQuery(".cep").mask("99999-999");
    
    //mascara para somente número
    jQuery(".sonumero").keyup(function (e) 
    {
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });
    
    //mascara para dinheiro
    jQuery(".dinheiro").priceFormat(
    {
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.'
    });
});