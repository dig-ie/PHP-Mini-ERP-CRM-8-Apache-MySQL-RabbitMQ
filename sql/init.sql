-- Schema inicial para Mini ERP/CRM
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(190) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  email VARCHAR(190),
  phone VARCHAR(60),
  cpf_cnpj VARCHAR(20),
  asaas_customer_id VARCHAR(100),
  asaas_synced_at DATETIME NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  status VARCHAR(40) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_client FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Tabela de pagamentos locais vinculados ao Asaas
CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  asaas_payment_id VARCHAR(100) NOT NULL,
  billing_type VARCHAR(20) NOT NULL,
  value DECIMAL(12,2) NOT NULL,
  due_date DATE NOT NULL,
  status VARCHAR(40) NOT NULL,
  description VARCHAR(500),
  invoice_url VARCHAR(500),
  bank_slip_url VARCHAR(500),
  pix_qr_code VARCHAR(1000),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_client FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Usuário admin default (senha: admin123)
INSERT INTO users (email, password_hash, name) VALUES (
  'admin@example.com',
  -- hash de "admin123" gerado com PASSWORD_DEFAULT
  '$2y$10$oFblGL./KrxBtnP6fEaQwe83WN5770BwXF9hUi0ETby3StuDo9rXy',
  'Admin'
) ON DUPLICATE KEY UPDATE email = email;