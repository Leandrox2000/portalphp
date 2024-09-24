jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    //Adiciona a validação de inteiros ao validate
    jQuery.validator.addMethod("integer", function(value, element) {
        var er = /^[0-9]+$/;
        return (er.test(value));
    }, "Informe um valor numérico");

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: { time: true },
            ambitoExecucao: {required: true},
            categoria: {required: true},
            status: {required: true},
            tipo: {required: true},
            exibirInformacoes: {required: true},
            prazoVigenciaInicial: {required: true},
            horaAberturaProposta: {time: true},
            prazoVigenciaFinal: {required: true},
            contratada: {required: true},
            objeto: {required: true},
            edital: {required: true},
            uasg: {required: true},
            valorEstimado: {required: true},
            ano: {required: true, integer: true},
            processo: {required: true},
            arquivos: {required: true}

        },
        messages: {
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            ambitoExecucao: {required: "Este campo é requerido"},
            categoria: {required: "Este campo é requerido"},
            status: {required: "Este campo é requerido"},
            tipo: {required: "Este campo é requerido"},
            exibirInformacoes: {required: "Este campo é requerido"},
            prazoVigenciaInicial: {required: "Este campo é requerido"},
            prazoVigenciaFinal: {required: "Este campo é requerido"},
            contradada: {required: "Este campo é requerido"},
            objeto: {required: "Este campo é requerido"},
            edital: {required: "Este campo é requerido"},
            uasg: {required: "Este campo é requerido"},
            valorEstimado: {required: "Este campo é requerido", number: "Utilize valores numéricos com ponto se necessário"},
            ano: {required: "Este campo é requerido", integer: "Informe um valor numérico"},
            processo: {required: "Este campo é requerido"},
            arquivos: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "ambitoExecucao" || element.attr("name") == "categoria" || element.attr("name") == "status" || element.attr("name") == "tipo") {
                element.parent().parent().append(error);

            } else if (element.attr("name") == "arquivos") {
                jQuery('#arquivo').parent().after(error);
            }
            else {
                error.insertAfter(element);
            }

        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.observacao) jQuery('#observacao').val(CKEDITOR.instances.observacao.getData());
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });


    //Upload de Arquivo Simples
    jQuery('#arquivo').change(function() {
        var progressBar = ".Progressarquivo .redbar";
        var fileField = jQuery(this).parent();
        fileField.fadeOut(20, function() {
            jQuery(this).next().fadeIn(200);
        });

        jQuery('#frm').ajaxSubmit({
            dataType: 'json',
            uploadProgress: function(event, position, total, percentComplete) {
                jQuery(progressBar).css('width', percentComplete + '%').text('' + percentComplete + '%');
            },
            success: function(data) {
                var count = 0;
                jQuery('.Progressarquivo .redbar').css('width', '100%').text('Concluído (100%)');

                setTimeout(function() {
                    jQuery.each(data, function(index, value) {
                        if (data[index].error.length > 0) {
                            jQuery.each(value.error, function(index, msg) {
                                jQuery.jGrowl(msg, {life: 4000, position: 'center'});
                            });
                            fileField.fadeIn(200);
                        } else {
                            jQuery('#divArquivos').append(getHtml(value));
                            var arquivos = jQuery('#arquivos').val();
                            jQuery('#arquivos').val(arquivos + '|' + value.temp_name + "." + value.extensao+";;"+value.real_name);
                            
                        }
                    });
                    jQuery('.Progressarquivo').fadeOut(700);
                    jQuery('#uniform-arquivo').fadeIn(700);
                    jQuery('.filename').html("Nenhum arquivo");

                }, 1000);
            },
            error: function() {
                jQuery('#arquivo').parent().next().fadeOut(600, function() {
                    jQuery(this).prev().fadeIn(600);
                });
            },
            url: baseUrl() + 'upload/adicionar/0',
            data: {
                field: 'arquivo',
                extensions: 'txt,pdf,xls,xlsx,doc,docx,jpg,png,csv,zip',
            }
        });

    });

    
    //Exibe o form de acordo com a categoria selecionada
    jQuery('#categoria').change(function() {
        formCategoria(jQuery('#categoria').val());
    });
    
    //Verifica se o form é de edição
    if(jQuery('#categoria').val() !== ""){
         formCategoria(jQuery('#categoria').val());
    }



});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "licitacaoConvenioContrato/lista";
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

function getHtml(data)
{
    funcao = "javascript:removerArquivoTemp('" + data.temp_name + "','" + data.extensao + "', '"+data.real_name+"')";
    html = '<div id="divArquivo' + data.temp_name + '"><i>' + data.real_name + '</i><a href="' + funcao + '">&nbsp;&nbsp;<strong>X</strong></a></div>';
    return  html;
}

function removerArquivoTemp(nome, extensao, nome_real)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#divArquivo" + nome);
            var stringArquivos = jQuery('#arquivos').val();
            stringArquivos = stringArquivos.replace('|' + nome + '.' + extensao + ";;" + nome_real, "");
            jQuery('#arquivos').val(stringArquivos);


            jQuery.post("upload/remover/" + nome + "." + extensao, function(data) {
                div.fadeOut(400,
                        function() {
                            jQuery(this).parent().children(":first").fadeIn(400);
                            jQuery(this).remove();
                            jQuery.jGrowl('Ação executada com sucesso!', {life: 4000, position: 'center'});
                        }
                );
            }, "json");
        }
    });
}

function removerArquivo(id, nomeArquivo, nomeOriginal)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#divArquivo" + id);
            
            //Adiciona o id do arquivo ao hidden de arquivos excluidos
            var arquivosExcluidos = jQuery('#arquivosExcluidos').val();
            jQuery('#arquivosExcluidos').val(arquivosExcluidos + id + ",");
        
            //Retira o nome do arquivo do hidden de arquivos
            var stringArquivos = jQuery('#arquivos').val();
            stringArquivos = stringArquivos.replace('|' + nomeArquivo + ";;" + nomeOriginal, "");
            jQuery('#arquivos').val(stringArquivos);
            
            div.fadeOut(400,
                    function() {
                        jQuery(this).parent().children(":first").fadeIn(400).children(":first").fadeIn(400);
                        jQuery(this).remove();
                        jQuery.jGrowl('Ação executada com sucesso!', {life: 4000, position: 'center'});
                    }
            );
        }
    });
}

function mostraLink()
{
    if (jQuery("#inserirLink").is(":checked")) {
        jQuery(".link").fadeIn(500);
    } else {
        jQuery(".link").fadeOut(500);
    }
}

/*Sub-crud de categorias */

function abrirCategorias()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "categoriaLcc/categorias", width: "75%", height: "90%", onClosed: carregaCategoriasSelect});
}

function carregaCategoriasSelect()
{
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "categoriaLcc/getCategorias", function(data) {
        jQuery("#categoria").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}


/*Sub-crud de âmbitos */

function abrirAmbitos()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "ambitoLcc/ambitos", width: "75%", height: "90%", onClosed: carregaAmbitosSelect});
}

function carregaAmbitosSelect()
{
    jQuery("#ambitoExecucao").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "ambitoLcc/getAmbitos", function(data) {
        jQuery("#ambitoExecucao").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#ambitoExecucao").append(new Option(value.label, value.id));
        });
    }, "json");

}

/*Sub-crud de tipos */

function abrirTipos()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "tipoLcc/tipos", width: "75%", height: "90%", onClosed: carregaTiposSelect});
}

function carregaTiposSelect()
{
    jQuery("#tipo").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "tipoLcc/getTipos", function(data) {
        jQuery("#tipo").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#tipo").append(new Option(value.label, value.id));
        });
    }, "json");

}


/*Sub-crud de status */

function abrirStatus()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "statusLcc/status", width: "75%", height: "90%", onClosed: carregaStatusSelect});
}

function carregaStatusSelect()
{
    jQuery("#status").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "statusLcc/getStatus", function(data) {
        jQuery("#status").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#status").append(new Option(value.label, value.id));
        });
    }, "json");

}


//Habilita as divs
function enableDivs(div, campos, habilitar) {
    for (var i = 0; i < div.length; i++) {
        if (habilitar) {
            jQuery('#' + div[i]).show('slow');
        } else {
            jQuery('#' + div[i]).hide('slow');
        }

    }

    for (var i = 0; i < campos.length; i++) {
        if (habilitar) {
            jQuery('#' + campos[i]).attr('disabled', false);
        } else {
            jQuery('#' + campos[i]).attr('disabled', true);
        }

    }
}

//Exibe os forms de acordo com a categoria passada
function formCategoria(categoria) {
    //Esconde as divs
    var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto", "divDataPublicacaoDou", "divPeriodoAberturaProposta", "divEdital", "divValorEstimado", "divUasg", "divAno", "divProcesso", "divPrazoVigencia", "divContratada");
    var campos = new Array("dataPublicacaoDou", "dataAberturaProposta", "horaAberturaProposta", "objeto", "edital", "valorEstimado", "uasg", "ano", "processo", "prazoVigenciaInicial", "prazoVigenciaFinal", "contratada");
    enableDivs(divs, campos, false);

    //Habilita as divs de acordo com a categoria
    switch (categoria) {
        case '1':
            var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto", "divDataPublicacaoDou", "divPeriodoAberturaProposta", "divEdital", "divValorEstimado", "divUasg", "divAno", "divProcesso");
            var campos = new Array("dataPublicacaoDou", "dataAberturaProposta", "horaAberturaProposta", "objeto", "edital", "valorEstimado", "uasg", "ano", "processo");
            enableDivs(divs, campos, true);
            break;

        case '2':
            var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto", "divDataPublicacaoDou", "divPeriodoAberturaProposta", "divEdital", "divValorEstimado", "divUasg", "divAno", "divProcesso");
            var campos = new Array("dataPublicacaoDou", "dataAberturaProposta", "horaAberturaProposta", "objeto", "edital", "valorEstimado", "uasg", "ano", "processo");
            enableDivs(divs, campos, true);
            break;

        case '3':
            var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto", "divDataPublicacaoDou", "divPeriodoAberturaProposta", "divEdital", "divValorEstimado", "divUasg", "divAno", "divProcesso", "divPrazoVigencia");
            var campos = new Array("dataPublicacaoDou", "dataAberturaProposta", "horaAberturaProposta", "objeto", "edital", "valorEstimado", "uasg", "ano", "processo", "prazoVigenciaInicial", "prazoVigenciaFinal");
            enableDivs(divs, campos, true);
            break;

        case '4' :
            var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto", "divDataPublicacaoDou", "divPeriodoAberturaProposta", "divEdital", "divValorEstimado", "divUasg", "divAno", "divProcesso", "divPrazoVigencia", "divContratada");
            var campos = new Array("dataPublicacaoDou", "dataAberturaProposta", "horaAberturaProposta", "objeto", "edital", "valorEstimado", "uasg", "ano", "processo", "prazoVigenciaInicial", "prazoVigenciaFinal", "contratada");
            enableDivs(divs, campos, true);
            break;

        case  '5':
            var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto", "divDataPublicacaoDou", "divPeriodoAberturaProposta", "divEdital", "divValorEstimado", "divUasg", "divAno", "divProcesso", "divPrazoVigencia", "divContratada");
            var campos = new Array("dataPublicacaoDou", "dataAberturaProposta", "horaAberturaProposta", "objeto", "edital", "valorEstimado", "uasg", "ano", "processo", "prazoVigenciaInicial", "prazoVigenciaFinal", "contratada");
            enableDivs(divs, campos, true);
            break;

        default:
            if (categoria !== "") {
                var divs = new Array("divTipo", "divStatus" ,"divArquivosAnexos", "divObservacoes", "divObjeto");
                var campos = new Array("objeto");
                enableDivs(divs, campos, true);
            }
            break;
    }

}

