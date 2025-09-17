# libphpcades

Библиотека для установки PHPCades(PHP 7.4.33) - PHP-расширения для работы с КриптоПро CSP.

## Установка КриптоПро и PHPCades (под root)

Важно: архив linux-amd64.tgz с дистрибутивом КриптоПро CSP не входит в репозиторий. Скачайте его вручную со страницы https://cryptopro.ru/products/csp/downloads и поместите файл в каталог phpcades/ под именем linux-amd64.tgz (скрипт установки распакует его автоматически).

```bash
chmod +x install.sh
sudo ./install.sh
```

### Основные шаги выполнения скрипта

1. **Обновление системы** - установка базовых зависимостей (boost-devel, libxml2-devel, php-devel)
2. **Распаковка архива** - извлечение пакетов КриптоПро CSP из linux-amd64.tgz
3. **Установка КриптоПро CSP** - выполнение базовой установки и запуск службы cprocsp
4. **Установка пакетов разработчика** - cprocsp-devel, cprocsp-pki-cades, cprocsp-pki-phpcades
5. **Сборка и настройка PHPCades** - применение патчей, компиляция расширения libphpcades.so и создание символических ссылок
6. **Настройка PHP** - создание ini-файлов для подключения PHPCades расширения
7. **Проверка установки** - тестирование работоспособности библиотеки

## Установка сертификатов (под bitrix)
#### Шаг 1: Установка сертификата в стор

```bash
/opt/cprocsp/bin/amd64/certmgr -install -file <путь_к_файлу_сертификата>
```

#### Шаг 2: Привязка сертификата к контейнеру закрытого ключа

Для работы с электронной подписью необходимо установить сертификат и связать его с контейнером закрытого ключа.

Контейнер закрытого ключа необходимо разместить в директории КриптоПро:

```bash
sudo cp -r /путь/к/контейнеру/имя_контейнера /var/opt/cprocsp/keys/bitrix/
sudo chown -R bitrix:bitrix /var/opt/cprocsp/keys/bitrix/
find /var/opt/cprocsp/keys/bitrix/ -type d -exec chmod 700 {} +
find /var/opt/cprocsp/keys/bitrix/ -type f -exec chmod 600 {} +

```

```bash
# Привязка всех сертификатов к контейнерам закрытых ключей
/opt/cprocsp/bin/amd64/csptest -absorb -certs
```

#### Шаг 3: Проверка успешности привязки

```bash
/opt/cprocsp/bin/amd64/certmgr -list

# В выводе должно быть:
# PrivateKey Link     : Yes
# Container           : HDIMAGE\\<имя_контейнера>\xxxx
```

#### Шаг 4: Установка цепочки доверия CA для валидной проверки

Скачать и установить сертификаты УЦ:
```bash
curl -fsSL -o /tmp/issuer.cer "URL_из_CA_эмитента"
/opt/cprocsp/bin/amd64/certmgr -inst -store uRoot -file /tmp/issuer.cer
```

Проверить содержимое хранилищ:
```bash
/opt/cprocsp/bin/amd64/certmgr -list -store uMy
/opt/cprocsp/bin/amd64/certmgr -list -store uCA
/opt/cprocsp/bin/amd64/certmgr -list -store uRoot
/opt/cprocsp/bin/amd64/certmgr -list -store uCRL
```

### Важные замечания

- **Права доступа**: Убедитесь, что пользователь, от имени которого работает веб-сервер (обычно `bitrix`), имеет доступ к контейнерам ключей
- **Безопасность**: Контейнеры закрытых ключей содержат критически важную информацию и должны быть защищены соответствующими правами доступа
- **Расположение**: Контейнеры ключей размещаются в `/var/opt/cprocsp/keys/[пользователь]/`
- **Именование**: Имя контейнера обычно представляет собой hex-строку с расширением `.000`

## Ссылки

- [Оригинальная инструкция по установке PHPCades](https://docs.cryptopro.ru/cades/phpcades/phpcades-install-archived)
- [Скачивание КриптоПро CSP](https://cryptopro.ru/products/csp/downloads)


## Проблемы

- httpd segmentation fault
```
sudo /opt/cprocsp/sbin/amd64/cpconfig -ini '\cryptography\apppath' -add string 'libcurl.so' '/usr/lib64/libcurl.so.4.3.0'
sudo systemctl restart httpd
```
