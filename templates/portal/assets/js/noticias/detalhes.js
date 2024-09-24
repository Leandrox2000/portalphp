(function($) {
    function commentForm(selector)
    {
        var element = $(selector);
        var _MSG_ENVIADA = 'Seu comentário foi enviado e está aguardando moderação.';
        var _MSG_NAO_ENVIADA = 'Não foi possível enviar sua mensagem. Desculpe pelo transtorno.';
        var _MSG_AGUARDE = 'Aguarde...';

        element.validate({
            rules: {
                nome: { required: true },
                email: { required: true, email: true },
                comentario: { required: true }
            },
            messages: {
                nome: { required: "Este campo é requerido" },
                email: {
                    required: "Este campo é requerido",
                    email: "Informe um e-mail válido."
                },
                comentario: { required: "Este campo é requerido" }
            },
            submitHandler: function(form) {
                $.ajax($(form).attr('action'), {
                    data: element.serialize(),
                    type: 'POST',
                    beforeSend: function(){
                        element.find('.message').css('color', '#fff');
                        element.find('.message').html(_MSG_AGUARDE);
                    },
                    success: function(){
                        element.find('.message').html(_MSG_ENVIADA);
                        element.get(0).reset(); // Limpa o formulário
                    },
                    error: function(){
                        element.find('.message').html(_MSG_NAO_ENVIADA);
                    }
                });
            }
        });
    }
    
    function loadComments()
    {
        var noticia = $('input[name="noticia"]').val();
        var _MSG_SEM_COMENTARIOS = 'Sem comentários.';
        var _MSG_CARREGANDO = 'Carregando...';

        $.ajax(BASE_URL + 'noticias/listaComentarios/' + noticia + '?now=' + new Date().getTime(), {
            beforeSend: function(){
                $('#lista-comentarios').html(_MSG_CARREGANDO);
            },
            success: function(data){
                if (data.count > 0) {
                    $('#lista-comentarios').html(data.html);
                    $('#num-comentarios').html('(' + data.count + ')');
                } else {
                    $('#lista-comentarios').html(_MSG_SEM_COMENTARIOS);
                }
            }
        });
    }

    $(document).ready(function(){
//        commentForm('#comentar');
//        loadComments();
    });

})(jQuery);