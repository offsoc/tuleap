DROP TABLE IF EXISTS plugin_pdftemplate;
CREATE TABLE plugin_pdftemplate
(
    id          BINARY(16)   NOT NULL PRIMARY KEY,
    label       VARCHAR(255) NOT NULL,
    description TEXT         NOT NULL,
    style       TEXT         NOT NULL,
    INDEX idx_label (label(10))
) ENGINE = InnoDB;
