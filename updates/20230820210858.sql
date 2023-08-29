CREATE TABLE `shop`
(
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(255)     NOT NULL,
    `description` VARCHAR(255)     NOT NULL DEFAULT ''
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `shop_stock`
(
    `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `shop_id`        INT(11) UNSIGNED NOT NULL,
    `item_id`        INT(11) UNSIGNED NOT NULL,
    `price_id`       INT(11) UNSIGNED NOT NULL,
    `price_quantity` INT(11) UNSIGNED DEFAULT 1,
    FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items (item_id) ON DELETE CASCADE,
    FOREIGN KEY (price_id) REFERENCES items (item_id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


INSERT INTO `shop`(id,
                   name,
                   description)
VALUES (1, 'Grocery', 'If you need supplies, you are at the right place.'),
       (2, 'Armory', 'Weapons, armors, we got them all.');


INSERT INTO items(name, grade, worth)
VALUES
    ('Animal hide', 1, 5),
    ('Leather', 1, 5);

UPDATE items SET worth=2 WHERE item_id=2;
UPDATE items SET worth=18 WHERE item_id=3;

INSERT INTO `shop_stock`(
                         shop_id, item_id, price_id, price_quantity
) VALUES
      (1, 2, 1, 3),
      (1, 5, 1, 7),
      (2, 3, 1, 54)
;
