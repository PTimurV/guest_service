# Микросервис для работы с гостями

## Описание

Микросервис предоставляет API для выполнения CRUD операций над сущностью "Гость". Сервис реализован на PHP без использования фреймворков, структурирован по паттерну MVC и запускается в Docker.

## Техническое задание

- **Сущность "Гость"**:
  - Обязательные поля: имя, фамилия, телефон.
  - Поля `телефон` и `email` должны быть уникальными.
  - Атрибуты гостя: идентификатор, имя, фамилия, email, телефон, страна.
  - Если страна не указана, она определяется по коду телефона (например, `+7` — Россия).

- **Требования**:
  - Реализовать API для CRUD операций над гостем.
  - Валидация данных согласно требованиям.
  - В ответах сервера должны присутствовать заголовки `X-Debug-Time` и `X-Debug-Memory`, указывающие время выполнения запроса и потребление памяти.
  - Проект должен запускаться в Docker.

## Запуск проекта

### Предварительные требования

- Установленный Docker и Docker Compose.

### Инструкция по запуску

1. **Клонируйте репозиторий:**

   ```bash
   git clone https://github.com/PTimurV/guest_service.git
   cd guest_service
   docker-compose build
   docker-compose up -d

   API будет доступно по адресу:
   http://localhost:8000

## Описание API
### Получить список всех гостей
GET http://localhost:8000/guests


Пример успешного ответа:  
Статус: 200 OK
[
  {
    "id": 1,
    "first_name": "Иван",
    "last_name": "Иванов",
    "email": "ivanov@example.com",
    "phone": "+71234567890",
    "country": "Россия"
  },
  {
    "id": 2,
    "first_name": "Анна",
    "last_name": "Петрова",
    "email": "petrova@example.com",
    "phone": "+79876543210",
    "country": "Россия"
  }
]

### Получить информацию о госте по ID
GET http://localhost:8000/guests{id}


Пример успешного ответа:  
Статус: 200 OK
{
  "id": 1,
  "first_name": "Иван",
  "last_name": "Иванов",
  "email": "ivanov@example.com",
  "phone": "+71234567890",
  "country": "Россия"
}  
Пример ошибки:  
Статус: 404 Not Found  
{
  "error": "Гость не найден"
}

### Создать нового гостя
POST http://localhost:8000/guests


Пример тела запроса:  
{
  "first_name": "Пётр",
  "last_name": "Сидоров",
  "email": "sidorov@example.com",
  "phone": "+74951234567"
}

Пример успешного ответа:  
Статус: 201 Created
{
  "message": "Гость создан",
  "id": 3
}  
Пример ошибки:  
Статус: 422 Unprocessable Entity  
{
  "errors": {
    "email": "Такой Email уже существует",
    "phone": "Такой телефон уже существует"
  }
}

### Обновить информацию о госте
PUT http://localhost:8000/guests{id}


Пример тела запроса:  
{
  "email": "new_email@example.com",
  "phone": "+1234567890"
}

Пример успешного ответа:  
Статус: 200 OK
{
  "message": "Гость обновлен"
}  
Пример ошибки:  
Статус: 404 Not Found  
{
  "error": "Гость не найден"
}  
Пример ошибки валидации:  
Статус: 422 Unprocessable Entity  
{
  "errors": {
    "email": "Неправильный формат Email"
  }
}

### Удалить гостя
DELETE http://localhost:8000/guests{id}


Пример успешного ответа:  
Статус: 200 OK
{
  "message": "Гость удален"
}
Пример ошибки:  
Статус: 404 Not Found  
{
  "error": "Гость не найден"
}  
