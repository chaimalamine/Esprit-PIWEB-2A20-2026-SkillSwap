-- ============================================================
-- TABLE PARRAINAGE - SkillSwap
-- À exécuter dans phpMyAdmin sur la base de données 'skillswap'
-- ============================================================

CREATE TABLE IF NOT EXISTS `parrainage` (
  `id_parrainage`    INT(11)      NOT NULL AUTO_INCREMENT,
  `id_parrain`       INT(11)      NOT NULL,
  `id_filleul`       INT(11)      DEFAULT NULL,
  `email_invite`     VARCHAR(255) NOT NULL,
  `code_parrainage`  VARCHAR(20)  NOT NULL UNIQUE,
  `statut`           ENUM('en_attente','accepte','expire') NOT NULL DEFAULT 'en_attente',
  `date_invitation`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_acceptation` DATETIME     DEFAULT NULL,
  `credits_parrain`  INT(11)      NOT NULL DEFAULT 10,
  `credits_filleul`  INT(11)      NOT NULL DEFAULT 5,
  PRIMARY KEY (`id_parrainage`),
  KEY `fk_parrain`  (`id_parrain`),
  KEY `fk_filleul`  (`id_filleul`),
  CONSTRAINT `fk_parrain`  FOREIGN KEY (`id_parrain`)  REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `fk_filleul`  FOREIGN KEY (`id_filleul`)  REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
