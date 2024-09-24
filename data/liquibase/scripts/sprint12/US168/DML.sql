INSERT INTO tb_funcionalidade_menu(
            ds_funcionalidade, no_url, no_entidade)
    VALUES ('Agenda da Direção', 'agendaDirecao', 'Entity\AgendaDirecao');
	
UPDATE tb_menu SET
  ds_url_externa = null,
  id_funcionalidade_menu = ( SELECT id_funcionalidade_menu FROM tb_funcionalidade_menu WHERE no_url = 'agendaDirecao' )
WHERE no_titulo = 'Agenda da Direção';	