
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- custom_contact
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `custom_contact`;

CREATE TABLE `custom_contact`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255),
    `return_url` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- custom_contact_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `custom_contact_i18n`;

CREATE TABLE `custom_contact_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `field_configuration` TEXT,
    `email` VARCHAR(255),
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `custom_contact_i18n_fk_f71245`
        FOREIGN KEY (`id`)
        REFERENCES `custom_contact` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
