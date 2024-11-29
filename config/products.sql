CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    sku VARCHAR(50) UNIQUE,
    category VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample products
INSERT INTO products (name, description, price, stock, sku, category) VALUES 
('Produto A', 'Descrição do Produto A', 99.99, 50, 'SKU001', 'Categoria 1'),
('Produto B', 'Descrição do Produto B', 149.99, 30, 'SKU002', 'Categoria 2'),
('Produto C', 'Descrição do Produto C', 199.99, 25, 'SKU003', 'Categoria 1');
