var table;
jQuery(document).ready(function() {

    table = jQuery('#gerenciadorBanners').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#gerenciadorBanners tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "gerenciadorBanner/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
            aoData.push({"name": "categoria", "value": jQuery("#categoria").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });


    jQuery("#categoria, #status, #site").change(function() {
        table.fnDraw();
    });

    jQuery("#dataInicial").change(function() {
        if (jQuery("#dataInicial").val() !== "" && jQuery("#dataFinal").val()) {
            table.fnDraw();
        }
    });

    jQuery("#dataFinal").change(function() {
        if (jQuery("#dataInicial").val() !== "" && jQuery("#dataFinal").val()) {
            table.fnDraw();
        }
    });

    jQuery(document).on('click', '.ordenacao_registro', function(){
        jQuery(this).select();
    });

    jQuery(document).on('blur', '.ordenacao_registro', function(){
        var regex = new RegExp('^[0-9]+$');

        if (!regex.test(jQuery(this).val()) || jQuery(this).val() < 1) {
            jQuery(this).val('');
        }
    });

    jQuery('input:checkbox, input:radio, select').uniform();

});


//Callbakc do botão Excluir
function callBackAjax(data)
{
    if (data.response == 1) {
        table.fnDraw(false);
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function excluir()
{
    var sel = getCheckboxMarcados("gerenciadorBanners");
    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
                if (response) {
                    jQuery.ajax('gerenciadorBanner/podeDeletar', {
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            sel: sel,
                            id_categoria: jQuery("#categoria").val(),
                            id_site: jQuery("#site").val()
                        },
                        success: function(data) {
                            if(data.podeExcluir == true){
                                jQuery.post("gerenciadorBanner/delete", {sel: sel}, callBackAjax, "json");
                            }else{
                                jAlert("Atenção, não possui permissão para Excluir.", "Aviso");
                            }
                        }
                    });
                }
            }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function alterarStatus(status,tipoBanner){
    var sel = getCheckboxMarcados("gerenciadorBanners");
    var categoria = jQuery('#categoria').val();
    var exc = false;
    jQuery('#gerenciadorBanners tbody tr .checked > input').each(function () {
        if (jQuery(this).is(':checked') && jQuery(this).parents('tr').find('.st').text().trim() === 'Não publicado') {
            exc = true
        }
    });

    msg = "Deseja realmente executar esta Ação? ";

    jQuery.ajax('gerenciadorBanner/ehComunicacao', {
        type: 'POST',
        dataType: 'json',
        data: {
            tipoBanner: jQuery("#categoria").val(),
            categoria: categoria,
            status: exc,
            subsite: jQuery("#site").val()
        },
        success: function(data) {
            //Debug Rain
            //console.log(data);
            if(data.permissao) {
                jAlert("Apenas pode existir três Banners publicados para esta categoria!", "Aviso");
            } else {
                if(data.comunicacao == true && status == 1 && sel.length > 1){
                    if(sel.length >= 3){
                        jQuery.ajax('gerenciadorBanner/buscaCategoria', {
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                tipoBanner: jQuery("#categoria").val()
                            },
                            success: function(data) {
                                if (data.podePublicar == false) {
                                    jAlert("Apenas pode existir dois Banners publicado para esta categoria!", "Aviso");
                                }else{
                                    jConfirm(msg, 'Confirmar Alteração',
                                        function(response) {
                                            if (response) {
                                                if(status == '1'){
                                                    jQuery.ajax('gerenciadorBanner/verificaBannersPublicados', {
                                                        type: 'POST',
                                                        dataType: 'json',
                                                        data: {
                                                            tipoBanner: jQuery("#categoria").val()
                                                        },
                                                        success: function(data) {
                                                            if (data.podePublicar == true) {
                                                                jQuery.post("gerenciadorBanner/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                                                            }else{
                                                                jAlert("Apenas pode existir dois Banners publicado para esta categoria!", "Aviso");
                                                            }
                                                        }
                                                    });
                                                }else{
                                                    jQuery.post("gerenciadorBanner/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                                                }
                                            }
                                        }
                                    );
                                }
                            }
                        });
                    }

                    if(sel.length == 2){
                        jQuery.ajax('gerenciadorBanner/buscaCategoria2', {
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                tipoBanner: jQuery("#categoria").val()
                            },
                            success: function(data) {
                                if (data.podePublicar == false) {
                                    jAlert("Apenas pode existir dois Banners publicado para esta categoria!", "Aviso");
                                } else{
                                    jConfirm(msg, 'Confirmar Alteração',
                                        function(response) {
                                            if (response) {
                                                if(status == '1'){
                                                    jQuery.ajax('gerenciadorBanner/verificaBannersPublicados', {
                                                        type: 'POST',
                                                        dataType: 'json',
                                                        data: {
                                                            tipoBanner: jQuery("#categoria").val()
                                                        },
                                                        success: function(data) {
                                                            if (data.podePublicar == true) {
                                                                jQuery.post("gerenciadorBanner/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                                                            }else{
                                                                jAlert("Apenas pode existir dois Banners publicado para esta categoria!", "Aviso");
                                                            }
                                                        }
                                                    });
                                                }else{
                                                    jQuery.post("gerenciadorBanner/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                                                }
                                            }
                                        }
                                    );
                                }
                            }
                        });
                    }
                }else{
                    if (sel.length > 0 ) {
                        jConfirm(msg, 'Confirmar Alteração',
                            function(response) {
                                if (response) {
                                    if(status == '1'){
                                        jQuery.ajax('gerenciadorBanner/verificaBannersPublicados', {
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {
                                                tipoBanner: jQuery("#categoria").val()
                                            },
                                            success: function(data) {
                                                if (data.podePublicar == true) {
                                                    jQuery.post("gerenciadorBanner/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                                                }else{
                                                    jAlert("Apenas pode existir dois Banners publicado para esta categoria!", "Aviso");
                                                }
                                            }
                                        });
                                    }else{
                                        jQuery.post("gerenciadorBanner/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                                    }
                                }
                            }
                        );
                    }
                }
            }
        }
    });

    if(sel.length == 0){
        jAlert("Nenhum registro selecionado", "Aviso");
    }

}




function salvarPosicionamento()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
        if (!response) {
            return false;
        }
        var ordenacao = [];
        var item;

        jQuery('.ordenacao_registro').each(function(){
            item = jQuery(this);
            ordenacao.push({
                id: item.attr('data-id'),
                ordenacao: item.val()
            });
        });

        jQuery.ajax('gerenciadorBanner/ajaxAtualizarOrdenacao', {
            type: 'POST',
            dataType: 'json',
            data: {
                ordenacao: ordenacao
            },
            success: function(data) {
                jQuery('input[name="ordenacao_registro"]').removeClass('ord-error');

                if (data.resultado == 'erro') {
                    jQuery.jGrowl('Existem itens de mesma categoria com ordenação repetida.', {
                        life: 4000,
                        position: 'center'
                    });
                    // Marca em vermelho os campos com erro
                    for (r in data.equals) {
                        jQuery('input[name="ordenacao_registro"][data-id="'+data.equals[r]+'"]').addClass('ord-error');
                    }
                } else if (data.resultado == 'ok') {
                    jQuery.jGrowl('Ação executada com sucesso.', {
                        life: 4000,
                        position: 'center'
                    });

                    // Atualiza o grid
                    table.fnDraw();
                }
            }
        });
    });
}

function visualizar(id, id_categoria) {
    jQuery.post(baseUrl() + "gerenciadorBanner/visualizar/" + id + "/" + id_categoria, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}