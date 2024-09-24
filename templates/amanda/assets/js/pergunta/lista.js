var table;
jQuery(document).ready(function() {

    table = jQuery('#perguntas').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#perguntas tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "pergunta/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "categoria", "value": jQuery("#categoriasFiltro").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
        }
    });

    jQuery("#categoriasFiltro").change(function() {
        table.fnDraw();
    });

    jQuery("#data_inicial").change(function() {
        if (jQuery("#data_inicial").val() !== "" && jQuery("#data_final").val())
            table.fnDraw();
    });

    jQuery("#data_final").change(function() {
        if (jQuery("#data_inicial").val() !== "" && jQuery("#data_final").val())
            table.fnDraw();
    });

    jQuery('input:checkbox, input:radio, select').uniform();



    jQuery("#perguntas tbody").sortable({
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



function salvarPosicionamentoPergunta()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posicionamento', function(response) {
        if (!response) {
            return false;
        }
        var ordenacao = [];
        var total = jQuery('[name="perguntas_length"] option:selected').text();
        if(total == 'Todos')
            total = 10;
        var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
        jQuery('#perguntas tbody .checker input').each(function(i){
            ordenacao.push({
                id: jQuery(this).val(),
                ordenacao: (i + 1 + ordenacaoBase)
            });
        });
        jQuery.ajax('pergunta/ajaxAtualizarOrdenacaoPergunta', {
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
    var sel = getCheckboxMarcados("perguntas");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {

            if (response) {
                jQuery.post("pergunta/delete", {sel: sel}, callBackAjax, "json");
            }
        }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}



function alterarStatus(status){
    var sel = getCheckboxMarcados("perguntas");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("pergunta/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}
