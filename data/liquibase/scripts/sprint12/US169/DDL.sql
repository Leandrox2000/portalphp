CREATE TABLE tb_agenda_direcao
(
  id_agenda_direcao bigserial NOT NULL,
  no_titulo character varying(120) NOT NULL,
  dt_inicial timestamp without time zone NOT NULL,
  dt_final timestamp without time zone,
  dt_cadastro timestamp without time zone NOT NULL,  
  st_publicado integer NOT NULL DEFAULT 0,
  at_username character varying(150),
  CONSTRAINT pk_agenda_direcao PRIMARY KEY (id_agenda_direcao)
);
  
CREATE TABLE tb_agenda_direcao_site
(
  id_agenda_direcao bigint NOT NULL,
  id_site bigint NOT NULL,
  CONSTRAINT pk_agenda_direcao_site PRIMARY KEY (id_agenda_direcao, id_site),
  CONSTRAINT fk_agenda_direcao_site_agenda FOREIGN KEY (id_agenda_direcao)
      REFERENCES tb_agenda_direcao (id_agenda_direcao) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_agenda_direcao_site_site FOREIGN KEY (id_site)
      REFERENCES tb_site (id_site) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT uk_agenda_direcao_site_id_agenda_direcao UNIQUE (id_agenda_direcao, id_site)
);

CREATE TABLE tb_agenda_direcao_responsavel
(
  id_agenda_direcao_responsavel bigserial NOT NULL,
  id_agenda_direcao bigint NOT NULL,
  ds_responsavel character varying(150) NOT NULL,
  CONSTRAINT pk_agenda_direcao_responsavel PRIMARY KEY (id_agenda_direcao_responsavel),
  CONSTRAINT fk_agenda_direcao_responsavel_agenda FOREIGN KEY (id_agenda_direcao)
      REFERENCES tb_agenda_direcao (id_agenda_direcao) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT uk_agenda_direcao_responsavel_id_agenda_direcao UNIQUE (id_agenda_direcao, ds_responsavel)
);
CREATE TABLE tb_pai_agenda_direcao_site
(
  id_agenda_direcao bigint NOT NULL,
  id_site bigint NOT NULL,
  CONSTRAINT fk_pai_agenda_direcao_site_agenda_direcao FOREIGN KEY (id_agenda_direcao)
      REFERENCES tb_agenda_direcao (id_agenda_direcao) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_pai_agenda_direcao_site_site FOREIGN KEY (id_site)
      REFERENCES tb_site (id_site) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);