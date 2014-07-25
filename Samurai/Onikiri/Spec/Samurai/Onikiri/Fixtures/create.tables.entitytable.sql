CREATE TEMPORARY TABLE spec_samurai_onikiri_entity_table (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(256),
    mail VARCHAR(256),
    PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO
    spec_samurai_onikiri_entity_table (name, mail)
VALUES
    ('Satoshinosuke', 'scholar@hayabusa-lab.jp')
;

