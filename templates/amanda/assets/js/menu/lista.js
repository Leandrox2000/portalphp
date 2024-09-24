var table;
/**
 * Rota de base para o controller.
 */
var controllerRoute = 'menu';
/**
 * Name do checkbox utilizado para a seleção de registros.
 */
var multipleSelectionCheckboxName = 'itensMenu';
/**
 * Elemento HTML que contém a tabela do CRUD.
 */
var tableElement = '#itensMenu';

jQuery(document).ready(function() {

    table = jQuery(tableElement).dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery(tableElement).find('tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + controllerRoute + "/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "subsite", "value": jQuery("#subsite").val()});
            aoData.push({"name": "vinculoPai", "value": jQuery("#vinculoPai").val()});
            aoData.push({"name": "tipoMenu", "value": jQuery("#tipoMenu").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#status").change(function() {
        table.fnDraw();
    });
    jQuery("#subsite").change(function() {
        atualizaVinculoPai();
        table.fnDraw();
    });
    jQuery("#tipoMenu").change(function() {
        atualizaVinculoPai();
        table.fnDraw();
    });
    jQuery("#vinculoPai").change(function() {
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

    jQuery(document).on('click', '.ordenacao_registro', function(){
        jQuery(this).select();
    });
    
    jQuery(document).on('blur', '.ordenacao_registro', function(){
        var regex = new RegExp('^[0-9]+$');

        if (!regex.test(jQuery(this).val()) || jQuery(this).val() < 1) {
            jQuery(this).val('');
        }
    });
    
    jQuery("#itensMenu tbody").sortable({
        update: function(){
            return verificaFiltros();
        },
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

function alterarStatus(status) {
    var sel = getCheckboxMarcados(multipleSelectionCheckboxName);
    msg = "Deseja realmente executar esta ação? ";

    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração', function(response) {
            if (response) {
                jQuery.post(controllerRoute + "/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function excluir()
{
    var sel = getCheckboxMarcados(multipleSelectionCheckboxName);

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post(controllerRoute + "/delete", {sel: sel}, callBackAjax, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function salvarPosicionamento()
{
    if(verificaFiltros()){
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posição', function(response) {
            if (!response) {
                return false;
            }
            var ordenacao = [];
            var total = jQuery('[name="itensMenu_length"] option:selected').text();
            if(total == 'Todos')
                total = 10;
            var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
            jQuery('#itensMenu tbody .checker input').each(function(i){
                ordenacao.push({
                    id: jQuery(this).val(),
                    ordenacao: (i + 1 + ordenacaoBase)
                });
            });
            jQuery.ajax('menu/ajaxAtualizarOrdenacao', {
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
}

function verificaFiltros(){
    if(jQuery("#subsite option:selected").val() != ""){
        var tipoMenuVal = jQuery("#tipoMenu option:selected").val();
        if((tipoMenuVal == "n2" ||  tipoMenuVal == "n3") && jQuery("#vinculoPai").val() == 0){
            jAlert('Os campos para alterar/salvar posicionamentos só ficarão habilitadas quando o filtro "Vinculo Pai" estiver selecionado', "Aviso");
            return false;
        }
    }else{
        jAlert('Os campos para alterar/salvar posicionamentos só ficarão habilitadas quando o filtro "Site" estiver selecionado', "Aviso");
        return false;
    }
    return true;
}

function atualizaVinculoPai(){
    var vinculoPai = jQuery('#vinculoPai');
    vinculoPai.find('option').remove().end().append('<option value="0">Vinculo Pai</option>').val(0);
    jQuery('#uniform-vinculoPai span').text('Vinculo Pai');

    var tipoMenu = jQuery("#tipoMenu option:selected").val();
    var subsite = jQuery("#subsite option:selected").val();

    if((tipoMenu == "n2" || tipoMenu == "n3") && subsite != ""){
        jQuery.ajax('menu/ajaxVinculoPai', {
            type: 'POST',
            dataType: 'json',
            data: {
                tipoMenu: tipoMenu,
                subsite: subsite
            },
            success: function(json) {
                jQuery.each(json, function(i, j) {
                    vinculoPai.append('<option value='+j.id+'>'+j.titulo+'</option>');
                });
            }
        });
    }
}