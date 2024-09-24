var table;
var criticaOrdenacao = 'Para salvar posicionamento é necessário escolher um subsite e definir a ordem das agendas na lista. Os demais filtros não poderão ser utilizados para esta ação.';
        
jQuery(document).ready(function() {

    trocarEstadoSalvarOrdenacao();

    table = jQuery('#agendas').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#agendas tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "gerenciadorAgenda/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#status, #site, #dataInicial, #dataFinal").change(function() {
        trocarEstadoSalvarOrdenacao();
        table.fnDraw();
    });
    
    jQuery('#agendas_filter input').change(trocarEstadoSalvarOrdenacao);

    jQuery('input:checkbox, input:radio, select').uniform();
    
    jQuery("#agendas tbody").sortable({
        helper: function(e, tr){
          var originals = tr.children();
          var helper = tr.clone();
          helper.children().each(function(index)
          {
            jQuery(this).width(originals.eq(index).width());
          });
          return helper;
        },
        stop: function(e, ui) {
            if(!podeSalvarOrdenacao()) {
                jAlert(criticaOrdenacao, "Aviso");
                return false;
            }
        }
    }).disableSelection();
});

/**
 * Envia uma requisição para salvar a ordenação definida pelo usuário.
 * Só é possível ordenar por subsite.
 * 
 * @returns {undefined}
 */
function salvarPosicionamento() {
    
    // Verifica se o filtro de subsite está aplicado
    if(!podeSalvarOrdenacao()) {
        jAlert(criticaOrdenacao, "Aviso");
        return false;
    }
        
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posicionamento', function(response) {
        if (!response) {
            return false;
        }
        var ordenacao = [];
        var total = jQuery('[name="agendas_length"] option:selected').text();
        if(total == 'Todos')
            total = 10;
        var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
        jQuery('#agendas tbody .checker input').each(function(i){
            ordenacao.push({
                id: jQuery(this).val(),
                ordenacao: (i + 1 + ordenacaoBase)
            });
        });
        jQuery.ajax('gerenciadorAgenda/salvarOrdenacao', {
            type: 'POST',
            dataType: 'json',
            data: {
                ordenacao: ordenacao,
                site: jQuery('#site').val()
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

/**
 * Só pode ordenar se o filtro de subsite estiver selecionado
 * e sem outros filtros.
 * 
 * @returns {Boolean}
 */
function podeSalvarOrdenacao() {
    return jQuery('#site').val() 
        && !jQuery('#dataInicial').val() 
        && !jQuery('#dataFinal').val() 
        && !jQuery('#status').val() 
        && !jQuery('#agendas_filter input').val();
}

/**
 * Troca os estado do botão de salvar posicionamento.
 * 
 * @returns {undefined}
 */
function trocarEstadoSalvarOrdenacao() {
    if(!podeSalvarOrdenacao()) {
        jQuery('.btn-agenda').prop('disabled', true).attr('title', criticaOrdenacao);
    } else {
        jQuery('.btn-agenda').prop('disabled', false).attr('title', '');
    }
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
    var sel = getCheckboxMarcados("agendas");
    msg = "Deseja realmente executar esta ação? ";
    var table = "agenda_direcao";
    var entity = "Entity\\AgendaDirecao";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.ajax({
                            url: 'gerenciadorAgenda/alterarStatusValidacao',
                            type: 'post',
                            dataType: 'json',
                            data: {sel: sel, status: status, table: table, entity : entity},
                            success: function(data) {
                                callBackAjax(data);
                            }
                        });
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function excluir()
{
    var sel = getCheckboxMarcados("agendas");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post("gerenciadorAgenda/delete", {sel: sel}, callBackAjax, "json");
            }
        }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}
