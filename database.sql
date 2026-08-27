CREATE DATABASE IF NOT EXISTS fixerupper_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE fixerupper_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `orders` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  fulfilment_method VARCHAR(20) NOT NULL DEFAULT 'collection',
  customer_name VARCHAR(150) NOT NULL DEFAULT '',
  address_line1 VARCHAR(255) NOT NULL DEFAULT '',
  address_line2 VARCHAR(255) NOT NULL DEFAULT '',
  city VARCHAR(100) NOT NULL DEFAULT '',
  postcode VARCHAR(20) NOT NULL DEFAULT '',
  order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES `orders`(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO products (name, description, price, image) VALUES
('Cordless Drill', 'Versatile cordless drill for drilling and screwdriving tasks around the home and workshop.', 79.99, 'assets/images/cordless-drill.svg'),
('Hammer', 'Balanced steel claw hammer designed for general construction and DIY work.', 14.99, 'assets/images/hammer.svg'),
('Screwdriver Set', 'Precision and standard screwdriver set with a range of flathead and Phillips sizes.', 22.50, 'assets/images/screwdriver-set.svg'),
('Circular Saw', 'High-performance circular saw for clean cutting through timber, sheet materials, and more.', 129.99, 'assets/images/circular-saw.svg'),
('Sander', 'Electric detail sander with dust collection for smooth finishing on wood surfaces.', 64.95, 'assets/images/sander.svg'),
('Tool Kit', 'Comprehensive tool kit containing common hand tools for household repairs and maintenance.', 89.00, 'assets/images/tool-kit.svg'),
('Power Drill', 'Mains-powered drill with variable speed control and reliable torque for workshop tasks.', 99.95, 'assets/images/power-drill.svg'),
('Angle Grinder', 'Compact angle grinder suitable for cutting, grinding, and polishing metal surfaces.', 74.50, 'assets/images/angle-grinder.svg'),
('Measuring Tape', 'Durable retractable measuring tape with an easy-read imperial and metric scale.', 12.75, 'assets/images/measuring-tape.svg'),
('Wrench Set', 'Multi-size wrench set for assembly, plumbing, and mechanical maintenance work.', 34.99, 'assets/images/wrench-set.svg'),
('Electric Screwdriver', 'Rechargeable electric screwdriver for fast, accurate fastening with less effort.', 39.95, 'assets/images/electric-screwdriver.svg'),
('Safety Equipment', 'Essential safety equipment bundle including eye protection, gloves, and ear defenders.', 27.99, 'assets/images/safety-equipment.svg')
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  price = VALUES(price),
  image = VALUES(image);
