CREATE TABLE IF NOT EXISTS guests (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20) UNIQUE NOT NULL,
    country VARCHAR(50)
);

INSERT INTO guests (first_name, last_name, email, phone, country) VALUES
('Иван', 'Иванов', 'ivanov@example.com', '+71234567890', 'Россия'),
('Анна', 'Петрова', 'petrova@example.com', '+79876543210', 'Россия');