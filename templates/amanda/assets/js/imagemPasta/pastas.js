jQuery(document).ready(function(){
    jQuery('select').uniform();
});

function adicionarPasta()
{
    if (jQuery("#novaPasta").val().trim() != "" && jQuery("#categorias").val() != "") {
        jQuery.post(baseUrl() + "imagemPasta/salvarPasta", {nome: jQuery("#novaPasta").val().trim(), categoria: jQuery("#categorias").val()}, callbackPastas, "json");
    } else {
        jAlert("Os campos categoria e pasta devem ser preenchidos! ");
    }
}

function carregaPastas()
{
    jQuery.post(baseUrl() + "imagemPasta/getTablePastas", function(table) {
        jQuery("#tablePastas").replaceWith(table); 
    });
}

function excluirPasta(id)
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "imagemPasta/excluiPasta", {id: id}, callbackPastas, "json");
                }

            }
    );
}

function editaPasta(id)
{
    jQuery("#formEdicao").prev().show();
    jQuery("#formEdicao").remove();
    jQuery("#pasta" + id)
            .hide()
            .parent()
            .append(
                    "<span id=\"formEdicao\">"
                    +
                        "<input type='text' name='edicaoPasta' id='edicaoPasta' value='" + jQuery("#pasta" + id).text() + "'/>"
                        +
                        "<input type='hidden' name='idPasta' id='idPasta' value='" + id + "'/>"
                        +
                        "<a href=\"javascript:editarPasta()\" class=\"btn btn_ btn_no_icon radius2\"><span>Salvar</span></a>"
                        +
                    "</span>"
                    );
}


function editarPasta()
{
    var id = jQuery("#idPasta").val();
    var pasta = jQuery("#edicaoPasta").val().trim();
    
    if (id != "" && pasta != "") {
        jQuery.post(baseUrl() + "imagemPasta/salvarPasta", {nome: pasta, id: id}, callbackPastas, "json");
    } else {
        jAlert("Os campos categoria e pasta devem ser preenchidos! ");
    }
    
}

function callbackPastas(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaPastas();
    } else if(data.response == 2){
    	jAlert(
            data.error, 
            'Erro ao Excluir!'
        );
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    
    jQuery("#novaPasta")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarPasta()'})
            .removeClass("disabled");
    
    jQuery("#categorias")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarPasta()'})
            .removeClass("disabled");
}