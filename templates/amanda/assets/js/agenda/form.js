jQuery(document).ready(function() {
    
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

     //if (jQuery('#compartilhado').val() == 1) {
     //   jQuery('div').find('input').prop('readonly', true);
     //   jQuery('#estado').css({"display":"none"});
     //}

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            titulo:             { required: true },
            dataPeriodoInicial: { required: true },
            horaPeriodoInicial: { required: true, time: true },
            dataPeriodoFinal:   { required: true },
            horaPeriodoFinal:   { required: true, time: true },
            //telefone:           { required: true },
            //celular:            { required: true },
            estado:             { required: true },
            //cep:                { required: true },
//            cidade:             { required: true },
//            local:              { required: true },
            //email:              { required: true, email: true },
            dataInicial:        { required: true },
            horaInicial:        { required: true, time: true },
            descricao: {
                required: function() {
                    CKEDITOR.instances.descricao.updateElement();
                }
            },
            'sites[]':          { required: true }
        },
        submitHandler: function(form) {
            jQuery('#descricao').val(CKEDITOR.instances.descricao.getData());
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });
    
    var position = window.location.href.lastIndexOf("/");
    var id = window.location.href.slice(position + 1);
    //var ckeditor = CKEDITOR.instances;
    console.log(ckeditor);
    if(!isNaN(id)) {
        var bt = jQuery(".break button").attr("disabled", true);
        jQuery.ajax({
            url: 'agenda/validaSubsiteVinculadoAgenda',
            type: 'post',
            dataType: 'json',
            data: {id: id},

            success: function (data) {
                bt.attr("disabled", false);
                if (data.permissao == false) {
                    jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
                    jQuery(".field input").off().attr("readonly", "readonly");
                    //ckeditor.descricao.setReadOnly(true);
                    jQuery('#estado').css({"display":"none"});
                    jQuery("#frm").attr("action", "agenda/compartilhar");
                    jQuery("#sites").rules("remove", "required");
                }
            }
        });
    }

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');
        validaPeriodo('dataPeriodoInicial', 'dataPeriodoFinal', 'horaPeriodoInicial', 'horaPeriodoFinal');
    });
   
    jQuery("#cep").mask(
        "99999-999",
        {completed:function(){
            jQuery("#ceploading").show();
            jQuery.getScript('http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep='+this.val(), function(data){
                jQuery('#estado').val(unescape(resultadoCEP.uf));
                jQuery.uniform.update();
                jQuery('#cidade').val(unescape(resultadoCEP.cidade));
                jQuery('#bairro').val(unescape(resultadoCEP.bairro));
                jQuery('#endereco').val(unescape(resultadoCEP.tipo_logradouro)+' '+unescape(resultadoCEP.logradouro));
//                if(jQuery('#estado').val()==''){
//                    jAlert('CEP Inválido', 'Alerta');
//                    jQuery("#cep").val('');
//                }
                jQuery("#ceploading").hide();
                
            })
        }}
    );


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "agenda/lista";
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

