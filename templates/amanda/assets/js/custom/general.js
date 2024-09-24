/*
 * 	Additional function for this template
 *	Written by ThemePixels	
 *	http://themepixels.com/
 *
 *	Copyright (c) 2012 ThemePixels (http://themepixels.com)
 *	
 *	Built for Amanda Premium Responsive Admin Template
 *  http://themeforest.net/category/site-templates/admin-templates
 */

jQuery.noConflict();
jQuery(document).ready(function() {
    // Esconde a área "Visualizar" do CKEditor, dentro do modal de "Imagem"
    setInterval(function(){
        jQuery('.ImagePreviewLoader').parents('.cke_dialog_ui_vbox:first').hide();
    }, 500);

    ///// SHOW/HIDE USERDATA WHEN USERINFO IS CLICKED ///// 

    jQuery('.userinfo').click(function() {
        if (!jQuery(this).hasClass('active')) {
            jQuery('.userinfodrop').fadeIn();
            jQuery(this).addClass('active');
        } else {
            jQuery('.userinfodrop').hide();
            jQuery(this).removeClass('active');
        }
        //remove notification box if visible
        jQuery('.notification').removeClass('active');
        jQuery('.noticontent').remove();

        return false;
    });


    ///// SHOW/HIDE NOTIFICATION /////

    jQuery('.notification a').click(function() {
        var t = jQuery(this);
        var url = t.attr('href');
        if (!jQuery('.noticontent').is(':visible')) {
            jQuery.post(url, function(data) {
                t.parent().append('<div class="noticontent">' + data + '</div>');
            });
            //this will hide user info drop down when visible
            jQuery('.userinfo').removeClass('active');
            jQuery('.userinfodrop').hide();
        } else {
            t.parent().removeClass('active');
            jQuery('.noticontent').hide();
        }
        return false;
    });



    ///// SHOW/HIDE BOTH NOTIFICATION & USERINFO WHEN CLICKED OUTSIDE OF THIS ELEMENT /////


    jQuery(document).click(function(event) {
        var ud = jQuery('.userinfodrop');
        var nb = jQuery('.noticontent');

        //hide user drop menu when clicked outside of this element
        if (!jQuery(event.target).is('.userinfodrop')
                && !jQuery(event.target).is('.userdata')
                && ud.is(':visible')) {
            ud.hide();
            jQuery('.userinfo').removeClass('active');
        }

        //hide notification box when clicked outside of this element
        if (!jQuery(event.target).is('.noticontent') && nb.is(':visible')) {
            nb.remove();
            jQuery('.notification').removeClass('active');
        }
    });


    ///// NOTIFICATION CONTENT /////

    jQuery('.notitab a').on('click', function() {
        var id = jQuery(this).attr('href');
        jQuery('.notitab li').removeClass('current'); //reset current 
        jQuery(this).parent().addClass('current');
        if (id == '#messages')
            jQuery('#activities').hide();
        else
            jQuery('#messages').hide();

        jQuery(id).fadeIn();
        return false;
    });



    ///// SHOW/HIDE VERTICAL SUB MENU /////	

    jQuery('.vernav > ul li a, .vernav2 > ul li a').each(function() {
        var url = jQuery(this).attr('href');
        jQuery(this).click(function() {
            if (jQuery(url).length > 0) {
                if (jQuery(url).is(':visible')) {
                    if (!jQuery(this).parents('div').hasClass('menucoll') &&
                            !jQuery(this).parents('div').hasClass('menucoll2'))
                        jQuery(url).slideUp();
                } else {
                    jQuery(url).parent().parent().children().each(function() {
                        if (jQuery(this).children('a').attr('href') !== url) {
                            jQuery(this).children('ul').slideUp();
                            jQuery(this).children('ul').children('li').children('ul').slideUp();
                        }
                    });
                    jQuery(url).slideDown();
                }
                return false;
            }
        });
    });


    ///// SHOW/HIDE SUB MENU WHEN MENU COLLAPSED /////
    jQuery('.menucoll ul li, .menucoll2 ul li').on('mouseenter mouseleave', function(e) {
        if (e.type == 'mouseenter') {
            jQuery(this).addClass('hover');
            jQuery(this).children('ul').show();
        } else {
            jQuery(this).removeClass('hover').find('ul').hide();
        }
    });


    ///// HORIZONTAL NAVIGATION (AJAX/INLINE DATA) /////	

    jQuery('.hornav a').click(function() {

        //this is only applicable when window size below 450px
        if (jQuery(this).parents('.more').length == 0)
            jQuery('.hornav li.more ul').hide();

        //remove current menu
        jQuery('.hornav li').each(function() {
            jQuery(this).removeClass('current');
        });

        jQuery(this).parent().addClass('current');	// set as current menu


        var url = jQuery(this).attr('href');

        if (jQuery(this).attr('link')) {
            location = url;
            return false;
        }


        if (jQuery(url).length > 0) {
            jQuery('.contentwrapper .subcontent').hide();
            jQuery(url).show();
        } else {
            jQuery.post(url, function(data) {
                jQuery('#contentwrapper').html(data);
                jQuery('.stdtable input:checkbox').uniform();	//restyling checkbox
            });
        }
        return false;
    });


    ///// NOTIFICATION CLOSE BUTTON /////

    jQuery('.notibar .close').click(function() {
        jQuery(this).parent().fadeOut(function() {
            jQuery(this).remove();
        });
    });


    ///// COLLAPSED/EXPAND LEFT MENU /////
    jQuery('.togglemenu').click(function() {
        if (!jQuery(this).hasClass('togglemenu_collapsed')) {

            if (jQuery('.vernav').length > 0) {
                if (jQuery('.vernav').hasClass('iconmenu')) {
                    jQuery('body').addClass('withmenucoll');
                    jQuery('.iconmenu').addClass('menucoll');
                } else {
                    jQuery('body').addClass('withmenucoll');
                    jQuery('.vernav').addClass('menucoll').find('ul').hide();
                }
            } else if (jQuery('.vernav2').length > 0) {
                jQuery('body').addClass('withmenucoll2');
                jQuery('.iconmenu').addClass('menucoll2');
            }

            jQuery(this).addClass('togglemenu_collapsed');

            jQuery('.iconmenu ul li a').each(function() {
                var label = jQuery(this).text();
                jQuery('<li><span>' + label + '</span></li>')
                        .insertBefore(jQuery(this).next().children('li:first-child'));
            });
        } else {

            //if(jQuery('.iconmenu').hasClass('vernav')) {
            if (jQuery('.vernav').length > 0) {
                if (jQuery('.vernav').hasClass('iconmenu')) {
                    jQuery('body').removeClass('withmenucoll');
                    jQuery('.iconmenu').removeClass('menucoll');
                } else {
                    jQuery('body').removeClass('withmenucoll');
                    jQuery('.vernav').removeClass('menucoll').find('ul').show();
                }
            } else if (jQuery('.vernav2').length > 0) {
                jQuery('body').removeClass('withmenucoll2');
                jQuery('.iconmenu').removeClass('menucoll2');
            }
            jQuery(this).removeClass('togglemenu_collapsed');

            //jQuery('.iconmenu ul ul li:first-child').remove();
        }
    });


    ///// RESPONSIVE /////
    if (jQuery(document).width() < 640) {
        jQuery('.togglemenu').addClass('togglemenu_collapsed');
        if (jQuery('.vernav').length > 0) {

            jQuery('.iconmenu').addClass('menucoll');
            jQuery('body').addClass('withmenucoll');
            jQuery('.centercontent').css({marginLeft: '56px'});
            if (jQuery('.iconmenu').length == 0) {
                jQuery('.togglemenu').removeClass('togglemenu_collapsed');
            } else {
                jQuery('.iconmenu > ul > li > a').each(function() {
                    var label = jQuery(this).text();
                    jQuery('<li><span>' + label + '</span></li>')
                            .insertBefore(jQuery(this).parent().find('ul li:first-child'));
                });
            }

        } else {
            jQuery('.iconmenu').addClass('menucoll2');
            jQuery('body').addClass('withmenucoll2');
            jQuery('.centercontent').css({marginLeft: '36px'});

            jQuery('.iconmenu ul li a').each(function() {
                var label = jQuery(this).text();
                jQuery('<li><span>' + label + '</span></li>')
                        .insertBefore(jQuery(this).next().children('li:first-child'));
            });
        }
    }




    ///// ON RESIZE WINDOW /////
    jQuery(window).resize(function() {

        if (jQuery(window).width() > 640) {
            jQuery('.centercontent').removeAttr('style');
        }

    });


    ///// CHANGE THEME /////
    jQuery('.changetheme a').click(function() {
        var c = jQuery(this).attr('class');
        jQuery.post('../mod-usuario/usuario-controller.php?action=alterar_tema', {tema: c})
        if (jQuery('#addonstyle').length == 0) {
            if (c != 'default') {
                jQuery('head').append('<link id="addonstyle" rel="stylesheet" href="../css/style.' + c + '.css" type="text/css" />');
            }
        } else {
            if (c != 'default') {
                jQuery('#addonstyle').attr('href', '../css/style.' + c + '.css');
                //jQuery.cookie("addonstyle", c, { path: '/' });
            } else {
                jQuery('#addonstyle').remove();
                //jQuery.cookie("addonstyle", null);
            }
        }
    });

    ///// DATE PICKER AND MASK DATE /////	
    jQuery(".price-value").inputmask("R$ 9{0,12}[,99]");
    jQuery(".date").mask("99/99/9999").datepicker();
    jQuery(".hora").mask("99:99");
    jQuery(".telefone").mask("(99) 9999-9999?9");
    jQuery(".number").spinner({min: jQuery(this).attr("min"), max: jQuery(this).attr("max"), step: jQuery(this).attr("step"), });


    jQuery(".chzn-select").chosen();

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
            ;
        });

        jQuery.uniform.update();
    });


    //Ajuda CMs
    jQuery('.ajudaCms').colorbox({width: '1200px', height: '780px', onComplete: carregeAjuda});

    jQuery("#subsitesTop").change(function(){
        var base = baseUrl();
        jQuery.post(base+"login/alterarSite/"+jQuery(this).val(), function(){
            //location.href = "";
        });
    });
   

    /* Valida as datas nas listagens */
    jQuery(".stdtable #data_inicial, .stdtable #dataInicial").change(function() {
        var dataInicialElement = jQuery("#data_inicial, #dataInicial");
        var dataFinalElement = jQuery("#data_final, #dataFinal");
        var value = jQuery(this).val();
        
        if (value == "") {
            return false;
        }
        
        var dataInicial = dataInicialElement.datepicker('getDate').getTime();

        if (dataFinalElement.val() != "") {
            var dataFinal = dataFinalElement.datepicker('getDate').getTime();
        } else {
            var dataFinal = null;
        }

        

        if (validaData(value) && (dataInicial == "" || dataFinal == null || dataInicial < dataFinal)) {
            table.fnDraw();
        } else {
            jAlert("Data inválida")
            jQuery(this).val("");
        }
    });

    jQuery(".stdtable #data_final, .stdtable #dataFinal").change(function() {
        var dataInicialElement = jQuery("#data_inicial, #dataInicial");
        var dataFinalElement = jQuery("#data_final, #dataFinal");
        var value = jQuery(this).val();
        
        if (value == "") {
            return false;
        }
        
        var dataFinal = dataFinalElement.datepicker('getDate').getTime();

        if (dataInicialElement.val() != "") {
            var dataInicial = dataInicialElement.datepicker('getDate').getTime();
        } else {
            var dataInicial = null;
        }
        
               
        if (validaData(value) && (dataInicial == "" || dataInicial < dataFinal)) {
            table.fnDraw();
        } else {
            jAlert("Data inválida")
            jQuery(this).val("");
        }
    });

});

jQuery(function() {
    jQuery('input').easyTooltip();
});

function baseUrl() {
    return jQuery("base").attr("href");
}


function getCheckboxMarcados(idTable)
{
    var count = 0;
    var sel = new Array();
    var ch = jQuery('#' + idTable).find('tbody input:checkbox');     //get each checkbox in a table

    //check if there is/are selected row in table
    ch.each(function() {
        if (jQuery(this).is(':checked')) {
            sel[count] = jQuery(this).val();
            count++;
        }
    });

    return sel;
}

function  carregeAjuda()
{
    // Smart Wizard 	
    jQuery('#wizard').smartWizard({
        enableAllSteps: !0,
        onShowStep: onShowStepCallback,
        onFinish: onFinishCallback,
        labelPrevious: "Voltar",
        labelFinish: "MANUAL COMPLETO"
    });
}

function onShowStepCallback()
{
    jQuery('#contentwrapper .stepContainer').css('height', '800px !important');
}

function onFinishCallback()
{
    window.open(baseUrl()+"templates/amanda/assets/ajuda/PORTAL_IPHAN_ManualUsuario_CMS.pdf","Manual Completo");
    jQuery.colorbox.close();
}

function validaPeriodo(idDataInicial, idDataFinal, idHoraInicial, idHoraFinal)
{
    //Armazena o valor dos campos
    var campoDataInicial = jQuery("#" + idDataInicial).val();
    var campoHoraInicial = jQuery("#" + idHoraInicial).val();
    var campoDataFinal = jQuery("#" + idDataFinal).val();
    var campoHoraFinal = jQuery("#" + idHoraFinal).val();
    
    //Valida se as datas já foram preenchidas
    if(campoDataInicial == "" || campoDataFinal == ""){
        return false;
    }
    
    //Armazena a Data Inicial
    var dataAgora = new Date();

    //Data inicial
    var arrayDataInicial = campoDataInicial.split("/");
    var horaInicial = (campoHoraInicial !== "") ? campoHoraInicial : "00:00";
    
    // Cria data utilizando o formato ISO 8601
    var dataInicial = new Date(
        arrayDataInicial[2] + "-"
        + arrayDataInicial[1] + "-"
        + arrayDataInicial[0]
        + "T" + horaInicial
    );
    
    
    // Exemplo: 2014-12-01T22:00

    // Data final
    var arrayDataFinal = campoDataFinal.split("/");
    var horaFinal = (campoHoraFinal !== "") ? campoHoraFinal : "23:59";
    
    // Cria data utilizando o formato ISO 8601
    var dataFinal = new Date(
        arrayDataFinal[2] + "-"
        + arrayDataFinal[1] + "-"
        + arrayDataFinal[0]
        + "T" + horaFinal
    );
    
    
    // A data final não pode ser menor que a data inicial
    if (dataFinal.getTime() < dataInicial.getTime()) {
        jQuery("#" + idDataFinal).val("");
        jQuery("#" + idHoraFinal).val("");
    }

//    // A data final não pode ser menor que a data atual
//    if(dataFinal < dataAgora) {
//        jQuery("#" + idDataFinal).val("");
//        jQuery("#" + idHoraFinal).val("");
//    }

//    var data_inicial    = jQuery("#" + id_data_inicial).val();
//    var data_final      = jQuery("#" + id_data_final).val();
//    var hora_inicial    = jQuery("#" + id_hora_inicial).val();
//    var hora_final      = jQuery("#" + id_hora_final).val() != "" ? jQuery("#" + id_hora_final).val() : "23:59";
//    
//    var date_now = new Date();    
//    var now = new Date(date_now.getFullYear() + "-" + (date_now.getMonth() + 1) + "-" + date_now.getDate()+" 00:00:00").getTime(); 
//    
//    var array_dti = data_inicial.split("/");
//    var array_dtf = data_final.split("/");
//    var array_hi = hora_inicial.split(":");
//    var array_hf = hora_final.split(":");
//
//    var periodo_inicial = new Date(array_dti[2] + "-" + array_dti[1] + "-" + " " + array_dti[0] + " " + array_hi[0] + ":" + array_hi[1]).getTime();
//    var periodo_final = new Date(array_dtf[2] + "-" + array_dtf[1] + "-" + array_dtf[0] + " " +  array_hf[0] + ":" + array_hf[1]).getTime();
//    
//    console.log(periodo_final);
//    console.log(periodo_inicial);
//
//    //Realizar a validação das datas anteriores
//    if(periodo_final < now){
//        jQuery("#" + id_data_final).val("");
//    }
//    
//    if(periodo_inicial < now){
//        jQuery("#" + id_data_inicial).val("");
//    }
//    
//    //Realiza validação dos períodos
//    if (data_inicial != "" && data_final != "" && hora_inicial != "" && hora_final != "") {
//        if (periodo_final < periodo_inicial) {
//            jQuery("#" + id_data_final).val("");
//            jQuery("#" + id_hora_final).val("");
//        }
//    }
}

function desativarDatas()
{
    
}

function validaData(value) {
    	//contando chars
	if(value.length!=10 && value.length!=0) {
            return false;
        }
	// verificando data
	var data 		= value;
	var dia 		= data.substr(0,2);
	var barra1		= data.substr(2,1);
	var mes 		= data.substr(3,2);
	var barra2		= data.substr(5,1);
	var ano 		= data.substr(6,4);
        
	if ( data.length!=10 || barra1!="/" || barra2!="/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia>31 || mes>12 ) {
            return false;
        }
	if ( ( mes==4 || mes==6 || mes==9 || mes==11 ) &&dia==31 ) {
            return false;
        }
	if ( mes==2 && (dia>29 || ( dia==29 && ano%4!=0 ) ) ) {
            return false;
        }
	if (ano < 1900) {
            return false;
        }
	return true;
}