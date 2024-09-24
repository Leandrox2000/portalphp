jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();
    jQuery("#frm").validate({
        rules: {
            dataInicial: {required: true},
            horaInicial: {required: true, time: true },
            horaFinal: { time: true },
            categoria: {required: true},
            verbete: {required: true},
            titulo: {required: true},
            descricao: {
                required: function(textarea) {
                    if(CKEDITOR.instances.descricao) CKEDITOR.instances.descricao.updateElement(); // update textarea
                    var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                    return editorcontent.length === 0;
                }
            },
            colaborador: {required: false},
            funcao: {required: false},
            fichaTecnica: { required: false}
            // fichaTecnica: {
            //     required: function(textarea) {
            //         CKEDITOR.instances.fichaTecnica.updateElement(); // update textarea
            //         var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
            //         return editorcontent.length === 0;
            //     }
            // }
        },
        messages: {
            dataInicial: {required: "Este campo é requerido"},
            horaInicial: {required: "Este campo é requerido"},
            categoria: {required: "Este campo é requerido"},
            verbete: {required: "Este campo é requerido"},
            titulo: {required: "Este campo é requerido"},
            descricao: {required: "Este campo é requerido"}
            // colaborador: {required: "Este campo é requerido"},
            // funcao: {required: "Este campo é requerido"},
            // fichaTecnica: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            // Se for um SELECT (fix para UniformJS)
            if (element.is('select')) {
                element.parent().parent().append(error);
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
            // console.log(t.serialize());

            // update no campo FichaTecnica para pegar o valor atualizado do textarea
            if(CKEDITOR.instances.fichaTecnica) CKEDITOR.instances.fichaTecnica.updateElement(); 

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


});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success,{life: 4000, position: 'center', close: function() {
                                    location.href = "dicionarioPatrimonioCultural/lista";
                                }
                            }
        );
    }
    else {
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
    jQuery.colorbox({href: link + "dicionarioPatrimonioCultural/categorias", width: "75%", height: "90%",  onClosed: carregaCategoriasSelect});
}

function carregaCategoriasSelect()
{
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "dicionarioPatrimonioCultural/getCategorias", function(data) {
        jQuery("#categoria").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}
