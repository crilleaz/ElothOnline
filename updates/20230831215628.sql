CREATE TABLE `activity`
(
    `id`              INT UNSIGNED     NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `character_id`    INT              NOT NULL UNIQUE,
    `name`            VARCHAR(50)      NOT NULL,
    `selected_option` TINYINT UNSIGNED NOT NULL,
    `started_at`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `checked_at`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_reward_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (character_id) REFERENCES players (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;
