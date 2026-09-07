<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Khởi tạo ứng dụng cho môi trường kiểm thử.
     *
     * Chỉ cho phép PHPUnit sử dụng SQLite trong bộ nhớ.
     * Nếu vô tình kết nối MySQL thật, bài test sẽ dừng ngay
     * trước khi RefreshDatabase có thể xóa dữ liệu.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $connection = $app['config']->get(
            'database.default'
        );

        $database = $app['config']->get(
            "database.connections.{$connection}.database"
        );

        if (
            $connection !== 'sqlite'
            || $database !== ':memory:'
        ) {
            throw new RuntimeException(
                'Đã chặn chạy test vì database không an toàn. '
                . 'Kết nối hiện tại: '
                . (string) $connection
                . '; database: '
                . (string) $database
                . '. Hãy chạy php artisan optimize:clear trước khi test.'
            );
        }

        return $app;
    }
}