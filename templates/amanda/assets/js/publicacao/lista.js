var table;
jQuery(document).ready(function() {

    table = jQuery('#publicacoes').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#publicacoes tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "publicacao/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "categoria", "value": jQuery("#categoriasFiltro").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#status").change(function() {
        table.fnDraw();
    });

    jQuery("#categoriasFiltro").change(function() {
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

    jQuery("#publicacoes tbody").sortable({
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

function salvarPosicionamentoPublicacao()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posicionamento', function(response) {
        if (!response) {
            return false;
        }
        var ordenacao = [];
        var total = jQuery('[name="publicacoes_length"] option:selected').text();
        if(total == 'Todos')
            total = 10;
        var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
        jQuery('#publicacoes tbody .checker input').each(function(i){
            ordenacao.push({
                id: jQuery(this).val(),
                ordenacao: (i + 1 + ordenacaoBase)
            });
        });
        jQuery.ajax('publicacao/ajaxAtualizarOrdenacaoPublicacao', {
            type: 'POST',
            dataType: 'json',
            data: {
                ordenacao: ordenacao
            },
            success: function(data) {
                if (!data.error) {
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


function alterarStatus(status){
    var sel = getCheckboxMarcados("publicacoes");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("publicacao/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}


function excluir()
{
    var sel = getCheckboxMarcados("publicacoes");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {

            if (response) {
                jQuery.post("publicacao/delete", {sel: sel}, callBackAjax, "json");
            }
        }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}
