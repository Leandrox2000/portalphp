function fototecasSubcrud() {

    var private = {};
    var public = {};
    var table;

    /**
     * Atualiza a lista
     */
    public.atualizar = function()
    {
        table.fnDraw();
    };

    /**
     * Abre nova janela com o form de inclusão de imagem
     */
    public.nova = function()
    {
        window.open('fototeca/form', '_blank');
    };
    
    /**
     * Remove um vídeo da seleção
     * 
     * @param integer id
     */
    public.removerRelacionado = function(id)
    {
        var ids = jQuery('#fototecasBanco').val();
        ids = ids.replace(id + ',', '');
        ids = ids.replace(',' + id, '');
        ids = ids.replace(id, '');

        jQuery('#fototeca-relacionada-' + id).remove();
        jQuery('#fototecasBanco').val(ids);
    };

    /**
     * Abre o modal de vídeos
     */
    public.abrirFototecas = function()
    {
        var link = baseUrl();

        jQuery.colorbox({
            href: link + "fototeca/fototecas",
            width: "75%",
            height: "90%",
            onComplete: private.loadDataTableSubCrud
        });
    };

    /**
     * Carrega o datatable e o checkall
     */
    private.loadDataTableSubCrud = function() {
        table = jQuery('#fototecasRelacionadasModal').dataTable({
            "sPaginationType": "full_numbers",
            "bSort": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "fototeca/pagination",
            "fnDrawCallback": function(oSettings) {
                jQuery('input:checkbox,input:radio,select').uniform();
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
    };

    /**
     * Valida a quantidade de vídeos selecionados pelo usuário
     * 
     * @returns {Boolean}
     */
    private.validarQtdFototecas = function()
    {
        var qtdMaxima = 5;
        var videosSel = jQuery("#fototecasRelacionadasModal input[type=checkbox]:checked");

        if (videosSel.length > qtdMaxima) {
            return false;
        }

        return true;
    };

    /**
     * Fecha o colorbox
     */
    public.fechar = function()
    {
        jQuery.colorbox.close();
    };

    /**
     * Armazena os ids no hidden e fecha o colorbox
     */
    public.escolherFechar = function()
    {
        if (private.validarQtdFototecas()) {
            private.setarFototecasRelacionadasCheckbox();
            jQuery.colorbox.close();
        } else {
            alert('Você selecionou mais que 5 fototecas.');
        }
    };

    /**
     * Adiciona os itens selecionados ao campo hidden
     */
    private.setarFototecasRelacionadasCheckbox = function()
    {
        v = new Array();

        jQuery("#fototecasRelacionadasModal input[type=checkbox]:checked").each(function() {
            if (jQuery(this).val() != "") {
                v.push(jQuery(this).val());
            }
        });

        jQuery('#fototecasBanco').val(v);

        jQuery.get("fototeca/getHtmlVideos/" + v).done(function(data) {
            jQuery('#fototecasSelecionadas').html(data);
        });
    };
    
    return public;
}

var fototecasModalObject = new fototecasSubcrud();