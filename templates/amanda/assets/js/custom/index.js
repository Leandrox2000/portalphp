jQuery(document).ready(function() {

    ///// TRANSFORM CHECKBOX /////							
    jQuery('input:checkbox').uniform();

    ///// LOGIN FORM SUBMIT /////
    jQuery('#login').submit(function() {

        if (jQuery('#username').val() == '') {
            jQuery('.nousername div').text('Insira o usuário!');
            jQuery('.nousername').fadeIn();
            jQuery('.nopassword').hide();
            return false;
        } else if (jQuery('#username').val() != '' && jQuery('#password').val() == '') {
            jQuery('.nousername div').text('Insira sua senha');
            jQuery('.nousername').fadeIn();
            return false;
        } else {
            var url = jQuery('#login').attr('action');
            jQuery('#enviar').text('Aguarde...');
            
            jQuery.post(url, jQuery('#login').serialize(), function(response) {
                jQuery('#enviar').text('Entrar');
                if (response.logado) {
                    location.href = response.page;
                } else {
                    jQuery('.nousername').show();
                    jQuery('.loginmsg').text(response.error[0]);
                }
            }, 'json');
            return false;
        }

    });

    ///// ADD PLACEHOLDER /////
    jQuery('#username').attr('placeholder', 'Usuário');
    jQuery('#password').attr('placeholder', 'Senha');
    jQuery('.keep').click(function() {
        location.href = '../mod-usuario/usuario-envia-lembrar-senha.php';
    }).css('cursor', 'pointer').mouseenter(function() {
        jQuery(this).css('color', '#EEEEEE');
    }).mouseout(function() {
        jQuery(this).css('color', '#CCCCCC');
    });
});
