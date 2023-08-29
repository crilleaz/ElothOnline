ALTER TABLE inventory DROP FOREIGN KEY inventory_ibfk_1;

DROP TABLE shop_stock;
DROP TABLE droplist;
DROP TABLE items;
DROP TABLE dungeons;
DROP TABLE monster;
DROP TABLE shop;
DROP TABLE item_effect;

ALTER TABLE users ADD UNIQUE(`anv`);
