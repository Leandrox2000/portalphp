var table;
var tipoMarcacao;
var categoriaImagem;
var ids;

function setarPaginasEstaticas()
{
    v = jQuery("input[name='pagina']:checked").val();

    if (v !== '' && v !== undefined) {
        jQuery('#idEntidade').val(v);
        jQuery.get("menu/getHtmlPagina/" + v).done(function(data) {
            jQuery('#paginaSelecionada').html(data);
            jQuery('input:checkbox, input:radio, select').uniform();
        });
    } else {
        jQuery('#idEntidade').val('');
        jQuery('#paginaSelecionada').html('');
    }
}

function excluirPagina(id)
{
    jQuery('#idEntidade').val("");
    jQuery('#paginaSel'+id).remove();
}

/**
 * 
 * Atualiza a lista
 */
function atualizar() {
    table.fnDraw();
}

/**
 * 
 * @returns Fecha o colorbox
 */
function fechar()
{
    jQuery.colorbox.close();
}

/**
 * 
 * Armazena os ids no hidden e fecha o colorbox
 */
function escolherPaginaFechar()
{
    setarPaginasEstaticas();
    jQuery.colorbox.close();
}

/**
 * 
 * Abre colorbox com imagens agrupadas em radio
 */
function abrirPaginas()
{
    
    var link = baseUrl();

    jQuery.colorbox({href: link + "menu/paginas", width: "75%", height: "90%", onComplete: loadDataTablePaginas});
}

/**
 * 
 * Carrega o datatable e o checkall
 */
function loadDataTablePaginas() {
    table = jQuery('#paginas').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio,select').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "menu/paginacaoPaginas",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "tipo", "value": tipoMarcacao});
            aoData.push({"name": "ids", "value": ids});
        }
    });

    jQuery("input.checkall").on("change", function() {
        var parentTable = jQuery(this).parents('table');
        var ch = parentTable.find('tbody input:checkbox');
        var checked;

        if (jQuery(this).is(':checked')) {
            checked = true;
        } else {
            checked = false;
        }

        jQuery.each(ch, function(index, element) {
            jQuery(element).attr('checked', checked);
        });

        jQuery.uniform.update();
    });
}


