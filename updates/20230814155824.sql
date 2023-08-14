ALTER TABLE inventory DROP FOREIGN KEY inventory_ibfk_1;

ALTER TABLE items
    DROP PRIMARY KEY,
    DROP COLUMN `id`,
    MODIFY COLUMN `item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;

ALTER TABLE monster
    DROP PRIMARY KEY,
    DROP COLUMN `id`,
    MODIFY COLUMN `monster_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;

CREATE TABLE `droplist`
(
    `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `monster_id`   INT(11) UNSIGNED NOT NULL,
    `item_id`      INT(11) UNSIGNED NOT NULL,
    `quantity_min` INT(11) UNSIGNED    DEFAULT 1,
    `quantity_max` INT(11) UNSIGNED    DEFAULT 1,
    `chance`       TINYINT(2) UNSIGNED DEFAULT 100,
    FOREIGN KEY (monster_id) REFERENCES monster (monster_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items (item_id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

ALTER TABLE inventory
    MODIFY `item_id` INT(11) UNSIGNED NOT NULL,
    ADD FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE;


INSERT INTO droplist(monster_id, item_id, quantity_min, quantity_max, chance)
VALUES (1, 2, 1, 1, 15),
       (1, 1, 1, 7, 100),
       (2, 1, 5, 5, 100),
       (3, 1, 40, 50, 100),
       (4, 3, 1, 1, 100),
       (4, 1, 50, 100, 100)
;
