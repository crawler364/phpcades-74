#!/bin/bash

# Скрипт проверки метода Find из расширения CryptoPro PHPCades.
# Выполняется напрямую под пользователем "bitrix".
#
# Использование:
#   bash ./check_find.sh [SHA1]
#   ./check_find.sh [SHA1]              # если у файла установлен исполняемый бит
#
# Если SHA1 не передан, используется демонстрационный хэш из задания:
#   0647f77d2af7d81a297f18df3bb9c9c26c9e6c48
#
# Вывод: JSON с результатами проверки (found_count, ok, error, extension_loaded).
# Код выхода: 0 — найдено >=1, 1 — не найдено, 2 — ошибка запуска.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEFAULT_SHA1="0647f77d2af7d81a297f18df3bb9c9c26c9e6c48"
SHA1_INPUT="${1:-$DEFAULT_SHA1}"

# Проверка наличия php
if ! command -v php >/dev/null 2>&1; then
  echo '{"ok":false,"error":"php не найден в PATH"}'
  exit 2
fi

# Выполняем PHP-код, передаём SHA1 через переменную окружения
if ! env CERT_SHA1="$SHA1_INPUT" php -d detect_unicode=0 <<'PHP'
<?php
// Минимальная проверка метода Find по SHA1 в пользовательском хранилище uMy
header('Content-Type: application/json; charset=UTF-8');

// Функция для безопасного получения имени текущего пользователя
function getCurrentUser() {
    if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
        try {
            $userInfo = posix_getpwuid(posix_geteuid());
            return $userInfo['name'] ?? 'unknown';
        } catch (Exception $e) {
            // Игнорируем ошибки POSIX
        }
    }

    // Альтернативные способы получения имени пользователя
    $user = getenv('USER') ?: getenv('USERNAME') ?: get_current_user();
    return $user ?: 'unknown';
}

$result = [
    'ok' => false,
    'found_count' => 0,
    'sha1' => getenv('CERT_SHA1') ?: '',
    'extension_loaded' => extension_loaded('php_CPCSP'),
    'error' => null,
    'user' => getCurrentUser()
];

try {
    if (!$result['extension_loaded']) {
        throw new RuntimeException('Расширение php_CPCSP не загружено для PHP CLI');
    }
    if ($result['sha1'] === '') {
        throw new InvalidArgumentException('SHA1 не задан');
    }

    // Открываем хранилище пользователя (uMy)
    $store = new \CPStore();
    $store->Open(2, 'My', 0); // 2 = CurrentUser, 'My' = uMy

    $certs = $store->get_Certificates();
    // Ищем по SHA1, строгое сравнение true
    $found = $certs->Find(CERTIFICATE_FIND_SHA1_HASH, strtolower($result['sha1']), true);

    $result['found_count'] = (int)$found->Count();
    $result['ok'] = $result['found_count'] > 0;
} catch (\Throwable $e) {
    $result['error'] = $e->getMessage();
}

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), PHP_EOL;

// Возвращаем корректный код выхода
if ($result['ok']) {
    exit(0);
} elseif (isset($result['error'])) {
    exit(2);
} else {
    exit(1);
}
PHP
then
  # PHP выполнился успешно, код выхода уже установлен в PHP
  :
else
  # Если php завершился с ошибкой (например, сегфолт), покажем общий JSON
  echo '{"ok":false,"error":"Ошибка запуска PHP (возможна проблема с расширением php_CPCSP)"}'
  exit 2
fi
