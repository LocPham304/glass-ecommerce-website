<?php

namespace App\Models;

use Core\Model;

class Voucher extends Model
{
    public function getAllVouchers(): array
    {
        $statement = $this->db->query('
            SELECT
                v.*,
                (
                    SELECT COUNT(*)
                    FROM orders o
                    WHERE o.voucher_id = v.id
                ) AS used_count
            FROM vouchers v
            ORDER BY v.start_at DESC, v.id DESC
        ');

        return $statement->fetchAll();
    }

    public function create(array $data): string
    {
        $voucherId = generate_id('VCH');

        $statement = $this->db->prepare('
            INSERT INTO vouchers (
                id, name, code, discount_type, discount_value, min_order_value,
                max_discount_value, start_at, expired_at, usage_limit, is_active
            ) VALUES (
                :id, :name, :code, :discount_type, :discount_value, :min_order_value,
                :max_discount_value, :start_at, :expired_at, :usage_limit, :is_active
            )
        ');
        $statement->execute([
            'id' => $voucherId,
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_order_value' => $data['min_order_value'] ?: null,
            'max_discount_value' => $data['max_discount_value'] ?: null,
            'start_at' => $data['start_at'] ?: null,
            'expired_at' => $data['expired_at'] ?: null,
            'usage_limit' => $data['usage_limit'] ?: null,
            'is_active' => $data['is_active'],
        ]);

        return $voucherId;
    }
}