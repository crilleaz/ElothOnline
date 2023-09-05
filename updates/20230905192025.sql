ALTER TABLE players
    ADD state TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER id;

UPDATE players SET state=1 WHERE in_combat=1;

ALTER TABLE players DROP in_combat;
