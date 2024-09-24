jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, select').uniform();

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            categoria: {required: true},
            pergunta: {required: true},
            resposta: {required: true},
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: { time: true }
        },
        messages: {
            categoria: {required: "Este campo é requerido"},
            pergunta: {required: "Este campo é requerido"},
            resposta: {required: "Este campo é requerido"},
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "categoria") {
                element.parent().parent().append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
         location.href = "pergunta/lista";       
            }});
        
    } else {
        jQuery('#submitloading').fadeOut(20, function() {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}


function abrirCategorias()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "pergunta/categorias", width: "75%", height: "90%", onComplete: carregaCategorias, onClosed: carregaCategoriasSelect});
}

function carregaCategoriasSelect()
{
    jQuery.uniform.update('#categoria');
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "pergunta/getCategorias", function(data) {
        jQuery("#categoria").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}