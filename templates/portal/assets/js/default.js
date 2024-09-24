 var addthis_share = 
 { 
    templates: { twitter: '{{url}}' }
 };
$(document).ready(function() {
    if (localStorage['contraste']=='sim') {
        // setar contraste
        $("body").toggleClass("contrast");
        localStorage['contraste'] = "sim";
    }
    $.datepicker.regional['pt-BR'] = {
        closeText: 'Fechar',
        prevText: '&#x3c;Anterior',
        nextText: 'Pr&oacute;ximo&#x3e;',
        currentText: 'Hoje',
        monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        dayNames: ['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira',
            'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado'],
        dayNamesShort: ['Do', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sa'],
        dayNamesMin: ['Do', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sa'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);

    $(".carousel-red").cycle();
    $(".carousel-yellow").cycle();
    $(".galeria-fotos").cycle();
    $(".noticias-subsites").cycle();

    $(".busca #date_from").datepicker({
        onClose: function(selectedDate) {
            $(".busca #date_to").datepicker("option", "minDate", selectedDate);
        }
    });

    $(".busca #date_to").datepicker({
        onClose: function(selectedDate) {
            $(".busca #date_from").datepicker("option", "maxDate", selectedDate);
        }
    });

    $('.datepicker').each(function() {
        $(this).datepicker();
    });

    $('.tooltiped').tooltip({delay: {show: 200}});
    
    $('[data-toggle=popover]').popover({ trigger: "hover" });

    $(".accordion li").click(function() {
        if ($(this).is(".active")) {
            $(this).removeClass("active").find("div").slideUp();
        } else {
            $(".accordion li").removeClass("active").find("div").slideUp();
            $(this).addClass("active").find("div").slideDown();
        }
    });

    $.rvFontsize({
        targetSection: 'section', // elemento para aumentar a fonte
        store: false, // store.min.js required! armazena preferência em um cookie
        controllers: {
            appendTo: '.bts-accessibility', // elemento que controla o tamanho da fonte
            showResetButton: false, // mostrar botão resetar
            template: '<ul class="clearfix">' +
                    '<li class="float-left">' +
                    '<a id="acessibilidadeBotao" class="rvfs-acessibilidadeBotao acessibilidadeBotao" href="#botao de acessibilidade">Acessibilidade botão</a>' +
                    '</li>' +
                    '<li class="float-left">' +
                    '<a class="rvfs-increase increase-font" href="#aumentar fonte">Aumentar Fonte botão</a>' +
                    '</li>' +
                    '<li class="float-left">' +
                    '<a class="rvfs-decrease decrease-font" href="#diminuir fonte">Diminuir Fonte botão</a>' +
                    '</li>' +
                    '<li class="float-left">' +
                    '<a class="contrast" href="#contraste">Contraste</a>' +
                    '</li>' +
                    '</ul>',
        }
    });

    $(".bts-accessibility .contrast").on('click', function(e) {
        e.preventDefault();
        $("body").toggleClass("contrast");
        if (localStorage['contraste']=='sim') {
            localStorage['contraste'] = 'nao';
        } else {
            localStorage['contraste'] = 'sim';
        }
    });

    $.validator.addMethod("email", function(value, element) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        value = value.trim();
        return re.test(value);
    }, "Formato de e-mail inválido");
    
    $("#descricao-site").trunc(
        700,
        "<span class='read-more-link'>[[leia mais]]</span>",
        "<span class='read-less-link'>[[ocultar]]</span>"
    );
    
    var acess = false;
    var acessCount = 0;
    $('#acessibilidadeBotao').focus(function(){
        acess = true;
        acessCount = 0;
    });
    
    $('#acessibilidadeBotao').focusout(function(){
        acess = false;
        acessCount = 0;
    });
    
    $('#acessibilidadeBotao').keydown(function(e){
        if(e.keyCode == 13){
            if(acess){
                switch (acessCount){
                    case 0:
                        document.getElementById('menu-master').scrollIntoView();
                        acessCount = 1;
                        break;
                    case 1:
                        document.getElementById('espacoPesquisa').scrollIntoView();
                        acessCount = 2;
                        break;
                    case 2:
                        if($("#container").length)
                            document.getElementById('container').scrollIntoView();
                        else if($("#banner").length)
                            document.getElementById('banner').scrollIntoView();
                        acessCount = 3;
                        break;
                    case 3:
                        document.getElementById('footer').scrollIntoView();
                        acessCount = 0;
                        break;
                }
            }
        }
        return false;
    });
    
    $('#linkPesquisa, .addthis_button_facebook, .addthis_button_twitter, .addthis_button_email, .addthis_button_linkedin').click(function(){
        return false;
    });
    
});
