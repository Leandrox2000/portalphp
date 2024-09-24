var isProcessingAjaxVinculoPai = false;

/**
 * Validações customizadas.
 */
jQuery.validator.addMethod(
    "seTipoMenuTemPai",
    function(value, element) {
        var tipoMenu = jQuery('input[name="tipoMenu"]:checked').val();

        // Se for item de nível 2 ou nível 3
        // e o vínculo não tiver sido selecionado
        if ((tipoMenu === 'n2' || tipoMenu === 'n3') && value === '') {
            return false;
        }

        return true;
    },
    "Este campo é requerido."
);

function ajaxVinculoPai()
{
    var obj = jQuery('input[name="tipoMenu"]:checked');
    var parentMenu = obj.attr('data-parent');
    var pId = jQuery('#id').val();
    var pSubsite = jQuery('#site').val();

    if (obj.val() !== 'n1' && obj.val() !== 'aux' && obj.val() !== 'lr') {
        jQuery('.vinculoPaiContainer').show();
        jQuery('#vinculoPai').html('<option value="">Carregando...</option>');
        jQuery.ajax('menu/ajaxVinculo', {
            type: 'POST',
            data: {
                tipoMenu: parentMenu,
                id: pId,
                subsite: pSubsite
            },
            beforeSend: function() {
                jQuery('input[name="categoria"]').attr('disabled', 'disabled');
                jQuery.uniform.update('select');
            },
            success: function(data) {
                isProcessingAjaxVinculoPai = false;
                var options = '<option value="">Selecione</option>';

                for (x in data.values) {
                    row = data.values[x];
                    options += '<option value="' + row.id + '">' + row.titulo + '</option>'
                }

                jQuery('#vinculoPai').html(options);
                jQuery('input[name="categoria"]').removeAttr('disabled');
                jQuery.uniform.update('select');
            }
        });
    } else {
        isProcessingAjaxVinculoPai = false;
        jQuery('.vinculoPaiContainer').hide();
    }
}

jQuery(document).ready(function() {
    if (jQuery('#funcionalidadeMenu').val() != '') {
        jQuery('#urlExterna').attr('disabled', 'disabled');
        jQuery('#urlInterna').attr('disabled', 'disabled');
    } else {
        jQuery('#urlExterna').removeAttr('disabled');
        jQuery('#urlInterna').removeAttr('disabled');
    }
    if (jQuery('#urlExterna').val() !== '') {
        jQuery('#funcionalidadeMenu').attr('disabled', 'disabled');
        jQuery('#urlInterna').attr('disabled', 'disabled');
    } else {
        jQuery('#funcionalidadeMenu').removeAttr('disabled');
        jQuery('#urlInterna').removeAttr('disabled');
    }

    if (jQuery('#funcionalidadeMenu option:selected').text() == 'Novas Páginas'
        || jQuery('#funcionalidadeMenu option:selected').text() == 'Páginas Estáticas') {
        jQuery('#sel1pag').show();
    }
    
    if (jQuery('#funcionalidadeMenu option:selected').text() == 'Subsite') {
        jQuery('#sel2pag').show();
    }
    
    jQuery('#urlExterna').keyup(function(){
        if (jQuery(this).val() !== '') {
            jQuery('#funcionalidadeMenu').attr('disabled', 'disabled');
            jQuery('#urlInterna').attr('disabled', 'disabled');
        } else {
            jQuery('#funcionalidadeMenu').removeAttr('disabled');
            jQuery('#urlInterna').removeAttr('disabled');
        }
        
        jQuery.uniform.update('select');
    });
    jQuery('input:checkbox, input:radio, input:file, select').uniform();
    jQuery("#frm").validate({
        rules: {
            dataInicial: { required: true, brazilianDate: true},
            dataFinal: { required: false, brazilianDate: true},
            horaInicial: { required: true, time: true },
            horaFinal: { time: true },
            titulo: { required: true },
            tipoMenu: { required: true },
            vinculoPai: { seTipoMenuTemPai: true },
            urlExterna: { required: false },
            site: { required: true }
        },
        errorPlacement: function(error, element) {
            // Se for um SELECT (fix para UniformJS)
            if (element.is('select') || element.is('input[type="radio"]')) {
                element.parents('.field:first').append(error);
            } else {
                error.insertAfter(element);
            }

        },
        submitHandler: function(form) {
            jQuery('input, select').removeClass('error');
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

    jQuery("#cep").mask(
            "99999-999",
            {completed: function() {
                    jQuery("#ceploading").show();
                    jQuery.getScript('http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep=' + this.val(), function(data) {
                        jQuery('#estado').val(unescape(resultadoCEP.uf));
                        jQuery.uniform.update();
                        jQuery('#cidade').val(unescape(resultadoCEP.cidade));
                        jQuery('#bairro').val(unescape(resultadoCEP.bairro));
                        jQuery('#endereco').val(unescape(resultadoCEP.tipo_logradouro) + ' ' + unescape(resultadoCEP.logradouro));
                        jQuery("#ceploading").hide();

                    });
                }}
    );
    
    jQuery('input[name="tipoMenu"], #site').on('change', function(){
        if (isProcessingAjaxVinculoPai === false) {
            isProcessingAjaxVinculoPai = true;
            ajaxVinculoPai();
        } else {
            return;
        }
    });
    jQuery('#funcionalidadeMenu').on('change', function(){
        if (jQuery(this).val() != '') {
            jQuery('#urlExterna').attr('disabled', 'disabled');
            jQuery('#urlInterna').attr('disabled', 'disabled');
        } else {
            jQuery('#urlExterna').removeAttr('disabled');
            jQuery('#urlInterna').removeAttr('disabled');
        }

        if (jQuery(this).find(':selected').text() == 'Novas Páginas'
            || jQuery(this).find(':selected').text() == 'Páginas Estáticas') {
            jQuery('#sel1pag').show();
        } else {
            jQuery('#sel1pag').hide();
            jQuery('#idEntidade').val('');
            jQuery('#paginaSelecionada').html('');
        }
        
        if (jQuery(this).find(':selected').text() == 'Subsite') {
            jQuery('#sel2pag').show();
        } else {
            jQuery('#sel2pag').hide();
            jQuery('#idEntidade').val('');
            jQuery('#subsiteSelecionado').html('');
        }
        
    });
    jQuery('#sel1pag .btn').click(function(){
        abrirPaginas();
        return false;
    });
    
    jQuery('#sel2pag .btn').click(function(){
        abrirSubsites();
        return false;
    });
    
});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
                location.href = "menu/lista";
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

