CREATE TABLE IF NOT EXISTS tb_edital_status
(
    id_status_categoria bigserial NOT NULL,
    no_status character varying(50) NOT NULL,
    nu_ordem bigint DEFAULT 0,
    CONSTRAINT pk_edital_status PRIMARY KEY (id_status_categoria),
    CONSTRAINT uk_edital_status_id_status_categoria UNIQUE (id_status_categoria),
    CONSTRAINT uk_edital_status_no_status UNIQUE (no_status)
);


ALTER TABLE tb_edital_status
   ALTER COLUMN no_status TYPE character varying(255);

ALTER TABLE tb_edital_status
    ADD COLUMN nu_ordem_column integer NOT NULL DEFAULT 1;