<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $statement = $this->db->prepare('
            SELECT u.*, r.name AS role_name
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            WHERE u.email = :email
            LIMIT 1
        ');
        $statement->execute([
            'email' => $email,
        ]);

        return $statement->fetch() ?: null;
    }

    public function findByEmailExcept(string $email, string $userId): ?array
    {
        $statement = $this->db->prepare('
            SELECT u.*, r.name AS role_name
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            WHERE u.email = :email AND u.id <> :id
            LIMIT 1
        ');
        $statement->execute([
            'email' => $email,
            'id' => $userId,
        ]);

        return $statement->fetch() ?: null;
    }

    public function findById(string $userId): ?array
    {
        $statement = $this->db->prepare('
            SELECT u.*, r.name AS role_name
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            WHERE u.id = :id
            LIMIT 1
        ');
        $statement->execute([
            'id' => $userId,
        ]);

        return $statement->fetch() ?: null;
    }

    public function createCustomer(array $data): array
    {
        $roleId = $this->ensureRole('customer', 'Khách hàng');
        $userId = generate_id('USR');

        $statement = $this->db->prepare('
            INSERT INTO users (
                id, role_id, full_name, email, phone, password_hash, status, created_at, updated_at
            ) VALUES (
                :id, :role_id, :full_name, :email, :phone, :password_hash, :status, :created_at, :updated_at
            )
        ');
        $statement->execute([
            'id' => $userId,
            'role_id' => $roleId,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status' => 'active',
            'created_at' => now_sql(),
            'updated_at' => now_sql(),
        ]);

        return $this->findById($userId);
    }

    public function createManagedUser(array $data): array
    {
        $roleDescriptions = [
            'admin' => 'System administrator',
            'manager' => 'Business manager',
            'sales' => 'Sales and support staff',
            'operations' => 'Operations staff',
            'customer' => 'Customer',
        ];

        $roleName = $data['role_name'];
        $roleId = $this->ensureRole($roleName, $roleDescriptions[$roleName] ?? $roleName);
        $userId = generate_id('USR');

        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare('
                INSERT INTO users (
                    id, role_id, full_name, email, phone, password_hash, gender,
                    date_of_birth, status, created_at, updated_at
                ) VALUES (
                    :id, :role_id, :full_name, :email, :phone, :password_hash, :gender,
                    :date_of_birth, :status, :created_at, :updated_at
                )
            ');
            $statement->execute([
                'id' => $userId,
                'role_id' => $roleId,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?: null,
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'gender' => $data['gender'] ?: null,
                'date_of_birth' => $data['date_of_birth'] ?: null,
                'status' => $data['status'],
                'created_at' => now_sql(),
                'updated_at' => now_sql(),
            ]);

            if (!empty($data['address_line'])) {
                $addressStatement = $this->db->prepare('
                    INSERT INTO addresses (
                        id, user_id, receiver_name, receiver_phone, province, district, ward, address_line, is_default, created_at
                    ) VALUES (
                        :id, :user_id, :receiver_name, :receiver_phone, :province, :district, :ward, :address_line, 1, :created_at
                    )
                ');
                $addressStatement->execute([
                    'id' => generate_id('ADR'),
                    'user_id' => $userId,
                    'receiver_name' => $data['full_name'],
                    'receiver_phone' => $data['phone'] ?: '',
                    'province' => null,
                    'district' => null,
                    'ward' => null,
                    'address_line' => $data['address_line'],
                    'created_at' => now_sql(),
                ]);
            }

            $this->db->commit();
            return $this->findById($userId);
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function updateManagedUser(string $userId, array $data): void
    {
        $roleDescriptions = [
            'admin' => 'System administrator',
            'manager' => 'Business manager',
            'sales' => 'Sales and support staff',
            'operations' => 'Operations staff',
            'customer' => 'Customer',
        ];

        $roleName = $data['role_name'];
        $roleId = $this->ensureRole($roleName, $roleDescriptions[$roleName] ?? $roleName);
        $password = trim((string) ($data['password'] ?? ''));

        $this->db->beginTransaction();

        try {
            $sql = '
                UPDATE users
                SET
                    role_id = :role_id,
                    full_name = :full_name,
                    email = :email,
                    phone = :phone,
                    gender = :gender,
                    date_of_birth = :date_of_birth,
                    status = :status,
                    updated_at = :updated_at';

            $params = [
                'id' => $userId,
                'role_id' => $roleId,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?: null,
                'gender' => $data['gender'] ?: null,
                'date_of_birth' => $data['date_of_birth'] ?: null,
                'status' => $data['status'],
                'updated_at' => now_sql(),
            ];

            if ($password !== '') {
                $sql .= ', password_hash = :password_hash';
                $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= ' WHERE id = :id';

            $statement = $this->db->prepare($sql);
            $statement->execute($params);

            if (trim((string) ($data['address_line'] ?? '')) !== '') {
                $this->upsertDefaultAddress($userId, [
                    'receiver_name' => $data['full_name'],
                    'receiver_phone' => $data['phone'] ?: '',
                    'province' => null,
                    'district' => null,
                    'ward' => null,
                    'address_line' => $data['address_line'],
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function updateProfile(string $userId, array $data): void
    {
        $statement = $this->db->prepare('
            UPDATE users
            SET
                full_name = :full_name,
                phone = :phone,
                gender = :gender,
                date_of_birth = :date_of_birth,
                updated_at = :updated_at
            WHERE id = :id
        ');
        $statement->execute([
            'id' => $userId,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?: null,
            'gender' => $data['gender'] ?: null,
            'date_of_birth' => $data['date_of_birth'] ?: null,
            'updated_at' => now_sql(),
        ]);
    }

    public function getAddressesByUserId(string $userId): array
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM addresses
            WHERE user_id = :user_id
            ORDER BY is_default DESC, created_at DESC, id DESC
        ');
        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function getDefaultAddressByUserId(string $userId): ?array
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM addresses
            WHERE user_id = :user_id
            ORDER BY is_default DESC, created_at DESC, id DESC
            LIMIT 1
        ');
        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetch() ?: null;
    }

    public function upsertDefaultAddress(string $userId, array $data): string
    {
        $existingAddress = $this->getDefaultAddressByUserId($userId);

        if ($existingAddress) {
            $statement = $this->db->prepare('
                UPDATE addresses
                SET
                    receiver_name = :receiver_name,
                    receiver_phone = :receiver_phone,
                    province = :province,
                    district = :district,
                    ward = :ward,
                    address_line = :address_line,
                    is_default = 1
                WHERE id = :id
            ');
            $statement->execute([
                'id' => $existingAddress['id'],
                'receiver_name' => $data['receiver_name'],
                'receiver_phone' => $data['receiver_phone'],
                'province' => $data['province'] ?: null,
                'district' => $data['district'] ?: null,
                'ward' => $data['ward'] ?: null,
                'address_line' => $data['address_line'],
            ]);

            return $existingAddress['id'];
        }

        $addressId = generate_id('ADR');
        $statement = $this->db->prepare('
            INSERT INTO addresses (
                id, user_id, receiver_name, receiver_phone, province, district, ward, address_line, is_default, created_at
            ) VALUES (
                :id, :user_id, :receiver_name, :receiver_phone, :province, :district, :ward, :address_line, 1, :created_at
            )
        ');
        $statement->execute([
            'id' => $addressId,
            'user_id' => $userId,
            'receiver_name' => $data['receiver_name'],
            'receiver_phone' => $data['receiver_phone'],
            'province' => $data['province'] ?: null,
            'district' => $data['district'] ?: null,
            'ward' => $data['ward'] ?: null,
            'address_line' => $data['address_line'],
            'created_at' => now_sql(),
        ]);

        return $addressId;
    }

    public function getOrderStats(string $userId): array
    {
        $statement = $this->db->prepare('
            SELECT
                COUNT(*) AS total_orders,
                SUM(CASE WHEN order_status = "delivered" THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN order_status IN ("pending_confirmation", "pre_order_pending", "prescription_review", "processing", "shipping", "after_sales") THEN 1 ELSE 0 END) AS processing_orders
            FROM orders
            WHERE user_id = :user_id
        ');
        $statement->execute([
            'user_id' => $userId,
        ]);

        $stats = $statement->fetch() ?: [];

        $refundStatement = $this->db->prepare('
            SELECT COUNT(*) AS after_sales_count
            FROM after_sales_requests
            WHERE user_id = :user_id
        ');
        $refundStatement->execute([
            'user_id' => $userId,
        ]);
        $afterSales = $refundStatement->fetch() ?: [];

        return [
            'total_orders' => (int) ($stats['total_orders'] ?? 0),
            'completed_orders' => (int) ($stats['completed_orders'] ?? 0),
            'processing_orders' => (int) ($stats['processing_orders'] ?? 0),
            'after_sales_count' => (int) ($afterSales['after_sales_count'] ?? 0),
        ];
    }

    public function getAllUsers(): array
    {
        $statement = $this->db->query('
            SELECT
                u.id,
                u.full_name,
                u.email,
                u.phone,
                u.status,
                u.created_at,
                u.updated_at,
                r.name AS role_name,
                (
                    SELECT COUNT(*)
                    FROM orders o
                    WHERE o.user_id = u.id
                ) AS orders_count
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            ORDER BY u.created_at DESC, u.id DESC
        ');

        return $statement->fetchAll();
    }

    public function canDeleteManagedUser(string $userId): bool
    {
        foreach ($this->getDeleteBlockers($userId) as $count) {
            if ($count > 0) {
                return false;
            }
        }

        return true;
    }

    public function getDeleteBlockers(string $userId): array
    {
        $queries = [
            'orders' => 'SELECT COUNT(*) FROM orders WHERE user_id = ?',
            'order_status_histories' => 'SELECT COUNT(*) FROM order_status_histories WHERE changed_by = ?',
            'prescriptions' => 'SELECT COUNT(*) FROM prescriptions WHERE user_id = ? OR verified_by = ?',
            'prescription_workflows' => 'SELECT COUNT(*) FROM prescription_workflows WHERE handled_by = ?',
            'after_sales_requests' => 'SELECT COUNT(*) FROM after_sales_requests WHERE user_id = ? OR handled_by = ?',
            'refunds' => 'SELECT COUNT(*) FROM refunds WHERE processed_by = ?',
            'inventory_transactions' => 'SELECT COUNT(*) FROM inventory_transactions WHERE created_by = ?',
        ];

        $blockers = [];

        foreach ($queries as $key => $sql) {
            $statement = $this->db->prepare($sql);
            $statement->execute(array_fill(0, substr_count($sql, '?'), $userId));
            $blockers[$key] = (int) $statement->fetchColumn();
        }

        return $blockers;
    }

    public function deleteManagedUser(string $userId): void
    {
        $this->db->beginTransaction();

        try {
            if (!$this->canDeleteManagedUser($userId)) {
                throw new \RuntimeException('Không thể xóa người dùng đã có dữ liệu nghiệp vụ liên quan.');
            }

            $cartStatement = $this->db->prepare('SELECT id FROM carts WHERE user_id = :id');
            $cartStatement->execute(['id' => $userId]);
            $cartIds = array_column($cartStatement->fetchAll(), 'id');

            foreach ($cartIds as $cartId) {
                $deleteCartItems = $this->db->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id');
                $deleteCartItems->execute(['cart_id' => $cartId]);
            }

            $deleteCarts = $this->db->prepare('DELETE FROM carts WHERE user_id = :id');
            $deleteCarts->execute(['id' => $userId]);

            $deleteAddresses = $this->db->prepare('DELETE FROM addresses WHERE user_id = :id');
            $deleteAddresses->execute(['id' => $userId]);

            $deleteUser = $this->db->prepare('DELETE FROM users WHERE id = :id');
            $deleteUser->execute(['id' => $userId]);

            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function getRoles(): array
    {
        $statement = $this->db->query('
            SELECT id, name, description
            FROM roles
            ORDER BY name ASC
        ');

        return $statement->fetchAll();
    }

    public function ensureRole(string $roleName, string $description = ''): string
    {
        $statement = $this->db->prepare('
            SELECT id
            FROM roles
            WHERE name = :name
            LIMIT 1
        ');
        $statement->execute([
            'name' => $roleName,
        ]);

        $role = $statement->fetch();
        if ($role) {
            return $role['id'];
        }

        $roleId = generate_id('ROL');
        $insert = $this->db->prepare('
            INSERT INTO roles (id, name, description)
            VALUES (:id, :name, :description)
        ');
        $insert->execute([
            'id' => $roleId,
            'name' => $roleName,
            'description' => $description !== '' ? $description : $roleName,
        ]);

        return $roleId;
    }
}