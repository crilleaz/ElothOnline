ALTER TABLE items ADD worth INT(10) NOT NULL DEFAULT 1;
ALTER TABLE items ADD CHECK (worth>=0);