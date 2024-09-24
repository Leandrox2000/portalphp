CREATE TABLE tb_compromisso
(
  id_compromisso bigserial NOT NULL,
  no_titulo character varying(120) NOT NULL,
  dt_compromisso_inicial timestamp without time zone NOT NULL,
  dt_compromisso_final timestamp without time zone,
  ds_local character varying(150) NOT NULL,
  ds_participantes text,
  dt_inicial timestamp without time zone NOT NULL,
  dt_final timestamp without time zone,
  dt_cadastro timestamp without time zone NOT NULL,  
  st_publicado integer NOT NULL DEFAULT 0,
  at_username character varying(150),
  CONSTRAINT pk_compromisso PRIMARY KEY (id_compromisso)
);

CREATE TABLE tb_compromisso_agenda_direcao
(
  id_agenda_direcao bigint NOT NULL,
  id_compromisso bigint NOT NULL,
  CONSTRAINT pk_compromisso_agenda_direcao PRIMARY KEY (id_agenda_direcao, id_compromisso),
  CONSTRAINT fk_compromisso_agenda_direcao_agenda FOREIGN KEY (id_agenda_direcao)
      REFERENCES tb_agenda_direcao (id_agenda_direcao) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_compromisso_agenda_direcao_compromisso FOREIGN KEY (id_compromisso)
      REFERENCES tb_compromisso (id_compromisso) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT uk_compromisso_agenda_direcao_id_compromisso UNIQUE (id_agenda_direcao, id_compromisso)
);