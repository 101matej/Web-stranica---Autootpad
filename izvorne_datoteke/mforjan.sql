-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema WebDiP2021x024
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema WebDiP2021x024
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `WebDiP2021x024` ;
USE `WebDiP2021x024` ;

-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`tip_korisnika`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`tip_korisnika` (
  `tip_korisnika_id` INT NOT NULL AUTO_INCREMENT,
  `naziv` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`tip_korisnika_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`korisnik`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`korisnik` (
  `korisnik_id` INT NOT NULL AUTO_INCREMENT,
  `ime` VARCHAR(45) NOT NULL,
  `prezime` VARCHAR(45) NOT NULL,
  `datum_rodenja` DATE NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `korisnicko_ime` VARCHAR(45) NOT NULL,
  `lozinka` VARCHAR(45) NOT NULL,
  `lozinka_sha256` CHAR(64) NOT NULL,
  `broj_neuspjesne_prijave` TINYINT NOT NULL,
  `status` TINYINT NOT NULL,
  `aktivacijski_kod` VARCHAR(45) NOT NULL,
  `validiran` TINYINT NOT NULL,
  `vrijeme_registracije` TIMESTAMP NULL,
  `tip_korisnika` INT NOT NULL,
  PRIMARY KEY (`korisnik_id`),
  INDEX `korisnik_tip_korisnika_tip_korisnika_id_fk_idx` (`tip_korisnika` ASC),
  CONSTRAINT `korisnik_tip_korisnika_tip_korisnika_id_fk`
    FOREIGN KEY (`tip_korisnika`)
    REFERENCES `WebDiP2021x024`.`tip_korisnika` (`tip_korisnika_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`tip_radnje`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`tip_radnje` (
  `tip_radnje_id` INT NOT NULL AUTO_INCREMENT,
  `naziv` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`tip_radnje_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`dnevnik_rada`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`dnevnik_rada` (
  `dnevnik_rada_id` INT NOT NULL AUTO_INCREMENT,
  `korisnik` INT NOT NULL,
  `tip_radnje` INT NOT NULL,
  `radnja` VARCHAR(200) NULL,
  `upit` VARCHAR(1000) NULL,
  `datum_vrijeme` DATETIME NOT NULL,
  PRIMARY KEY (`dnevnik_rada_id`),
  INDEX `fk_dnevnik_rada_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  INDEX `dnevnik_rada_tip_radnje_tip_radnje_id_fk_idx` (`tip_radnje` ASC),
  CONSTRAINT `dnevnik_rada_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `dnevnik_rada_tip_radnje_tip_radnje_id_fk`
    FOREIGN KEY (`tip_radnje`)
    REFERENCES `WebDiP2021x024`.`tip_radnje` (`tip_radnje_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`stanje_racuna`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`stanje_racuna` (
  `stanje_racuna_id` INT NOT NULL AUTO_INCREMENT,
  `stanje` INT NOT NULL,
  `banka` VARCHAR(50) NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`stanje_racuna_id`),
  INDEX `stanje_racuna_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  UNIQUE INDEX `korisnik_UNIQUE` (`korisnik` ASC),
  CONSTRAINT `stanje_racuna_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`marka_vozila`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`marka_vozila` (
  `marka_vozila_id` INT NOT NULL AUTO_INCREMENT,
  `naziv` VARCHAR(45) NOT NULL,
  `datum_kreiranja` DATE NOT NULL,
  PRIMARY KEY (`marka_vozila_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`moderator`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`moderator` (
  `korisnik` INT NOT NULL,
  `marka_vozila` INT NOT NULL,
  PRIMARY KEY (`korisnik`, `marka_vozila`),
  INDEX `moderator_marka_vozila_marka_vozila_id_fk_idx` (`marka_vozila` ASC),
  CONSTRAINT `moderator_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `moderator_marka_vozila_marka_vozila_id_fk`
    FOREIGN KEY (`marka_vozila`)
    REFERENCES `WebDiP2021x024`.`marka_vozila` (`marka_vozila_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`zahtjev`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`zahtjev` (
  `zahtjev_id` INT NOT NULL AUTO_INCREMENT,
  `tip` VARCHAR(45) NOT NULL,
  `godina` INT NOT NULL,
  `masa_vozila` INT NOT NULL,
  `snaga_motora` INT NOT NULL,
  `vrsta_goriva` VARCHAR(45) NOT NULL,
  `slika` VARCHAR(50) NOT NULL,
  `marka_vozila` INT NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`zahtjev_id`),
  INDEX `zahtjev_marka_vozila_marka_vozila_id_fk_idx` (`marka_vozila` ASC),
  INDEX `zahtjev_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  CONSTRAINT `zahtjev_marka_vozila_marka_vozila_id_fk`
    FOREIGN KEY (`marka_vozila`)
    REFERENCES `WebDiP2021x024`.`marka_vozila` (`marka_vozila_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `zahtjev_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`vozilo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`vozilo` (
  `vozilo_id` INT NOT NULL AUTO_INCREMENT,
  `tip` VARCHAR(45) NOT NULL,
  `godina` INT NOT NULL,
  `masa_vozila` INT NOT NULL,
  `snaga_motora` INT NOT NULL,
  `vrsta_goriva` VARCHAR(45) NOT NULL,
  `slika` VARCHAR(50) NOT NULL,
  `marka_vozila` INT NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`vozilo_id`),
  INDEX `vozilo_marka_vozila_marka_vozila_id_fk_idx` (`marka_vozila` ASC),
  INDEX `vozilo_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  CONSTRAINT `vozilo_marka_vozila_marka_vozila_id_fk`
    FOREIGN KEY (`marka_vozila`)
    REFERENCES `WebDiP2021x024`.`marka_vozila` (`marka_vozila_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `vozilo_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`kategorija_dijela`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`kategorija_dijela` (
  `kategorija_dijela_id` INT NOT NULL AUTO_INCREMENT,
  `naziv` VARCHAR(45) NOT NULL,
  `datum_kreiranja` DATE NOT NULL,
  PRIMARY KEY (`kategorija_dijela_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`dio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`dio` (
  `dio_id` INT NOT NULL AUTO_INCREMENT,
  `naziv` VARCHAR(45) NOT NULL,
  `proizvodac` VARCHAR(45) NOT NULL,
  `masa` INT NOT NULL,
  `opis` VARCHAR(45) NOT NULL,
  `cijena` INT NOT NULL,
  `datum_kreiranja` DATE NOT NULL,
  `raspolozivo` TINYINT NOT NULL,
  `vozilo` INT NOT NULL,
  `kategorija_dijela` INT NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`dio_id`),
  INDEX `dio_vozilo_vozilo_id_fk_idx` (`vozilo` ASC),
  INDEX `dio_kategorija_dijela_kategorija_dijela_id_fk_idx` (`kategorija_dijela` ASC),
  INDEX `dio_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  CONSTRAINT `dio_vozilo_vozilo_id_fk`
    FOREIGN KEY (`vozilo`)
    REFERENCES `WebDiP2021x024`.`vozilo` (`vozilo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `dio_kategorija_dijela_kategorija_dijela_id_fk`
    FOREIGN KEY (`kategorija_dijela`)
    REFERENCES `WebDiP2021x024`.`kategorija_dijela` (`kategorija_dijela_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `dio_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`sadrzava`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`sadrzava` (
  `kategorija_dijela` INT NOT NULL,
  `marka_vozila` INT NOT NULL,
  PRIMARY KEY (`kategorija_dijela`, `marka_vozila`),
  INDEX `sadrzava_marka_vozila_marka_vozila_id_fk_idx` (`marka_vozila` ASC),
  CONSTRAINT `sadrzava_kategorija_dijela_kategorija_dijela_id_fk`
    FOREIGN KEY (`kategorija_dijela`)
    REFERENCES `WebDiP2021x024`.`kategorija_dijela` (`kategorija_dijela_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `sadrzava_marka_vozila_marka_vozila_id_fk`
    FOREIGN KEY (`marka_vozila`)
    REFERENCES `WebDiP2021x024`.`marka_vozila` (`marka_vozila_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`procjena`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`procjena` (
  `procjena_id` INT NOT NULL AUTO_INCREMENT,
  `cijena` INT NOT NULL,
  `stanje` VARCHAR(45) NOT NULL,
  `nedostatak` VARCHAR(200) NOT NULL,
  `prihvaceno` TINYINT NOT NULL,
  `zahtjev` INT NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`procjena_id`),
  INDEX `procjena_zahtjev_zahtjev_id_fk_idx` (`zahtjev` ASC),
  INDEX `procjena_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  CONSTRAINT `procjena_zahtjev_zahtjev_id_fk`
    FOREIGN KEY (`zahtjev`)
    REFERENCES `WebDiP2021x024`.`zahtjev` (`zahtjev_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `procjena_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`kupnja_dijela`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`kupnja_dijela` (
  `kupnja_dijela_id` INT NOT NULL AUTO_INCREMENT,
  `datum_kupnje` DATE NOT NULL,
  `dio` INT NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`kupnja_dijela_id`),
  INDEX `kupnja_dijela_dio_dio_id_fk_idx` (`dio` ASC),
  INDEX `kupnja_dijela_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  CONSTRAINT `kupnja_dijela_dio_dio_id_fk`
    FOREIGN KEY (`dio`)
    REFERENCES `WebDiP2021x024`.`dio` (`dio_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `kupnja_dijela_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WebDiP2021x024`.`kupnja_vozila`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `WebDiP2021x024`.`kupnja_vozila` (
  `kupnja_vozila_id` INT NOT NULL AUTO_INCREMENT,
  `datum_kupnje` DATE NOT NULL,
  `procjena` INT NOT NULL,
  `korisnik` INT NOT NULL,
  PRIMARY KEY (`kupnja_vozila_id`),
  INDEX `kupnja_vozila_procjena_procjena_id_fk_idx` (`procjena` ASC),
  INDEX `kupnja_vozila_korisnik_korisnik_id_fk_idx` (`korisnik` ASC),
  CONSTRAINT `kupnja_vozila_procjena_procjena_id_fk`
    FOREIGN KEY (`procjena`)
    REFERENCES `WebDiP2021x024`.`procjena` (`procjena_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `kupnja_vozila_korisnik_korisnik_id_fk`
    FOREIGN KEY (`korisnik`)
    REFERENCES `WebDiP2021x024`.`korisnik` (`korisnik_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
