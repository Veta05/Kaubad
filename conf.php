<?php
$connection = new mysqli('localhost', 'Aia21', '123456', 'aia21');
$connection->set_charset('UTF8');

/*$connection = new mysqli('d109453.mysql.zonevs.eu', 'd109453_aia', '49409170264', 'd109453_aiabaas');
$connection->set_charset('UTF8');*/

/*CREATE TABLE kaubagrupid(
    id int PRIMARY KEY AUTO_INCREMENT,
            kaubagrupp varchar(100)
        );
          CREATE TABLE kaubad(
    id int PRIMARY KEY AUTO_INCREMENT,
            kaubanimi varchar(100),
            hind int,
            kaubagrupp_id int,
            FOREIGN KEY (kaubagrupp_id) REFERENCES kaubagrupid(id)
        );
        INSERT INTO kaubagrupid(kaubagrupp) VALUES ('mänguasjad');
        INSERT INTO kaubad(kaubanimi, hind, kaubagrupp_id) VALUES ('auto', 25, 1);*/
