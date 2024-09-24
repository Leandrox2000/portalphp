--Insere todas as ordens de galerias de subsites que não estão inseridas atualmente. As ordens serão inseridas inicialmente sem valor 
INSERT INTO tb_galeria_ordem (id_site, id_galeria) 
(
SELECT id_site, id_galeria FROM tb_galeria_site 
EXCEPT 
SELECT id_site, id_galeria FROM tb_galeria_ordem
)

--Insere todas as ordens de vídeos de subsites que não estão inseridas atualmente
INSERT INTO tb_video_ordem (id_site, id_video) 
(SELECT id_site, id_video FROM tb_video_site 
EXCEPT 
SELECT id_site, id_video FROM tb_video_ordem)
