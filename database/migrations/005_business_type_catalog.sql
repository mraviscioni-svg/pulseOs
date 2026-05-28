-- Catálogo histórico de categorías y marcas por rubro (compartido entre tenants)

CREATE TABLE IF NOT EXISTS business_catalog_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_type VARCHAR(50) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    use_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_catalog_cat (business_type, name),
    INDEX idx_catalog_cat_type (business_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS business_catalog_brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_type VARCHAR(50) NOT NULL,
    name VARCHAR(120) NOT NULL,
    use_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_catalog_brand (business_type, name),
    INDEX idx_catalog_brand_type (business_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
