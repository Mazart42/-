-- tapka database for MySQL 8+ / phpMyAdmin
-- Admin login after import: admin@tapka.local / admin123

CREATE DATABASE IF NOT EXISTS tapka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tapka;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS callback_requests;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS cars;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(50) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand VARCHAR(80) NOT NULL,
  model VARCHAR(100) NOT NULL,
  car_class ENUM('economy','comfort','business','suv','electric','minivan','premium') NOT NULL DEFAULT 'comfort',
  year SMALLINT UNSIGNED NOT NULL,
  transmission ENUM('auto','manual','robot') NOT NULL DEFAULT 'auto',
  fuel ENUM('petrol','diesel','hybrid','electric') NOT NULL DEFAULT 'petrol',
  seats TINYINT UNSIGNED NOT NULL DEFAULT 5,
  price_day DECIMAL(10,2) NOT NULL,
  deposit DECIMAL(10,2) NOT NULL DEFAULT 0,
  mileage_limit INT NOT NULL DEFAULT 300,
  city VARCHAR(120) NOT NULL DEFAULT 'Новосибирск',
  status ENUM('available','maintenance','hidden') NOT NULL DEFAULT 'available',
  description TEXT,
  image VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  car_id INT NOT NULL,
  customer_name VARCHAR(160) NOT NULL,
  customer_phone VARCHAR(50) NOT NULL,
  customer_email VARCHAR(190) DEFAULT NULL,
  pickup_city VARCHAR(120) NOT NULL,
  pickup_address VARCHAR(255) DEFAULT NULL,
  date_from DATE NOT NULL,
  date_to DATE NOT NULL,
  days INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('new','confirmed','in_progress','done','cancelled') NOT NULL DEFAULT 'new',
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  INDEX (car_id),
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_bookings_car FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE callback_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(190) DEFAULT NULL,
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, email, phone, password_hash, role) VALUES
('Администратор tapka', 'admin@tapka.local', '+7 900 000-00-00', '$2y$12$k1LxKoDlkfpcSixl6b2jpO6KQi2xi2BUEIXV0neH6xZGt71iNAE2q', 'admin');

INSERT INTO cars (brand, model, car_class, year, transmission, fuel, seats, price_day, deposit, mileage_limit, city, status, description, image) VALUES
('Hyundai', 'Solaris Active', 'economy', 2022, 'auto', 'petrol', 5, 2600.00, 8000.00, 300, 'Новосибирск', 'available', 'Экономичный седан для города: небольшой расход, камера заднего вида и кондиционер.', 'assets/cars/solaris.svg'),
('Lada', 'Vesta SW Cross', 'economy', 2023, 'manual', 'petrol', 5, 2400.00, 7000.00, 300, 'Топки', 'available', 'Практичный универсал с высоким клиренсом для ежедневных поездок и багажа.', 'assets/cars/solaris.svg'),
('Renault', 'Logan Stepway', 'economy', 2021, 'auto', 'petrol', 5, 2300.00, 6500.00, 280, 'Тогучин', 'available', 'Простой и надёжный автомобиль для недорогой аренды на каждый день.', 'assets/cars/solaris.svg'),
('Kia', 'K5 Prestige', 'comfort', 2023, 'auto', 'petrol', 5, 4200.00, 12000.00, 350, 'Кемерово', 'available', 'Комфортный седан для деловых поездок, трассы и ежедневных маршрутов.', 'assets/cars/k5.svg'),
('Skoda', 'Octavia Ambition', 'comfort', 2022, 'robot', 'petrol', 5, 3900.00, 11000.00, 350, 'Барабинск', 'available', 'Лифтбек с большим багажником, удобной посадкой и мягкой подвеской.', 'assets/cars/k5.svg'),
('Toyota', 'Corolla', 'comfort', 2021, 'auto', 'petrol', 5, 3700.00, 10000.00, 320, 'Ордынка', 'available', 'Универсальный комфорт-класс для города, трассы и спокойной семейной поездки.', 'assets/cars/k5.svg'),
('Toyota', 'Camry 70', 'business', 2022, 'auto', 'petrol', 5, 5900.00, 18000.00, 350, 'Топки', 'available', 'Бизнес-класс с просторным салоном, мягкой подвеской и премиальной акустикой.', 'assets/cars/camry.svg'),
('BMW', '520i G30', 'business', 2021, 'auto', 'petrol', 5, 7800.00, 28000.00, 350, 'Новосибирск', 'available', 'Динамичный бизнес-седан для встреч, трассы и представительских поездок.', 'assets/cars/camry.svg'),
('Mercedes-Benz', 'E 200', 'business', 2022, 'auto', 'petrol', 5, 8400.00, 30000.00, 350, 'Душанбе (Таджикистан)', 'available', 'Статусный седан с тихим салоном, комфортными креслами и аккуратной подачей.', 'assets/cars/camry.svg'),
('Geely', 'Monjaro Flagship', 'suv', 2024, 'auto', 'petrol', 5, 7200.00, 22000.00, 400, 'Улан-Удэ', 'available', 'Полноприводный кроссовер для поездок по городу, трассе и загородным маршрутам.', 'assets/cars/monjaro.svg'),
('Haval', 'Jolion', 'suv', 2023, 'robot', 'petrol', 5, 4800.00, 14000.00, 350, 'Тогучин', 'available', 'Универсальный кроссовер с высоким клиренсом и удобной мультимедиа.', 'assets/cars/monjaro.svg'),
('Toyota', 'RAV4', 'suv', 2022, 'auto', 'hybrid', 5, 6900.00, 21000.00, 380, 'Ордынка', 'available', 'Гибридный SUV для дальних маршрутов, багажа и уверенного движения по трассе.', 'assets/cars/monjaro.svg'),
('Tesla', 'Model 3 Long Range', 'electric', 2023, 'auto', 'electric', 5, 8300.00, 25000.00, 350, 'Ордынка', 'available', 'Электромобиль с быстрым разгоном, тихим салоном и запасом хода для выходных.', 'assets/cars/tesla.svg'),
('BYD', 'Dolphin', 'electric', 2024, 'auto', 'electric', 5, 6900.00, 18000.00, 320, 'Душанбе (Таджикистан)', 'available', 'Компактный электромобиль для спокойных городских поездок и экономичной аренды.', 'assets/cars/tesla.svg'),
('Zeekr', '001', 'electric', 2024, 'auto', 'electric', 5, 11800.00, 40000.00, 350, 'Новосибирск', 'available', 'Электро-грантурер с полным приводом, просторным салоном и премиальной динамикой.', 'assets/cars/tesla.svg'),
('Volkswagen', 'Caravelle', 'minivan', 2021, 'robot', 'diesel', 8, 7600.00, 20000.00, 450, 'Барабинск', 'available', 'Минивэн для семьи, команды или трансфера с большим багажником.', 'assets/cars/caravelle.svg'),
('Hyundai', 'Staria', 'minivan', 2023, 'auto', 'diesel', 8, 9300.00, 26000.00, 450, 'Улан-Удэ', 'available', 'Современный минивэн для трансфера, туристической группы и дальних поездок.', 'assets/cars/caravelle.svg'),
('Mercedes-Benz', 'Vito Tourer', 'minivan', 2022, 'auto', 'diesel', 8, 9800.00, 30000.00, 450, 'Кемерово', 'available', 'Комфортный пассажирский минивэн для корпоративных поездок и семьи.', 'assets/cars/caravelle.svg'),
('Lexus', 'ES 250', 'premium', 2022, 'auto', 'petrol', 5, 9900.00, 35000.00, 350, 'Душанбе (Таджикистан)', 'available', 'Премиальный седан с мягкой подвеской, кожаным салоном и высоким уровнем тишины.', 'assets/cars/camry.svg'),
('Audi', 'A6 Quattro', 'premium', 2023, 'auto', 'petrol', 5, 11200.00, 38000.00, 350, 'Новосибирск', 'available', 'Полноприводный премиум-седан для статуса, комфорта и уверенной динамики.', 'assets/cars/camry.svg');
