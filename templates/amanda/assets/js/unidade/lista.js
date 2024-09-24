var table;
jQuery(document).ready(function() {
    table = jQuery('#unidades').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#unidades tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "unidade/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "data_inicial",    "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final",      "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#dataInicial").change(function() {
        table.fnDraw();
    });

    jQuery("#dataFinal").change(function() {
        table.fnDraw();
    });

    jQuery('input:checkbox, input:radio, select').uniform();
    
    
    jQuery("#unidades tbody").sortable({
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



function salvarPosicionamentoUnidade()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posicionamento', function(response) {
        if (!response) {
            return false;
        }
        var ordenacao = [];
        var total = jQuery('[name="unidades_length"] option:selected').text();
        if(total == 'Todos')
            total = 10;
        var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
        jQuery('#unidades tbody .checker input').each(function(i){
            ordenacao.push({
                id: jQuery(this).val(),
                ordenacao: (i + 1 + ordenacaoBase)
            });
        });
        jQuery.ajax('unidade/ajaxAtualizarOrdenacaoUnidade', {
            type: 'POST',
            dataType: 'json',
            data: {
                ordenacao: ordenacao
            },
            success: function(data) {
                if (data.resultado == 'ok') {
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
    var sel = getCheckboxMarcados("unidades");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
                function(response) {
                    if (response) {
                        jQuery.post("unidade/delete", {sel: sel}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}
