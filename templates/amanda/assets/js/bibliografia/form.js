jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();
    
    /*************** DESATIVA CAMPOS ***************/
    // Desativa campo de título
    jQuery('#titulo').attr('disabled', 'disabled');
    // Desativa campo de imagem
    jQuery('a[href="javascript:abrirImagemRadio(0)"]').click(function(e){
        e.preventDefault();
    }).css('background', '#fff').css('color', '#ddd');
    /*************** DESATIVA CAMPOS ***************/

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
//            titulo: {required: true},
            dataInicial: {required: true},
            horaInicial: {required: true, time: true},
            horaFinal: { time: true },
            conteudo: { required: true }
        },
        messages: {
//            titulo: {required: "Este campo é requerido"},
            dataInicial: {required: "Este campo é requerido"},
            horaInicial: {required: "Este campo é requerido"},
            conteudo: { required: "Este campo é requerido" }
        },
        submitHandler: function(form) {
//            jQuery('#conteudo').val(CKEDITOR.instances.conteudo.getData());
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');

    });


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "bibliografia/lista";
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


function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();
    jQuery('#imagemBanco').val(v);

    if (v !== "" && v !== undefined) {
        jQuery.get("publicacao/getHtmlImagens/" + v).done(
                function(data) {
                    jQuery('#imgSelecionadas').html(data);
                });
    }
}

function excluirImagem(id)
{
    jQuery('#imagemBanco').val("");
    jQuery('#img' + id).remove();

}