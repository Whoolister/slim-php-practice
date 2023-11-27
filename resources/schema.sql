DROP DATABASE IF EXISTS `slim_php_practice`;
CREATE DATABASE IF NOT EXISTS `slim_php_practice`
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`users`
(
    `id`         INT                                                                       NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(50)                                                               NOT NULL CHECK ( `first_name` <> ''),
    `last_name`  VARCHAR(50)                                                               NOT NULL CHECK ( `last_name` <> ''),
    `email`      VARCHAR(50)                                                               NOT NULL CHECK ( `email` <> ''),
    `password`   VARCHAR(50)                                                               NOT NULL CHECK ( `password` <> ''),
    `role`       ENUM ('BARTENDER', 'CERVECERO', 'COCINERO', 'PASTELERO', 'MOZO', 'SOCIO') NOT NULL,
    `active`     BOOLEAN                                                                   NOT NULL DEFAULT TRUE,
    PRIMARY KEY (`id`),
    UNIQUE INDEX (`email` ASC)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`products`
(
    `id`             INT                                                    NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(100)                                           NOT NULL CHECK ( `name` <> ''),
    `price`          DECIMAL(10, 2)                                         NOT NULL CHECK ( `price` > 0),
    `estimated_time` INT                                                    NOT NULL CHECK ( `estimated_time` > 0 ),
    `type`           ENUM ('TRAGO O VINO', 'CERVEZA', 'PLATILLO', 'POSTRE') NOT NULL,
    `active`         BOOLEAN                                                NOT NULL DEFAULT TRUE,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`tables`
(
    `id`     INT                                                  NOT NULL AUTO_INCREMENT,
    `status` ENUM ('ESPERANDO', 'COMIENDO', 'PAGANDO', 'CERRADA') NOT NULL DEFAULT 'CERRADA',
    `active` BOOLEAN                                              NOT NULL DEFAULT TRUE,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`orders`
(
    `id`          VARCHAR(5)  NOT NULL DEFAULT (SUBSTRING(MD5(RAND()) FROM 1 FOR 5)) CHECK (`id` REGEXP '^[a-zA-Z0-9]{5}$'),
    `table_id`    INT         NOT NULL,
    `client_name` VARCHAR(50) NOT NULL CHECK ( `client_name` <> ''),
    `status`      ENUM ('PENDIENTE', 'PREPARANDO', 'LISTO', 'SERVIDO', 'PAGADO'),
    FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`),
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`order_items`
(
    `id`         INT        NOT NULL AUTO_INCREMENT,
    `order_id`   VARCHAR(5) NOT NULL,
    `product_id` INT        NOT NULL,
    `start_time` TIMESTAMP  NULL CHECK ( IF(`start_time` IS NULL, `end_time` IS NULL, TRUE) ),
    `end_time`   TIMESTAMP  NULL CHECK ( `end_time` >= `start_time` ),
    `status`     ENUM ('PENDIENTE', 'PREPARANDO', 'LISTO') AS (CASE
                                                                   WHEN `start_time` IS NULL THEN 'PENDIENTE'
                                                                   WHEN `end_time` IS NULL THEN 'PREPARANDO'
                                                                   ELSE 'LISTO' END) VIRTUAL,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`surveys`
(
    `id`                INT        NOT NULL AUTO_INCREMENT,
    `order_id`          VARCHAR(5) NOT NULL,
    `table_rating`      INT        NOT NULL CHECK ( `table_rating` > 0 AND `table_rating` <= 10),
    `restaurant_rating` INT        NOT NULL CHECK ( `restaurant_rating` > 0 AND `restaurant_rating` <= 10),
    `waiter_rating`     INT        NOT NULL CHECK ( `waiter_rating` > 0 AND `waiter_rating` <= 10),
    `chef_rating`       INT        NOT NULL CHECK ( `chef_rating` > 0 AND `chef_rating` <= 10),
    `comment`           VARCHAR(66) DEFAULT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
    PRIMARY KEY (`id`)
);

INSERT INTO `slim_php_practice`.`users` (first_name, last_name, email, password, role)
VALUES ('John', 'Doe', 'johndoe@gmail.com', '123456', 'SOCIO'),
       ('Jane', 'Doe', 'janedoe@gmail.com', '567890', 'SOCIO'),
       ('Eladio', 'Carrion', 'miprimerachamba@gmail.com', 'humac40', 'SOCIO'),
       ('Miguel', 'Perez', 'miguelperez@yahoo.com', '123456', 'MOZO'),
       ('Juan', 'Gonzalez', 'juangonzalez@hotmail.com', '123456', 'MOZO'),
       ('Maria', 'Garcia', 'mariagarcia@outlook.com', '123456', 'CERVECERO'),
       ('Jose', 'Rodriguez', 'joserodriguez@gmail.com', '123456', 'COCINERO'),
       ('Carlos', 'Sanchez', 'carlosanchez@gmail.com', '123456', 'PASTELERO'),
       ('Ana', 'Romero', 'anaromero@yahoo.com', '123456', 'BARTENDER'),
       ('Laura', 'Sosa', 'laurasosa@hotmail.com', '123456', 'COCINERO');

INSERT INTO `slim_php_practice`.`products` (`name`, `price`, `estimated_time`, `type`)
VALUES ('Asado', 400.50, 60, 'PLATILLO'),
       ('Empanadas', 20.00, 30, 'PLATILLO'),
       ('Choripán', 50.75, 15, 'PLATILLO'),
       ('Provoleta', 100.25, 20, 'PLATILLO'),
       ('Locro', 300.00, 120, 'PLATILLO'),
       ('Puchero', 350.00, 180, 'PLATILLO'),
       ('Milanesa a Caballo', 200.50, 45, 'PLATILLO'),
       ('Carbonada', 250.00, 90, 'PLATILLO'),
       ('Alfajores', 15.00, 10, 'POSTRE'),
       ('Churros con dulce de leche', 25.00, 15, 'POSTRE'),
       ('Pastelitos', 30.00, 20, 'POSTRE'),
       ('Budín de pan', 40.00, 30, 'POSTRE'),
       ('Flan con dulce de leche', 35.00, 25, 'POSTRE'),
       ('Helado', 45.00, 5, 'POSTRE'),
       ('Malbec', 200.00, 1000, 'TRAGO O VINO'),
       ('Torrontés', 180.00, 50, 'TRAGO O VINO'),
       ('Cabernet Sauvignon', 190.00, 33, 'TRAGO O VINO'),
       ('Hamburguesa de Garbanzo', 500, 300, 'PLATILLO'),
       ('Cerveza Quilmes', 70.00, 15, 'CERVEZA'),
       ('Cerveza Patagonia', 85.00, 22.5, 'CERVEZA'),
       ('Cerveza Salta', 80.00, 10, 'CERVEZA'),
       ('Cerveza Andes', 75.00, 11, 'CERVEZA'),
       ('Sorrentinos', 150.00, 30, 'PLATILLO'),
       ('Ravioles', 140.00, 35, 'PLATILLO'),
       ('Ñoquis', 120.00, 20, 'PLATILLO'),
       ('Spaghettis', 110.00, 25, 'PLATILLO'),
       ('Tallarines', 100.00, 30, 'PLATILLO'),
       ('Pionono', 80.00, 45, 'POSTRE'),
       ('Tiramisú', 90.00, 50, 'POSTRE'),
       ('Medialunas', 10.00, 10, 'POSTRE'),
       ('Chocotorta', 95.00, 40, 'POSTRE'),
       ('Torta Galesa', 80.00, 60, 'POSTRE'),
       ('Cerveza Corona', 75.00, 15, 'CERVEZA'),
       ('Cerveza Isenbeck', 65.00, 30, 'CERVEZA'),
       ('Cerveza Schneider', 70.00, 30, 'CERVEZA'),
       ('Cerveza Iguana', 85.00, 30, 'CERVEZA'),
       ('Cerveza Otro Mundo', 90.00, 20, 'CERVEZA'),
       ('Bonarda', 195.00, 80, 'TRAGO O VINO'),
       ('Daikiri', 255.50, 70, 'TRAGO O VINO'),
       ('Tempranillo', 205.00, 120, 'TRAGO O VINO'),
       ('Chardonnay', 220.00, 93, 'TRAGO O VINO'),
       ('Pinot Noir', 210.00, 800, 'TRAGO O VINO'),
       ('Merlot', 200.00, 70, 'TRAGO O VINO'),
       ('Fideos con tuco', 130.00, 45, 'PLATILLO'),
       ('Fideos con pesto', 140.00, 50, 'PLATILLO'),
       ('Fideos con salsa blanca', 150.00, 55, 'PLATILLO'),
       ('Fideos con bolognesa', 160.00, 60, 'PLATILLO'),
       ('Fideos con salsa rosa', 170.00, 65, 'PLATILLO'),
       ('Postre balcarce', 85.00, 35, 'POSTRE'),
       ('Rogel', 90.00, 40, 'POSTRE'),
       ('Crumble de manzana', 95.00, 45, 'POSTRE'),
       ('Volcán de chocolate', 100.00, 50, 'POSTRE'),
       ('Lemon pie', 105.00, 55, 'POSTRE'),
       ('Cerveza Antares', 95.00, 13, 'CERVEZA'),
       ('Cerveza Bieckert', 100.00, 20, 'CERVEZA'),
       ('Cerveza Warsteiner', 105.00, 30, 'CERVEZA'),
       ('Cerveza Brahma', 110.00, 14, 'CERVEZA'),
       ('Cerveza Kunstmann', 115.00, 90, 'CERVEZA'),
       ('Pinot Grigio', 215.00, 15, 'TRAGO O VINO'),
       ('Chenin Blanc', 210.00, 10, 'TRAGO O VINO'),
       ('Tannat', 220.00, 10, 'TRAGO O VINO'),
       ('Sauvignon Blanc', 220.00, 20, 'TRAGO O VINO');

INSERT INTO `slim_php_practice`.`tables`
VALUES (),
       (),
       (),
       (),
       (),
       (),
       (),
       (),
       (),
       ();