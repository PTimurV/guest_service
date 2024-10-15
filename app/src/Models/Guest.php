<?php

/**
 * Модель для работы с данными гостей.
 */

class Guest
{
    private $db;

     /**
     * Конструктор класса Guest.
     *
     * @param PDO $db Объект подключения к базе данных.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

     /**
     * Получает список всех гостей.
     *
     * @return array Массив гостей.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM guests");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получает информацию о госте по ID.
     *
     * @param int $id Идентификатор гостя.
     *
     * @return array|null Данные гостя или null, если не найден.
     */
    public function getById($id): array|null
    {
        $stmt = $this->db->prepare("SELECT * FROM guests WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $guest = $stmt->fetch(PDO::FETCH_ASSOC);
        return $guest ?: null;
    }

    /**
     * Создает нового гостя.
     *
     * @param array $data Данные гостя.
     *
     * @return int Идентификатор созданного гостя.
     */
    public function create($data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO guests (first_name, last_name, email, phone, country)
            VALUES (:first_name, :last_name, :email, :phone, :country)
            RETURNING id
        ");
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'country' => $data['country'] ?? null,
        ]);
        return $stmt->fetchColumn();
    }

    /**
     * Обновляет информацию о госте.
     *
     * @param int $id Идентификатор гостя.
     * @param array $data Новые данные гостя.
     *
     * @return void
     */
    public function update($id, $data): void
    {
        $stmt = $this->db->prepare("
            UPDATE guests SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                country = :country
            WHERE id = :id
        ");
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'country' => $data['country'] ?? null,
            'id' => $id,
        ]);
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
        $stmt = $this->db->prepare("DELETE FROM guests WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Проверяет существование email в базе данных.
     *
     * @param string $email Email для проверки.
     * @param int|null $excludeId Идентификатор гостя для исключения (при обновлении).
     *
     * @return bool true, если email существует, иначе false.
     */
    public function emailExists($email, $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM guests WHERE email = :email";
        $params = ['email' => $email];
        if ($excludeId) {
            $query .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Проверяет существование телефона в базе данных.
     *
     * @param string $phone Телефон для проверки.
     * @param int|null $excludeId Идентификатор гостя для исключения (при обновлении).
     *
     * @return bool true, если телефон существует, иначе false.
     */
    public function phoneExists($phone, $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM guests WHERE phone = :phone";
        $params = ['phone' => $phone];
        if ($excludeId) {
            $query .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
