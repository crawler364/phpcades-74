#!/bin/bash

# Скрипт установки зависимостей для phpcades

set -e  # Завершить скрипт при любой ошибке

# Определение рабочей директории скрипта
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Функция для безопасной установки RPM пакетов
install_rpm_package() {
    local package_path=$1
    local package_name=$2

    echo "Установка $package_name..."
    if rpm -Uvh "$package_path" 2>&1 | tee "$SCRIPT_DIR/rpm_output.log"; then
        echo "✓ $package_name успешно установлен/обновлен"
        return 0
    else
        # Проверяем, не является ли "ошибка" сообщением о том, что пакет уже установлен
        if grep -q "is already installed" "$SCRIPT_DIR/rpm_output.log"; then
            echo "✓ $package_name уже установлен (пропускаем)"
            return 0
        else
            echo "Ошибка установки $package_name"
            cat "$SCRIPT_DIR/rpm_output.log"
            return 1
        fi
    fi
}

echo "=== Скрипт установки зависимостей для PHPCades ==="
echo "Сервер: $(hostname)"
echo "Рабочая директория: $SCRIPT_DIR"
echo

# Проверка прав суперпользователя
if [[ $EUID -ne 0 ]]; then
    echo "ОШИБКА: Скрипт должен запускаться от имени root"
    exit 1
fi

echo

# Обновление списка пакетов и установка базовых зависимостей
echo "1. Обновление списка пакетов и установка базовых зависимостей..."
yum update -y || { echo "Ошибка обновления пакетов"; exit 1; }
yum install -y boost-devel libxml2-devel php-devel sqlite-devel unzip || { echo "Ошибка установки базовых зависимостей"; exit 1; }
echo "✓ Базовые зависимости установлены"

# Вызов скрипта install.sh
echo
echo "3. Установка минимального набора пакетов КриптоПро CSP."

# Распаковка архива linux-amd64.tgz
cd "$SCRIPT_DIR"
if [[ ! -f "linux-amd64.tgz" ]]; then
    echo "ОШИБКА: Архив linux-amd64.tgz не найден"
    echo "Ожидается: $SCRIPT_DIR/linux-amd64.tgz"
    exit 1
fi

if [[ ! -d "linux-amd64" ]]; then
    echo "Распаковка архива linux-amd64.tgz..."
    tar -xzf linux-amd64.tgz || { echo "Ошибка распаковки архива linux-amd64.tgz"; exit 1; }
    echo "✓ Архив linux-amd64.tgz распакован"
else
    echo "✓ Директория linux-amd64 уже существует"
fi

"$SCRIPT_DIR/linux-amd64/install.sh" || { echo "Ошибка выполнения install.sh"; exit 1; }
echo "✓ Установка минимального набора пакетов КриптоПро CSP выполнена успешно."

# Запуск службы cprocsp
echo "Запуск службы cprocsp..."
systemctl start cprocsp || { echo "Ошибка запуска службы cprocsp"; exit 1; }
echo "✓ Служба cprocsp запущена и добавлена в автозагрузку"

# Установка cprocsp-devel из локального RPM пакета
echo
echo "4. Установка cprocsp-devel из локального пакета..."
if [[ -f "$SCRIPT_DIR/linux-amd64/lsb-cprocsp-devel-5.0.13000-7.noarch.rpm" ]]; then
    install_rpm_package "$SCRIPT_DIR/linux-amd64/lsb-cprocsp-devel-5.0.13000-7.noarch.rpm" "cprocsp-devel" || exit 1
    echo "✓ Пакет cprocsp-devel установлен из локального RPM"
else
    echo "ОШИБКА: Локальный RPM пакет cprocsp-devel не найден"
    echo "Ожидается: $SCRIPT_DIR/linux-amd64/lsb-cprocsp-devel-5.0.13000-7.noarch.rpm"
    exit 1
fi

# Установка пакетов PKI (cprocsp-pki-cades, cprocsp-pki-phpcades)
echo
echo "5. Установка пакетов PKI из архива КриптоПро..."

# Установка cprocsp-pki-cades
if [[ -f "$SCRIPT_DIR/linux-amd64/cprocsp-pki-cades-64-2.0.15000-1.amd64.rpm" ]]; then
    install_rpm_package "$SCRIPT_DIR/linux-amd64/cprocsp-pki-cades-64-2.0.15000-1.amd64.rpm" "cprocsp-pki-cades" || exit 1
else
    echo "ОШИБКА: Пакет cprocsp-pki-cades не найден"
    exit 1
fi

# Установка cprocsp-pki-phpcades
if [[ -f "$SCRIPT_DIR/linux-amd64/cprocsp-pki-phpcades-2.0.15000-1.noarch.rpm" ]]; then
    install_rpm_package "$SCRIPT_DIR/linux-amd64/cprocsp-pki-phpcades-2.0.15000-1.noarch.rpm" "cprocsp-pki-phpcades" || exit 1
else
    echo "ОШИБКА: Пакет cprocsp-pki-phpcades не найден"
    exit 1
fi

# Очистка временных файлов
rm -f "$SCRIPT_DIR/rpm_output.log"

# Настройка переменной PHPDIR в Makefile.unix
echo
echo "7. Настройка переменной PHPDIR в Makefile.unix..."

# Проверка существования файла Makefile.unix
if [[ -f "/opt/cprocsp/src/phpcades/Makefile.unix" ]]; then
    # Определение правильного пути к исходникам PHP
    if which php > /dev/null 2>&1; then
        PHPDIR_PATH="/usr/include/php"
    else
        echo "ОШИБКА: Не удалось определить путь к заголовочным файлам PHP"
        exit 1
    fi

    echo "Установка PHPDIR=$PHPDIR_PATH в /opt/cprocsp/src/phpcades/Makefile.unix"
    sed -i "s|^PHPDIR=.*|PHPDIR=$PHPDIR_PATH|" /opt/cprocsp/src/phpcades/Makefile.unix || { echo "Ошибка изменения PHPDIR"; exit 1; }
    echo "✓ Переменная PHPDIR установлена успешно"
else
    echo "ОШИБКА: Файл /opt/cprocsp/src/phpcades/Makefile.unix не найден"
    exit 1
fi

# Скачивание php7_support.patch.zip
echo
echo "8. Скачивание php7_support.patch.zip..."
cd "$SCRIPT_DIR"
if [[ ! -f "php7_support.patch.zip" ]]; then
    wget https://www.cryptopro.ru/sites/default/files/products/cades/php7_support.patch.zip || { echo "Ошибка скачивания php7_support.patch.zip"; exit 1; }
    echo "✓ Файл php7_support.patch.zip скачан"
else
    echo "✓ Файл php7_support.patch.zip уже существует"
fi

# Распаковка php7_support.patch.zip
if [[ -f "php7_support.patch.zip" ]]; then
    echo "Распаковка php7_support.patch.zip..."
    unzip -o php7_support.patch.zip || { echo "Ошибка распаковки php7_support.patch.zip"; exit 1; }
    echo "✓ Файл php7_support.patch.zip распакован"
# Копирование и применение патча
cp ./php7_support.patch /opt/cprocsp/src/phpcades
cd /opt/cprocsp/src/phpcades
patch -p0 < ./php7_support.patch
echo "✓ Патч php7_support.patch применен к исходникам phpcades"
else
    echo "ОШИБКА: Файл php7_support.patch.zip не найден для распаковки"
    exit 1
fi

echo
echo "9. Сборка расширения phpcades..."
cd /opt/cprocsp/src/phpcades
eval `/opt/cprocsp/src/doxygen/CSP/../setenv.sh --64`; make -f Makefile.unix
echo "✓ Расширение phpcades собрано успешно"

echo
echo "10. Вывод пути к расширениям PHP..."
php -i | grep extension_dir
echo "✓ Путь к расширениям PHP выведен"

echo
echo "11. Создание символической ссылки на библиотеку libphpcades.so..."
# Определение пути к директории расширений PHP
EXTENSION_DIR=$(php -r "echo ini_get('extension_dir');")
if [[ -z "$EXTENSION_DIR" ]]; then
    EXTENSION_DIR="/usr/lib64/php/modules"
fi

# Создание символической ссылки
if [[ -f "/opt/cprocsp/src/phpcades/libphpcades.so" ]]; then
    ln -sf /opt/cprocsp/src/phpcades/libphpcades.so "$EXTENSION_DIR/libphpcades.so" || { echo "Ошибка создания символической ссылки"; exit 1; }
    echo "✓ Символическая ссылка создана: $EXTENSION_DIR/libphpcades.so -> /opt/cprocsp/src/phpcades/libphpcades.so"
else
    echo "ОШИБКА: Библиотека libphpcades.so не найдена в /opt/cprocsp/src/phpcades/"
    exit 1
fi

echo
echo "12. Добавление расширения в конфигурацию PHP..."
# Создание отдельного файла конфигурации для libphpcades
LIBPHPCADES_INI_PATH="/etc/php.d/50-libphpcades.ini"

# Проверка, не добавлено ли уже расширение
if [[ -f "$LIBPHPCADES_INI_PATH" ]]; then
    echo "✓ Файл конфигурации $LIBPHPCADES_INI_PATH уже существует"
else
    # Создание файла с комментарием и загрузкой расширения
    cat > "$LIBPHPCADES_INI_PATH" << 'EOF' || { echo "Ошибка создания $LIBPHPCADES_INI_PATH"; exit 1; }
; Расширение для работы с электронной подписью КриптоПро
extension=libphpcades.so
EOF
    echo "✓ Файл конфигурации создан: $LIBPHPCADES_INI_PATH"
fi

# Проверка содержимого файла
if grep -q "extension=libphpcades.so" "$LIBPHPCADES_INI_PATH"; then
    echo "✓ Расширение libphpcades.so корректно настроено в $LIBPHPCADES_INI_PATH"
else
    echo "ПРЕДУПРЕЖДЕНИЕ: Строка extension=libphpcades.so не найдена в $LIBPHPCADES_INI_PATH"
fi

echo
echo -n "Проверка работы службы КриптоПро CSP... "
if systemctl is-active --quiet cprocsp; then
    echo "✓ Активна"
else
    echo "❌ Не активна"
fi

echo -n "Проверка загрузки PhpCAdES... "
if php -r "echo extension_loaded('php_CPCSP') ? 'loaded' : 'not_loaded';" 2>/dev/null | grep -q "loaded"; then
    echo "✓ Загружен"
else
    echo "❌ Не загружен"
fi

echo
echo "=== Установка завершена ==="
