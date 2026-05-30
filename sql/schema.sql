-- Inventory Manager schema
-- Compatible with MySQL 8.x / MariaDB 10.x

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(64)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    sku         VARCHAR(64)    NOT NULL,
    name        VARCHAR(255)   NOT NULL,
    description TEXT           NULL,
    price       DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    quantity    INT            NOT NULL DEFAULT 0,
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_sku (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
