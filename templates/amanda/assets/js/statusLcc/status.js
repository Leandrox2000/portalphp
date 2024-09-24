jQuery(document).ready(function(){
    
    carregaSortable();
    
});

function adicionarStatus()
{
    if (jQuery("#novoStatus").val().trim() != "") {
        jQuery.post(baseUrl() + "statusLcc/salvarStatus", {nome: jQuery("#novoStatus").val().trim()}, callbackStatus, "json");
    } else {
        jAlert("Informe o status");
    }
}

function carregaStatus()
{
    jQuery.post(baseUrl() + "statusLcc/getTableStatus", function(table) {
        jQuery("#tableStatus").replaceWith(table);
        carregaSortable();
    });
}

function excluirStatus(id)
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "statusLcc/excluiStatus", {id: id}, callbackStatus, "json");
                }

            }
    );
}

function editaStatus(e) {
    
    e.preventDefault();

    var link = jQuery(this);
    var id = link.attr('data-id');
    var cols = link.parents('tr:first').find('td');
    
    dumpRows[id] = cols.clone();
    
    cols.first().
            empty().
            append("<input type='hidden' name='idStatus' value='" + id + "'/>").
            append("<input type='text' name='edicaoStatus' value='" + link.text() + "'/>");
    

    var combo = jQuery('<select>').attr('name', 'ordemColumn').val(link.attr('data-column'));
    
    jQuery.each(ordemColumns, function(value, display){
        
        combo.append(jQuery('<option>').attr('value', value).text(display));
    });
    
    combo.find('option[value=' + link.attr('data-column') +  ']').attr('selected', 'selected');

    cols.next().
            empty().
            append(combo);
    
    cols.last().
            empty().
            append('<a href="#" class="btn btn3 btn_ btn_archive radius0" title="Salvar"></a>').
            append('<a href="#" class="btn btn3 btn_ btn_stop radius0" data-remove="' + id + '" title="Cancelar"></a>');

    return false;
}

function rollbackStatus(e){
    
    e.preventDefault();
    
    var link = jQuery(this);
    var id = link.attr('data-remove');
    
    if(dumpRows[id]){
        
        var row = link.parents('tr:first');
        
        row.empty();
        
        dumpRows[id].clone().each(function(index, element){
            
            jQuery(element).appendTo(row);
            
        });
        
        dumpRows[id] = null;
    }
    
    return false;
}

function editarStatus(e) {
    
    e.preventDefault();
    
    try {

        var link = jQuery(this);
        var row = link.parents('tr:first');

        var params = {
            nome: row.find('input[name=edicaoStatus]').val().trim(),
            column: row.find('select[name=ordemColumn]').val(),
            id: row.find('input[name=idStatus]').val()      
        };

        jQuery.post(baseUrl() + "statusLcc/salvarStatus", params, callbackStatus, "json");
    
    }
    catch(e){
        
        alert(e.message);
    }
}

function callbackStatus(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaStatus();
    } else if (data.response == 2) {
        jAlert(
            data.error, 
            'Erro ao Excluir!'
        );
        carregaVinculos();
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    jQuery("#novoStatus")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarStatus()'})
            .removeClass("disabled");
}

function salvarPosicionamento()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posição', function(response) {
        if (response) {
            var ordenacao = [];
            jQuery("#tableStatus tbody input[name='statusLccId']").each(function(i){
                ordenacao.push({
                    id: jQuery(this).val(),
                    ordenacao: (i + 1)
                });
            });
            jQuery.ajax('statusLcc/ajaxAtualizarOrdenacao', {
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
//                        table.fnDraw();
                    }
                }
            });
        }
    });
}

function carregaSortable(){
    
    jQuery('table#tableStatus').delegate('a[data-id]', 'click', editaStatus);
    jQuery('table#tableStatus').delegate('a[data-remove]', 'click', rollbackStatus); 
    jQuery('table#tableStatus').delegate('a.btn_archive', 'click', editarStatus);
    
    jQuery("#tableStatus tbody").sortable({
        helper: function(e, tr){
            var originals = tr.children();
            var helper = tr.clone();
            helper.children().each(function(index){
              jQuery(this).width(originals.eq(index).width());
            });
            return helper;
        }
    });
}