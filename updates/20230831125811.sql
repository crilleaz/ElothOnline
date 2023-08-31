ALTER TABLE players ADD `user_id` INT(11) NOT NULL DEFAULT 0 AFTER id;
UPDATE players p SET p.`user_id`=(SELECT u.`id` FROM users u WHERE u.anv=p.name);
ALTER TABLE players
    ADD race TINYINT(2) UNSIGNED NOT NULL DEFAULT 1 AFTER user_id,
    MODIFY user_id INT(11) NOT NULL,
    ADD FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

ALTER TABLE inventory ADD `character_id` INT(11) NOT NULL DEFAULT 0 AFTER id;
UPDATE inventory i SET i.`character_id`=(SELECT p.`id` FROM players p WHERE p.name=i.username);
ALTER TABLE inventory
    DROP COLUMN username,
    MODIFY character_id INT(11) NOT NULL,
    ADD FOREIGN KEY (character_id) REFERENCES players(id) ON DELETE CASCADE;

ALTER TABLE hunting ADD `character_id` INT(11) NOT NULL DEFAULT 0 AFTER id;
UPDATE hunting h SET h.`character_id`=(SELECT p.`id` FROM players p WHERE p.name=h.username);
ALTER TABLE hunting
    DROP COLUMN username,
    MODIFY character_id INT(11) NOT NULL,
    ADD FOREIGN KEY (character_id) REFERENCES players(id) ON DELETE CASCADE;

ALTER TABLE log ADD `character_id` INT(11) NOT NULL DEFAULT 0 AFTER id;
UPDATE log l SET l.`character_id`=(SELECT p.`id` FROM players p WHERE p.name=l.username);
ALTER TABLE log
    DROP COLUMN username,
    MODIFY character_id INT(11) NOT NULL,
    ADD FOREIGN KEY (character_id) REFERENCES players(id) ON DELETE CASCADE;

ALTER TABLE users MODIFY anv VARCHAR(50) NOT NULL;
