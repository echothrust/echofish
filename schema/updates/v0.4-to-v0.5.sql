SET NAMES utf8 COLLATE 'utf8_unicode_ci';
SET FOREIGN_KEY_CHECKS=0;
SET time_zone = "+00:00";

ALTER TABLE `archive_bh` DROP `updated_at`;
ALTER TABLE `archive` DROP `updated_at`;
ALTER TABLE `syslog` DROP `updated_at`;

ALTER TABLE `archive_bh` CHANGE `created_at` `created_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `archive` CHANGE `created_at` `created_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `syslog` CHANGE `created_at` `created_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

ALTER TABLE `archive` CHANGE `host` `host` INT UNSIGNED NOT NULL ;
ALTER TABLE `syslog` CHANGE `host` `host` INT UNSIGNED NOT NULL ;

ALTER TABLE `host` DROP PRIMARY KEY;
ALTER TABLE `host` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
DROP INDEX `short` ON `host`; /* This is meant for the unique index */
CREATE INDEX `short` on `host`(`short`);
CREATE UNIQUE INDEX `host_details` ON `host`(`fqdn`,`short`,`ip`);

UPDATE `syslog` as t1 LEFT JOIN host as t2 ON t1.host=t2.ip SET t1.host=t2.id; 
UPDATE `archive` as t1 LEFT JOIN host as t2 ON t1.host=t2.ip SET t1.host=t2.id; 

ALTER TABLE `host` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `trail` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `archive_unparse` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `archive_parser` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
