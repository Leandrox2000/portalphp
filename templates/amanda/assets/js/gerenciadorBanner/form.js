/**
 * Funcionalidade
 */
var campoFuncionalidadeMenu;
/**
 * Campos #url e #abrirEm
 */
var camposDeUrl;
/**
 * Tem link ou não
 */
var campoTemLink;
/**
 * URL
 */
var campoUrl;
/**
 * Abrir na "mesma janela" ou em "nova janela"
 */
var campoAbrirEm;
/**
 * ID da entidade (Funcionalidade)
 */
var campoIdEntidade;
/**
 * Container com o botão para selecionar "Novas páginas"
 */
var seletorNovasPaginas;
/**
 * Exibe a "Nova página" selecionada
 */
var paginaSelecionada;

jQuery(document).ready(function(){
    campoFuncionalidadeMenu = jQuery('#funcionalidadeMenu');
    camposDeUrl = jQuery('#frm .par.url-extra-form input');
    campoTemLink = jQuery('#temLink');
    campoTemLink = jQuery('#temLink');
    campoUrl = jQuery('#url');
    campoAbrirEm = jQuery('input[name="abrirEm"]');
    campoIdEntidade = jQuery('#idEntidade');
    seletorNovasPaginas = jQuery('#sel1pag');
    paginaSelecionada = jQuery('#paginaSelecionada');
    categoria = jQuery('#categoria');
    id = jQuery('#id');
    
});

function desativarCampos()
{
    camposDeUrl.attr('disabled', 'disabled');
    campoFuncionalidadeMenu.attr('disabled', 'disabled');

    if (campoFuncionalidadeMenu.val() !== '') {
        campoFuncionalidadeMenu.removeAttr('disabled', 'disabled');
        seletorNovasPaginas.show();
    }

    if (campoUrl.val() !== '') {
        camposDeUrl.removeAttr('disabled');
    }

    jQuery.uniform.update();
}

function funcionalidadeMenuSelecionada(elemento)
{
    if (elemento.val() !== '' && campoTemLink.val() === '1') {
        campoUrl.attr('disabled', 'disabled');
        //campoAbrirEm.attr('disabled', 'disabled');
    } else {
        campoUrl.removeAttr('disabled');
        //campoAbrirEm.removeAttr('disabled');
    }

    var labelOpcaoSelecionada = elemento.find(':selected').text();
    if (labelOpcaoSelecionada === 'Novas Páginas') {
        seletorNovasPaginas.show();
    } else {
        seletorNovasPaginas.hide();
        campoIdEntidade.val('');
        paginaSelecionada.html('');
    }
}

function temLinkSelecionado(elemento)
{
    if (elemento.is(':checked')) {
        camposDeUrl.removeAttr('disabled');
        campoFuncionalidadeMenu.removeAttr('disabled');
    } else {
        camposDeUrl.attr('disabled', 'disabled');
        campoFuncionalidadeMenu.attr('disabled', 'disabled');
    }

    jQuery.uniform.update();
}

function urlDigitada(elemento)
{
    if (elemento.val() !== '') {
        campoFuncionalidadeMenu.attr('disabled', 'disabled');
    } else {
        campoFuncionalidadeMenu.removeAttr('disabled');
        campoUrl.removeAttr('disabled');
    }

    jQuery.uniform.update();
}  

function salvarCallback(data) {
    if (data.response === 1) {
        jQuery.jGrowl(data.success, {life: 2000, position: 'center', close: function() {
            location.href = "gerenciadorBanner/lista";
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

    if (v !== "" && v !== undefined) {
        jQuery('#imagemBanco').val(v);
        jQuery.get("gerenciadorBanner/getHtmlImagens/" + v).done(function(data) {
            jQuery('#imgSelecionadas').html(data);
        });
    }
}

function excluirImagem(id)
{
    var ids = jQuery('#imagemBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#img' + id).remove();
    jQuery('#imagemBanco').val(ids)
}

jQuery(document).ready(function() {
    //tipoBanner
    categoria.change(function(){
        jQuery.ajax('gerenciadorBanner/buscaSubsitesPermissionadosBanner', {
            type: 'POST',
            dataType: 'json',
            data: {
                tipoBanner:categoria.val(),
                idBanner: id.val()
            },
            success: function(data) {
                if(data.login == true){
                    var $el = jQuery('#sites');
                    $el.html(' ');
                    jQuery.each(data.subsites, function(key, value) {
                        var a = jQuery("<option></option>").attr("value", value).text(key);
                        if(data.vinculos != null){
                            if(data.vinculos[value] == true || data.vinculos[value] == "true"){ 
                                a.attr("selected","selected");
                            }
                        }
                        $el.append(a);
                    });
                }
            }
        });
    });

    
    
    
    // Ativa ou desativa os campos
    desativarCampos();

    // Ativa ou desativa campos de URL
    campoTemLink.change(function(){
        temLinkSelecionado(jQuery(this));
    });

    // Quando é digitado conteúdo no campo de URL
    campoUrl.keyup(function(){
        urlDigitada(jQuery(this));
    });
    
    // Quando uma Funcionalidade é selecionada
    campoFuncionalidadeMenu.on('change', function(){
        funcionalidadeMenuSelecionada(jQuery(this));
    });

    // Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');
    });
    
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    jQuery.validator.addMethod('timeorblank', function(value, element, param) {
    
            // quando não tem valor digitado, vem a mascará __:__
            value = value.replace(/\D/g, '');
            
            if(value) {
                
                if(value.length < 4) return false;

                var hour = parseInt(value[0] + value[1]);
                var min = parseInt(value[2] + value[3]);
                
                if(hour > 23 || min > 59) return false;
            }
            return true;
        }, 
        'Hora inválida'
    );

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome: { required: true, maxlength: 90 },
            imagemBanco: { required: true },
            categoria: { required: true },
            'sites[]': { required: true, minlength: 1 },
            dataInicial: { required: true },
            horaInicial: { required: true, time: true },
            horaFinal: { timeorblank: true }
        },
        messages: {
            nome: { required: "Este campo é requerido", maxlength: "Máximo 90 caracteres" },
            imagemBanco: { required: "Este campo é requerido" },
            categoria: { required: "Este campo é requerido" },
            'sites[]': { required: "Este campo é requerido" },
            hora_inicial: { required: "Este campo é requerido" },
            data_inicial: { required: "Este campo é requerido" },
        },
        submitHandler: function(form) {
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        },
        errorPlacement: function(error, element) {
            // Se for um SELECT (fix para UniformJS)
            if (element.is('select') || element.is('input[type="radio"]')) {
                element.parents('.field:first').append(error);
            } else {
                error.insertAfter(element);
            }
        }
    });
});
