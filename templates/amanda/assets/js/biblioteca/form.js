jQuery(document).ready(function() {
    jQuery('.par.redes-sociais input, .par.redes-sociais select').attr('disabled', 'disabled');
    jQuery('#add-rede').hide();

    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            dataInicial:            { required: true },
            horaInicial:            { required: true, time: true },
            horaFinal:              { time: true },
            nome:                   { required: true },
            responsavel:            { required: true },
            cidade:                 { required: true },
            uf:                     { required: true },
            cep:                    { required: true },
            endereco:               { required: true },
            numero:                 { required: true },
            bairro:                 { required: true },
            telefone:               { required: true },
            email:                  { email: true },
            horarioFuncionamento:   { required: true },
            descricao:              {
                required: function(textarea) {
                    if(CKEDITOR.instances.descricao) CKEDITOR.instances.descricao.updateElement(); // update textarea
                    var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                    return editorcontent.length === 0;
                }
            },
            imagemBanco:            { required: true }
        },
        messages: {
            dataInicial:            { required: "Este campo é requerido" },
            horaInicial:            { required: "Este campo é requerido" },
            nome:                   { required: "Este campo é requerido" },
            responsavel:            { required: "Este campo é requerido" },
            cidade:                 { required: "Este campo é requerido" },
            uf:                     { required: "Este campo é requerido" },
            endereco:               { required: "Este campo é requerido" },
            numero:                 { required: "Este campo é requerido" },
            cep:                    { required: "Este campo é requerido" },
            bairro:                 { required: "Este campo é requerido" },
            telefone:               { required: "Este campo é requerido" },
            horarioFuncionamento:   { required: "Este campo é requerido" },
            descricao:              { required: "Este campo é requerido" },
            email:                  { required: "Este deve ser um endereço de e-mail válido" },
            imagemBanco:            { required: "Este campo é requerido" }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "uf") {
                element.parent().parent().append(error);
            } else if (element.attr("name") == "imagemBanco") {
                jQuery('#imgSelecionadas').parent().append(error);
            } else {
                error.insertAfter(element);
            }

        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.descricao) jQuery('#descricao').val(CKEDITOR.instances.descricao.getData());
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
        {completed:function(){
            jQuery("#ceploading").show();
            jQuery.getScript('http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep='+this.val(), function(data){
                jQuery('#uf').val(unescape(resultadoCEP.uf));
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

function adicionarRedeSocial() {
    
}

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "biblioteca/lista";
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

function bindRmRede(element)
{
    jQuery(element).on('click', function(e){
        e.preventDefault();
        var element = jQuery(this).parents('.adicional:first');

        element.remove();
    });
}
jQuery(document).ready(function(){
    var destination = jQuery(".par.redes-sociais .field:first");
    var rm = '<a href="#" class="rm-rede" title="Remover"><span class="glyphicon glyphicon-minus-sign"></span></a>';

    jQuery('#add-rede').click(function(){
        var count = parseInt(jQuery('.par.redes-sociais .adicional').length) + 1;
        var select = '<select name="redeSocial[' + count + ']" id="#ID_SELECT#">'+
                    '<option value="">Selecione</option>'+
                    '<option value="facebook">Facebook</option> '+
                    '<option value="twitter">Twitter</option> '+
                    '<option value="instagram">Instagram</option> '+
                    '<option value="googleplus">Google+</option> '+
                    '<option value="outros">Outros</option> '+
                 '</select>';
        var input = '<input type="text" size="" value="" id="#ID_URL#" name="url[' + count + ']" placeholder="Url" title="Url" class="smallinput">';
        var select_replace = select.replace(/#ID_SELECT#/, 'fredesocial' + count);
        var input_replace = input.replace(/#ID_URL#/, 'furl' + count);
        var html = "<div class='adicional' style='margin-top: 20px;'>" + select_replace + ' ' + input_replace + ' '  + rm + "</div>";

        destination.append(html);
        last = destination.find('.adicional:last');
        last.find('select, input').uniform();
        bindRmRede(last.find('.rm-rede'));

        return false;
    });

    bindRmRede('.rm-rede');
});


function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();

    if (v !== "" && v !== undefined) {
        jQuery('#imagemBanco').val(v);

        jQuery.get("biblioteca/getHtmlImagens/" + v).done(function(data) {
            jQuery('#imgSelecionadas').html(data);
        });
    }
}

function excluirImagem(id)
{
    jQuery('#imagemBanco').val("");
    jQuery('#img'+id).remove();
    
}