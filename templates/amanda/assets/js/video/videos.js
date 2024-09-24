function videosSubcrud() {

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
        window.open('video/form', '_blank');
    };
    
    /**
     * Remove um vídeo da seleção
     * 
     * @param integer id
     */
    public.removerRelacionado = function(id)
    {
        var ids = jQuery('#videosBanco').val();
        ids = ids.replace(id + ',', '');
        ids = ids.replace(',' + id, '');
        ids = ids.replace(id, '');

        jQuery('#video-relacionado-' + id).remove();
        jQuery('#videosBanco').val(ids);
    };

    /**
     * Abre o modal de vídeos
     */
    public.abrirVideos = function()
    {
        var link = baseUrl();

        jQuery.colorbox({
            href: link + "video/videos",
            width: "75%",
            height: "90%",
            onComplete: private.loadDataTableSubCrud
        });
    };

    /**
     * Carrega o datatable e o checkall
     */
    private.loadDataTableSubCrud = function() {
        table = jQuery('#videosRelacionadosModal').dataTable({
            "sPaginationType": "full_numbers",
            "bSort": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "video/pagination",
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
    private.validarQtdVideos = function()
    {
        var qtdMaxima = 5;
        var videosSel = jQuery("#videosRelacionadosModal input[type=checkbox]:checked");

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
        if (private.validarQtdVideos()) {
            private.setarVideosRelacionadosCheckbox();
            jQuery.colorbox.close();
        } else {
            alert('Você selecionou mais que 5 vídeos.');
        }
    };

    /**
     * Adiciona os itens selecionados ao campo hidden
     */
    private.setarVideosRelacionadosCheckbox = function()
    {
        v = new Array();

        jQuery("#videosRelacionadosModal input[type=checkbox]:checked").each(function() {
            if (jQuery(this).val() != "") {
                v.push(jQuery(this).val());
            }
        });

        jQuery('#videosBanco').val(v);

        jQuery.get("video/getHtmlVideos/" + v).done(function(data) {
            jQuery('#videosSelecionados').html(data);
            jQuery('#list-videos-relacionados').sortable();
        });
    };
    
    return public;
}

var videosModalObject = new videosSubcrud();