-- remove a chave composta
alter table tb_agenda_direcao_site drop constraint pk_agenda_direcao_site;

-- adiciona a nova chave primaria
alter table tb_agenda_direcao_site add column id_agenda_direcao_site serial primary key;

-- adiciona a coluna de ordenacao
alter table tb_agenda_direcao_site add column nu_ordem int;