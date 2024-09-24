function adicionarCategoria()
{
    if (jQuery("#novaCategoria").val().trim() != "") {
        jQuery.post(baseUrl() + "categoriaLcc/salvarCategoria", {nome: jQuery("#novaCategoria").val().trim()}, callbackCategorias, "json");
    } else {
        jAlert("Informe a categoria");
    }
}

function carregaCategorias()
{
    jQuery.post(baseUrl() + "categoriaLcc/getTableCategorias", function(table) {
        jQuery("#tableCategorias").replaceWith(table);
    });
}

function excluirCategoria(id)
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "categoriaLcc/excluiCategoria", {id: id}, callbackCategorias, "json");
                }

            }
    );
}

function editaCategoria(id)
{
    jQuery("#formEdicao").prev().show();
    jQuery("#formEdicao").remove();
    jQuery("#categoria" + id)
            .hide()
            .parent()
            .append(
                "<span id=\"formEdicao\">"
                    +
                    "<input type='text' name='edicaoCategoria' id='edicaoCategoria' value='" + jQuery("#categoria" + id).text() + "'/>"
                    +
                    "<input type='hidden' name='idCategoria' id='idCategoria' value='" + id + "'/>"
                    +
                    "<a href=\"javascript:editarCategoria()\" class=\"btn btn_ btn_no_icon radius2\"><span>Salvar</span></a>"
                    +
                 "</span>"
                    );
    jQuery("#novaCategoria")
            .attr('disabled', 'disabled')
            .next()
            .attr({'href': 'javascript:void(0)'})
            .toggleClass("disabled");
}


function editarCategoria()
{
    var id = jQuery("#idCategoria").val();
    var categoria = jQuery("#edicaoCategoria").val().trim();
    
    if (categoria != "") {
        jQuery.post(baseUrl() + "categoriaLcc/salvarCategoria", {nome: categoria, id: id}, callbackCategorias, "json");
    } else {
        jAlert("Informe a categoria.");
    }

}

function callbackCategorias(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaCategorias();
    } else if (data.response == 2) {
        jAlert(
            data.error, 
            'Erro ao Excluir!'
        );
        carregaCategorias();
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    jQuery("#novaCategoria")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarCategoria()'})
            .removeClass("disabled");
}