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
    PRIMARY KEY (`id`)
);

INSERT INTO `slim_php_practice`.`users` (first_name, last_name, email, password, role)
VALUES ('John', 'Doe', 'johndoe@gmail.com', '123456', 'SOCIO'),
       ('Jane', 'Doe', 'janedoe@gmail.com', '567890', 'SOCIO'),
       ('Eladio', 'Carrion', 'miprimerachamba@gmail.com', 'humac40', 'SOCIO');

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
    `id`     VARCHAR(5)                                           NOT NULL DEFAULT RIGHT(UUID(), 5) CHECK (`id` REGEXP '^[\w\d]{5}$'),
    `status` ENUM ('ESPERANDO', 'COMIENDO', 'PAGANDO', 'CERRADA') NOT NULL,
    `active` BOOLEAN                                              NOT NULL DEFAULT TRUE,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`orders`
(
    `id`          INT         NOT NULL AUTO_INCREMENT,
    `table_id`    VARCHAR(5)  NOT NULL,
    `client_name` VARCHAR(50) NOT NULL CHECK ( `client_name` <> ''),
    FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`),
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`order_items`
(
    `id`         INT       NOT NULL AUTO_INCREMENT,
    `order_id`   INT       NOT NULL,
    `product_id` INT       NOT NULL,
    `start_time` TIMESTAMP NULL CHECK ( IF(`start_time` IS NULL, `end_time` IS NULL, TRUE) ),
    `end_time`   TIMESTAMP NULL CHECK ( `end_time` >= `start_time` ),
    `status`     ENUM ('PENDIENTE', 'PREPARANDO', 'LISTO') AS (CASE
                                                                   WHEN `start_time` IS NULL THEN 'PENDIENTE'
                                                                   WHEN `end_time` IS NULL THEN 'PREPARANDO'
                                                                   ELSE 'LISTO' END) VIRTUAL,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `slim_php_practice`.`surveys`
(
    `id`                INT NOT NULL AUTO_INCREMENT,
    `order_id`          INT NOT NULL,
    `table_rating`      INT NOT NULL CHECK ( `table_rating` > 0 AND `table_rating` <= 10),
    `restaurant_rating` INT NOT NULL CHECK ( `restaurant_rating` > 0 AND `restaurant_rating` <= 10),
    `waiter_rating`     INT NOT NULL CHECK ( `waiter_rating` > 0 AND `waiter_rating` <= 10),
    `chef_rating`       INT NOT NULL CHECK ( `chef_rating` > 0 AND `chef_rating` <= 10),
    `comment`           VARCHAR(66) DEFAULT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
    PRIMARY KEY (`id`)
);

INSERT INTO `slim_php_practice`.`products` (name, price, estimated_time, type)
VALUES ('Milanesa', 15.0, 15000, 'PLATILLO')
RETURNING id;