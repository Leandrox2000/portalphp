CREATE TABLE IF NOT EXISTS tb_status_lcc
(
  id_status_lcc bigserial NOT NULL,
  no_status_lcc character varying(100) NOT NULL,
  nu_ordem bigint,
  CONSTRAINT pk_status_lcc PRIMARY KEY (id_status_lcc),
  CONSTRAINT uk_status_lcc_id_status_lcc UNIQUE (id_status_lcc),
  CONSTRAINT uk_status_lcc_no_status_lcc UNIQUE (no_status_lcc)
);

ALTER TABLE tb_status_lcc
   ADD COLUMN nu_ordem_column integer NOT NULL DEFAULT 1;