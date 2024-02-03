CREATE TABLE wp_ponto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE,
    diaSemana VARCHAR(10),
    entrada TIME,
    entradaAlmoco TIME,
    retornoAlmoco TIME,
    saida TIME,
    user_id INT
);



/* depois criado o obs */

ALTER TABLE wp_ponto
ADD obs TEXT;
