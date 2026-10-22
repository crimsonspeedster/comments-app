CREATE DATABASE IF NOT EXISTS comments_app
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE comments_app;

CREATE TABLE reviews (
id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
parent_id BIGINT UNSIGNED NULL,
user_name VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
url VARCHAR(255) NULL,
text TEXT NOT NULL,
created_at TIMESTAMP NULL DEFAULT NULL,
updated_at TIMESTAMP NULL DEFAULT NULL,

PRIMARY KEY (`id`),
CONSTRAINT `reviews_parent_id_foreign`
    FOREIGN KEY (`parent_id`)
    REFERENCES `reviews` (`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

CREATE TABLE review_attachments (
id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
review_id BIGINT UNSIGNED NOT NULL,
status VARCHAR(255) NOT NULL DEFAULT 'pending',
original_path VARCHAR(255) NULL,
processed_path VARCHAR(255) NULL,
original_name VARCHAR(255) NOT NULL,
mime_type VARCHAR(100) NULL,
size BIGINT UNSIGNED NULL,
created_at TIMESTAMP NULL DEFAULT NULL,
updated_at TIMESTAMP NULL DEFAULT NULL,

PRIMARY KEY (`id`),
CONSTRAINT `review_attachments_review_id_foreign`
    FOREIGN KEY (`review_id`)
    REFERENCES `reviews` (`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;