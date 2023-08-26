CREATE TABLE `item_effect`
(
    `item_id` INT(11) UNSIGNED NOT NULL,
    `name`    VARCHAR(255)     NOT NULL,
    `type`    TINYINT(1)       NOT NULL,
    `power`   INT(4)           NOT NULL DEFAULT 1
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `item_effect`(item_id, name, type, power) VALUE (2, 'Restore stamina', 1, 5);
