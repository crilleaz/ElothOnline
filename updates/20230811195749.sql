ALTER TABLE inventory
    MODIFY item_id INT(10) NOT NULL,
    ADD FOREIGN KEY (item_id) REFERENCES items(id);