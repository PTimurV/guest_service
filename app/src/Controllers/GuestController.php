<?php

/**
 * Контроллер для управления операциями CRUD над гостями.
 */

class GuestController
{
    private $guestModel;

    /**
     * Конструктор класса GuestController.
     *
     * Инициализирует модель гостя.
     */
    public function __construct()
    {
        $db = Database::getConnection();
        $this->guestModel = new Guest($db);
    }

    /**
     * Получает список всех гостей.
     *
     * @return void
     */
    public function getAll(): void
    {
        $guests = $this->guestModel->getAll();
        $this->sendResponse($guests);
    }

    /**
     * Получает информацию о госте по ID.
     *
     * @param int $id Идентификатор гостя.
     *
     * @return void
     */
    public function getById($id): void
    {
        $guest = $this->guestModel->getById($id);
        if ($guest) {
            $this->sendResponse($guest);
        } else {
            $this->sendResponse(['error' => 'Гость не найден'], 404);
        }
    }

    /**
     * Отправляет JSON-ответ клиенту с дополнительными заголовками для отладки.
     *
     * @param array $data Данные для отправки в формате JSON.
     * @param int $status HTTP-статус ответа. По умолчанию 200.
     *
     * @return void
     */
    private function sendResponse($data, $status = 200): void
    {
        global $startTime, $startMemory;

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        header('Content-Type: application/json; charset=utf-8');
        header('X-Debug-Time: ' . round(($endTime - $startTime) * 1000, 2) . ' ms');
        header('X-Debug-Memory: ' . round(($endMemory - $startMemory) / 1024, 2) . ' KB');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }


    /**
     * Создает нового гостя.
     *
     * @return void
     */
    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $validationErrors = $this->validate($data);
        if (!empty($validationErrors)) {
            $this->sendResponse(['errors' => $validationErrors], 422);
            return;
        }

        if (empty($data['country'])) {
            $data['country'] = $this->getCountryByPhone($data['phone']);
        }

        $id = $this->guestModel->create($data);
        $this->sendResponse(['message' => 'Гость создан', 'id' => $id], 201);
    }

    /**
     * Обновляет информацию о существующем госте.
     *
     * @param int $id Идентификатор гостя.
     *
     * @return void
     */
    public function update($id): void
    {
        $existingGuest = $this->guestModel->getById($id);
        if (!$existingGuest) {
            $this->sendResponse(['error' => 'Гость не найден'], 404);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $data = array_merge($existingGuest, $data);
        $validationErrors = $this->validate($data, $id);
        if (!empty($validationErrors)) {
            $this->sendResponse(['errors' => $validationErrors], 422);
            return;
        }

        if (empty($data['country'])) {
            $data['country'] = $this->getCountryByPhone($data['phone']);
        }

        $this->guestModel->update($id, $data);
        $this->sendResponse(['message' => 'Гость обновлен']);
    }

    /**
     * Удаляет гостя по ID.
     *
     * @param int $id Идентификатор гостя.
     *
     * @return void
     */
    public function delete($id): void
    {
        $existingGuest = $this->guestModel->getById($id);
        if (!$existingGuest) {
            $this->sendResponse(['error' => 'Гость не найден'], 404);
            return;
        }

        $this->guestModel->delete($id);
        $this->sendResponse(['message' => 'Гость удален']);
    }

    /**
     * Валидирует данные гостя.
     *
     * @param array $data Данные гостя.
     * @param int|null $id Идентификатор гостя (для обновления).
     *
     * @return array Массив ошибок валидации.
     */
    private function validate($data, $id = null): array
    {
        $errors = [];

        // Проверка обязательных полей
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Имя пустое';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Фамилия пустая';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Телефон пустой';
        }

        // Проверка уникальности email
        if (!empty($data['email'])) {
            if ($this->guestModel->emailExists($data['email'], $id)) {
                $errors['email'] = 'Такой Email уже существует';
            }
        }

        // Проверка уникальности телефона
        if ($this->guestModel->phoneExists($data['phone'], $id)) {
            $errors['phone'] = 'Такой телефон уже существует';
        }

        // Дополнительная валидация (формат email)
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неправильный формат Email';
        }

        return $errors;
    }

    /**
     * Определяет страну по номеру телефона.
     *
     * @param string $phone Номер телефона гостя.
     *
     * @return string|null Название страны или null, если не определена.
     */
    private function getCountryByPhone($phone): string|null
    {
        $countryCodes = [
            '+7' => 'Россия',
            '+1' => 'США',
            '+44' => 'Великобритания',
            '+86' => 'Китай',
            '+91' => 'Индия',
        ];

        foreach ($countryCodes as $code => $country) {
            if (strpos($phone, $code) === 0) {
                return $country;
            }
        }

        return null;
    }
}
