<?php
/**
 * CLI скрипт проверки метода Find для CPStore (CryptoPro PHP CAdES)
 *
 * Выполняет проверку поиска сертификата по SHA1 хешу с использованием
 * предоставленного кода через метод Find.
 * Предназначен для выполнения через CLI на удаленном сервере.
 */

// Инициализация Bitrix для CLI
$docRoot = dirname(__DIR__, 2);
include_once $docRoot . '/local/cron/config.php';

echo "=== CLI тест проверки Find для CPStore ===\n";
echo "Дата и время: " . date('Y-m-d H:i:s') . "\n";
echo "PHP версия: " . PHP_VERSION . "\n";

try {
    // Проверка загрузки расширения
    if (!extension_loaded('php_CPCSP')) {
        throw new RuntimeException('Расширение php_CPCSP не загружено');
    }
    echo "✓ Расширение php_CPCSP загружено\n";

    // Выполнение предоставленного кода проверки
    echo "\n--- Выполнение проверки Find ---\n";
    
    // Открываем хранилище сертификатов
    $certificateStore = new \CPStore();
    $certificateStore->Open(2, 'My', 0);
    echo "✓ Хранилище сертификатов открыто (CURRENT_USER_STORE, 'My')\n";
    
    $certificates = $certificateStore->get_Certificates();
    echo "✓ Получен объект сертификатов\n";

    $certHashFile = __DIR__ . '/cert_hash.txt';
    if (!file_exists($certHashFile)) {
        throw new RuntimeException('Файл с хешем сертификата не найден: ' . $certHashFile);
    }
    
    $certHash = trim(file_get_contents($certHashFile));
    if (empty($certHash)) {
        throw new RuntimeException('Файл с хешем сертификата пустой: ' . $certHashFile);
    }
    
    $foundCertificates = $certificates->Find(
        CERTIFICATE_FIND_SHA1_HASH,
        $certHash,
        true
    );
    echo "✓ Выполнен поиск по SHA1 хешу: $certHash\n";
    
    $count = $foundCertificates->Count();
    echo "✓ Количество найденных сертификатов: " . $count . "\n";
    
    if ($count > 0) {
        echo "✓ УСПЕХ: Сертификат найден!\n";
    } else {
        echo "! ВНИМАНИЕ: Сертификат с указанным SHA1 хешем не найден\n";
    }

} catch (\Throwable $e) {
    echo "✗ ОШИБКА: " . $e->getMessage() . "\n";
    echo "Класс исключения: " . get_class($e) . "\n";
    if ($e->getFile()) {
        echo "Файл: " . $e->getFile() . " (строка " . $e->getLine() . ")\n";
    }
    exit(1);
}

echo "\n=== Тест завершен ===\n";
