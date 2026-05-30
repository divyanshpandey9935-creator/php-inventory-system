-- Optional sample data for a quick demo.
INSERT INTO products (sku, name, description, price, quantity) VALUES
    ('SKU-1001', 'Wireless Mouse',      'Ergonomic 2.4GHz wireless mouse', 19.99, 120),
    ('SKU-1002', 'Mechanical Keyboard', 'RGB mechanical keyboard, blue switches', 79.50, 45),
    ('SKU-1003', 'USB-C Hub',           '7-in-1 USB-C multiport adapter', 34.00, 4),
    ('SKU-1004', '27" Monitor',         '1440p IPS display, 75Hz', 229.99, 18)
ON DUPLICATE KEY UPDATE name = VALUES(name);
