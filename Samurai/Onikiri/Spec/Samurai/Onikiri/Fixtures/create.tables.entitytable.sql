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




CREATE TEMPORARY TABLE spec_samurai_onikiri_fullstack_table (
    id INT(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    name VARCHAR(256) NOT NULL DEFAULT 'who' COMMENT '名前',
    mail VARCHAR(256) NULL DEFAULT NULL COMMENT 'メールアドレス',
    introduction TEXT NOT NULL COMMENT '紹介',
    birthday DATE NOT NULL DEFAULT '0000-00-00' COMMENT '誕生日',
    gender ENUM('male', 'female') NOT NULL DEFAULT 'male' COMMENT '性別',
    height DECIMAL(5,2) NULL DEFAULT NULL COMMENT '身長',
    PRIMARY KEY (id)
) ENGINE = InnoDB;

