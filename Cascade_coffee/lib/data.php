<?php
declare(strict_types=1);

const DATA_DIR = __DIR__ . '/../data';
const MENU_FILE = DATA_DIR . '/menu.json';
const ADMIN_FILE = DATA_DIR . '/admin.json';
const ABOUT_FILE = DATA_DIR . '/about.json';

function ensure_data_files(): void
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0775, true);
    }

    if (!file_exists(MENU_FILE)) {
        save_menu_items([
            [
                'id' => uniqid('item_', true),
                'name' => 'Cascade Macchiato',
                'category' => 'Drink',
                'price' => '95',
                'description' => 'Rich espresso with silky milk and a soft caramel finish.',
                'available' => true,
            ],
            [
                'id' => uniqid('item_', true),
                'name' => 'Iced Vanilla Latte',
                'category' => 'Drink',
                'price' => '120',
                'description' => 'Cold espresso, vanilla, and chilled milk over ice.',
                'available' => true,
            ],
            [
                'id' => uniqid('item_', true),
                'name' => 'Chicken Panini',
                'category' => 'Food',
                'price' => '180',
                'description' => 'Toasted bread with chicken, cheese, and house sauce.',
                'available' => true,
            ],
            [
                'id' => uniqid('item_', true),
                'name' => 'Chocolate Cake',
                'category' => 'Specials',
                'price' => '140',
                'description' => 'Moist chocolate slice served fresh as a house special.',
                'available' => true,
            ],
        ]);
    }

    if (!file_exists(ADMIN_FILE)) {
        save_admin([
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        ]);
    }

    if (!file_exists(ABOUT_FILE)) {
        save_about([
            'description' => 'Welcome to Cascade Coffee. We are dedicated to serving the finest coffee and food in a warm, welcoming atmosphere.',
            'address' => '123 Coffee Street, Addis Ababa, Ethiopia',
            'phone' => '+251 1 234 5678',
            'images' => [],
            'socials' => [
                [
                    'id' => uniqid('social_', true),
                    'name' => 'Facebook',
                    'url' => 'https://facebook.com',
                ],
                [
                    'id' => uniqid('social_', true),
                    'name' => 'Instagram',
                    'url' => 'https://instagram.com',
                ],
                [
                    'id' => uniqid('social_', true),
                    'name' => 'Twitter',
                    'url' => 'https://twitter.com',
                ],
            ],
        ]);
    }
}

function read_json_file(string $path, array $fallback): array
{
    if (!file_exists($path)) {
        return $fallback;
    }

    $content = file_get_contents($path);
    $decoded = json_decode($content ?: '', true);

    return is_array($decoded) ? $decoded : $fallback;
}

function write_json_file(string $path, array $data): void
{
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function get_menu_items(): array
{
    ensure_data_files();
    return read_json_file(MENU_FILE, []);
}

function save_menu_items(array $items): void
{
    write_json_file(MENU_FILE, array_values($items));
}

function find_menu_item(string $id): ?array
{
    foreach (get_menu_items() as $item) {
        if (($item['id'] ?? '') === $id) {
            return $item;
        }
    }

    return null;
}

function get_admin(): array
{
    ensure_data_files();
    return read_json_file(ADMIN_FILE, []);
}

function save_admin(array $admin): void
{
    write_json_file(ADMIN_FILE, $admin);
}

function clean_text(string $value): string
{
    return trim(strip_tags($value));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function get_about(): array
{
    ensure_data_files();
    return read_json_file(ABOUT_FILE, [
        'description' => '',
        'address' => '',
        'phone' => '',
        'images' => [],
        'socials' => [],
    ]);
}

function save_about(array $about): void
{
    write_json_file(ABOUT_FILE, $about);
}
