var table;
jQuery(document).ready(function() {
    table = jQuery('#conselheiros').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#conselheiros tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "conselheiro/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status",          "value": jQuery("#status").val()});
            aoData.push({"name": "data_inicial",    "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final",      "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#status").change(function() {
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


    jQuery('input:checkbox, input:radio, select').uniform();

    jQuery("#conselheiros tbody").sortable({
        helper: function(e, tr){
          var originals = tr.children();
          var helper = tr.clone();
          helper.children().each(function(index)
          {
            jQuery(this).width(originals.eq(index).width());
          });
          return helper;
        }
    }).disableSelection();
    
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
    var sel = getCheckboxMarcados("conselheiros");

    if (sel.length > 0) {
       jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
                function(response) {
                    if (response) {
                        jQuery.post("conselheiro/delete", {sel: sel}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}


function alterarStatus(status){
    var sel = getCheckboxMarcados("conselheiros");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("conselheiro/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function salvarPosicionamento()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posicionamento', function(response) {
        if (!response) {
            return false;
        }
        var ordenacao = [];
        var item;
        var total = jQuery('[name="conselheiros_length"] option:selected').text();
        if(total == 'Todos')
            total = 10;
        var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
        jQuery('.ordenacao_registro').each(function(i) {
            item = jQuery(this);
            ordenacao.push({
                id: item.attr('data-id'),
                ordenacao: (i + 1 + ordenacaoBase)
            });
        });

        jQuery.ajax('conselheiro/ajaxAtualizarOrdenacao', {
            type: 'POST',
            dataType: 'json',
            data: {
                ordenacao: ordenacao
            },
            success: function(data) {
//                jQuery('input[name="ordenacao_registro"]').removeClass('ord-error');
                if (data.resultado == 'erro') {
                    jQuery.jGrowl('Existem itens com ordenação repetida.', {
                        life: 4000,
                        position: 'center'
                    });
//                    Marca em vermelho os campos com erro
//                    for (r in data.equals) {
//                        jQuery('input[name="ordenacao_registro"][data-id="' + data.equals[r] + '"]').addClass('ord-error');
//                    }
                } else if (data.resultado == 'ok') {
                    jQuery.jGrowl('Ação executada com sucesso.', {
                        life: 4000,
                        position: 'center'
                    });
//                    Atualiza o grid
                    table.fnDraw();
                }
            }
        });
    });
}