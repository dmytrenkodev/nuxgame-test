#!/bin/bash

echo "Starting DB"
until docker exec nuxgame-mysql mysqladmin ping -h"localhost" --silent; do
  sleep 2
done

docker exec -i nuxgame-mysql mysql -u root -proot nuxgame <<'EOF'
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    phone VARCHAR(30),
    token VARCHAR(64) UNIQUE,
    expires_at DATETIME,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS lucky_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    number INT,
    result ENUM('Win','Lose'),
    amount DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

EOF

echo "Success"
