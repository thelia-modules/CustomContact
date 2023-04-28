# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `custom_contact_i18n` ADD (`success_message` TEXT(255));

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
