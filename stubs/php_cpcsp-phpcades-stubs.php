<?php
/** @noinspection PhpUnused */
/** @noinspection PhpInconsistentReturnPointsInspection */
/** @noinspection PhpUndefinedClassInspection */

/**
 * PHPCades Stubs
 * Автоматически сгенерированные заглушки для PHPCades расширения
 * 
 * Генератор: generate_stubs.php
 * Дата: Wed, 17 Sep 2025 18:01:57 +0300
 */


/** @generate-class-entries */

/**
 * Объект AboutВерсия библиотеки. Объект About предоставляет интерфейс IAbout, IAbout3, IAbout4, IAbout5. Объект About может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта About: CAdESCOM. About.
 */
class About {
    public function __construct() {}
    
    /**
     * Возвращает старший компонент версии
     *
     * @return int
     */
    public function get_MajorVersion(): int {}
    
    /**
     * Возвращает младший компонент версии
     *
     * @return int
     */
    public function get_MinorVersion(): int {}
    
    /**
     * @return int
     */
    public function get_BuildVersion(): int {}
    
    /**
     * Возвращает строковое представление версии
     *
     * @return string
     */
    public function get_Version(): string {}
    
    /**
     * @param ?string $provName
     * @param ?int $provType
     * @return CPVersion
     */
    public function CSPVersion(?string $provName = null, ?int $provType = null): CPVersion {}
    
    /**
     * @return CPVersion
     */
    public function PluginVersion(): CPVersion {}
    
}

/**
 * Объект AlgorithmОписывает алгоритм шифрования. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. Algorithm. В отличие от объекта Microsoft CAPICOM. Algorithm, свойство Name данного объекта доступно только для чтения. Объект данного класса нельзя создать. Данный объект возвращает свойство Algorithm объекта CPEnvelopedData.
 */
class CPAlgorithm {
    public function __construct() {}
    
    /**
     * Возвращает алгоритм подписи или шифрования
     *
     * @return int
     */
    public function get_Name(): int {}
    
    /**
     * Устанавливает алгоритм подписи или шифрования
     *
     * @param int $name
     * @return void
     */
    public function set_Name(int $name): void {}
    
    /**
     * Возвращает длину ключа
     *
     * @return int
     */
    public function get_KeyLength(): int {}
    
    /**
     * Устанавливает длину ключа
     *
     * @param int $len
     * @return void
     */
    public function set_KeyLength(int $len): void {}
    
}

/**
 * Объект CPAttributeАтрибут усовершенствованной подписи (подписанный или неподписанный). Объект CPAttribute предоставляет средства для работы с отдельным атрибутом усовершенствованной подписи. Объект CPAttribute предоставляет интерфейсы ICPAttribute и ICPAttribute2. Объект CPAttribute может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта CPAttribute: CAdESCOM. CPAttribute.
 */
class CPAttribute {
    public function __construct() {}
    
    /**
     * Устанавливает объектный идентификатор атрибута
     *
     * @param string $str
     * @return void
     */
    public function set_OID(string $str): void {}
    
    /**
     * Возвращает объектный идентификатор атрибута
     *
     * @return string
     */
    public function get_OID(): string {}
    
    /**
     * Устанавливает закодированное значение атрибута
     *
     * @param string $str
     * @return void
     */
    public function set_Value(string $str): void {}
    
    /**
     * Возвращает закодированное значение атрибута
     *
     * @return string
     */
    public function get_Value(): string {}
    
    /**
     * Устанавливает имя атрибута
     *
     * @param int $lname
     * @return void
     */
    public function set_Name(int $lname): void {}
    
    /**
     * Возвращает имя атрибута
     *
     * @return int
     */
    public function get_Name(): int {}
    
    /**
     * Устанавливает способ кодирования значения атрибута
     *
     * @param int $type
     * @return void
     */
    public function set_ValueEncoding(int $type): void {}
    
    /**
     * Возвращает способ кодирования значения атрибута
     *
     * @return int
     */
    public function get_ValueEncoding(): int {}
    
}

/**
 * Объект CPAttributes = Коллекция объектов CPAttribute. Объект CPAttributes предоставляет интерфейс ICPAttributes. Объект данного класса нельзя создать. Данный объект возвращают свойства AuthenticatedAttributes2 и UnauthenticatedAttributes объекта CPSigner.
 */
class CPAttributes {
    public function __construct() {}
    
    /**
     * Добавляет атрибут в коллекцию
     *
     * @param object $item
     * @return void
     */
    public function Add(object $item): void {}
    
    /**
     * Возвращает количество атрибутов в коллекции
     *
     * @return int
     */
    public function get_Count(): int {}
    
    /**
     * Возвращает атрибут из коллекции по его индексу
     *
     * @param int $idx
     * @return CPAttribute
     */
    public function get_Item(int $idx): CPAttribute {}
    
    /**
     * Удаляет все атрибуты из коллекции
     *
     * @return void
     */
    public function Clear(): void {}
    
    /**
     * Удаляет атрибут из коллекции
     *
     * @param int $idx
     * @return void
     */
    public function Remove(int $idx): void {}
    
    /**
     * Назначает коллекцию атрибутов
     *
     * @param object $attrs
     * @return void
     */
    public function Assign(object $attrs): void {}
    
}

/**
 * Объект BasicConstraintsОписывает основные ограничения на использование сертификата. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. BasicConstraints. Объект данного класса нельзя создать. Данный объект возвращает метод BasicConstraints объекта Certificate.
 */
class CPBasicConstraints {
    public function __construct() {}
    
    /**
     * Присутствует ли расширение
     *
     * @param int $lpr
     * @return void
     */
    public function set_IsPresent(int $lpr): void {}
    
    /**
     * Присутствует ли расширение
     *
     * @return int
     */
    public function get_IsPresent(): int {}
    
    /**
     * Является ли расширение критическим
     *
     * @param int $lcr
     * @return void
     */
    public function set_IsCritical(int $lcr): void {}
    
    /**
     * Является ли расширение критическим
     *
     * @return int
     */
    public function get_IsCritical(): int {}
    
    /**
     * @return int
     */
    public function get_IsCertificateAuthority(): int {}
    
    /**
     * Существует ли ограничение на длину пути сертификата
     *
     * @return int
     */
    public function get_IsPathLenConstraintPresent(): int {}
    
    /**
     * Ограничение на длину пути
     *
     * @return int
     */
    public function get_PathLenConstraint(): int {}
    
}

/**
 * Объект CPCardholderData содержит данные держателя карты. Объект предоставляет методы для получения персональных данных с карты.
 */
class CPCardholderData {
    public function __construct() {}
    
    /**
     * Возвращает номер социального счета
     *
     * @return string
     */
    public function get_SocialAccountNumber(): string {}
    
    /**
     * Возвращает номер полиса ОМС
     *
     * @return string
     */
    public function get_OMSNumber(): string {}
    
    /**
     * Возвращает полное имя держателя карты
     *
     * @return string
     */
    public function get_Name(): string {}
    
    /**
     * Возвращает имя держателя карты
     *
     * @return string
     */
    public function get_FirstName(): string {}
    
    /**
     * Возвращает фамилию держателя карты
     *
     * @return string
     */
    public function get_LastName(): string {}
    
    /**
     * Возвращает отчество держателя карты
     *
     * @return string
     */
    public function get_SecondName(): string {}
    
    /**
     * Возвращает адрес издателя карты
     *
     * @return string
     */
    public function get_CardIssuerAddress(): string {}
    
    /**
     * Возвращает дату рождения держателя карты
     *
     * @return string
     */
    public function get_DateOfBirth(): string {}
    
    /**
     * Возвращает место рождения держателя карты
     *
     * @return string
     */
    public function get_PlaceOfBirth(): string {}
    
    /**
     * Возвращает пол держателя карты в виде строки
     *
     * @return string
     */
    public function get_SexString(): string {}
    
    /**
     * Возвращает пол держателя карты в числовом виде
     *
     * @return int
     */
    public function get_Sex(): int {}
    
}

/**
 * Объект CPCertificateОписывает сертификат открытого ключа. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. Certificate, а интерфейсы ICPCertificate2 и IAdditionalStore. В отличие от объекта Microsoft CAPICOM. Certificate, для данного объекта реализованы только следующие методы и свойства: **Export**, **Import**, **GetInfo**, **HasPrivateKey**, **IsValid**, **IssuerName**, **SerialNumber**, **SubjectName**, **Thumbprint**, **ValidFromDate**, **ValidToDate**, **Version**, **ExtendedKeyUsage**, **KeyUsage**, **PublicKey**, **PrivateKey**, **Extensions**, **BasicConstraints**. Метод AdditionalStore позволяет передать в объект дополнительное хранилище для проверки статуса сертификата. Метод **GetInfo** с использованием параметра CADESCOM_CERT_INFO_ROLE позволяет дополнительно определить положение сертификата в цепочке (ROOT/CA/LEAF). > Метод Export в КриптоПро ЭЦП Browser plug-in не поддерживает кодировку CAPICOM_ENCODE_BINARY. См. Особенности работы с бинарными данными. Объект Certificate может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта Certificate: CAdESCOM. Certificate. Для работы в браузере Internet Explorer рекомендуется использовать объект CAdESCOM. Certificate вместо CAPICOM. Certificate. > В библиотеке типов данный объект присутствует под именем CPCertificate.
 */
class CPCertificate {
    public function __construct() {}
    
    /**
     * Возвращает информацию из сертификата
     *
     * @param int $type
     * @return string
     */
    public function GetInfo(int $type): string {}
    
    /**
     * Производит поиск закрытого ключа соответствующего сертификату открытого ключа и устанавливает ссылку на него
     *
     * @param ?string $str
     * @return void
     */
    public function FindPrivateKey(?string $str = null): void {}
    
    /**
     * Имеется ли закрытый ключ для сертификата
     *
     * @return bool
     */
    public function HasPrivateKey(): bool {}
    
    /**
     * Является ли сертификат валидным
     *
     * @return CPCertificatestat
     */
    public function IsValid(): CPCertificatestat {}
    
    /**
     * Возвращает объект ExtendedKeyUsage для данного сертификата
     *
     * @return exCPKeyUsage
     */
    public function ExtendedKeyUsage(): exCPKeyUsage {}
    
    /**
     * Возвращает объект KeyUsage для данного сертификата
     *
     * @return CPKeyUsage
     */
    public function KeyUsage(): CPKeyUsage {}
    
    /**
     * Экспортирует сертификат в виде закодированной строки
     *
     * @param int $type
     * @return string
     */
    public function Export(int $type): string {}
    
    /**
     * Импортирует сертификат из закодированной строки
     *
     * @param string $str
     * @return void
     */
    public function Import(string $str): void {}
    
    /**
     * Серийный номер
     *
     * @return string
     */
    public function get_SerialNumber(): string {}
    
    /**
     * Отпечаток
     *
     * @return string
     */
    public function get_Thumbprint(): string {}
    
    /**
     * Имя субъекта
     *
     * @return string
     */
    public function get_SubjectName(): string {}
    
    /**
     * Издатель сертификата
     *
     * @return string
     */
    public function get_IssuerName(): string {}
    
    /**
     * Версия сертификата
     *
     * @return int
     */
    public function get_Version(): int {}
    
    /**
     * Дата, до которой сертификат действителен
     *
     * @return string
     */
    public function get_ValidToDate(): string {}
    
    /**
     * Дата, с которой сертификат действителен
     *
     * @return string
     */
    public function get_ValidFromDate(): string {}
    
    /**
     * @return CPBasicConstraints
     */
    public function BasicConstraints(): CPBasicConstraints {}
    
    /**
     * Возвращает объект PublicKey для данного сертификата
     *
     * @return CPPublicKey
     */
    public function PublicKey(): CPPublicKey {}
    
    /**
     * @return CPPrivateKey
     */
    public function PrivateKey(): CPPrivateKey {}
    
}

/**
 * Объект CertificateStatusОписывает статус сертификата открытого ключа. Реализует интерфейс ICPCertificateStatus. Объект данного класса нельзя создать. Данный объект возвращает метод IsValid объекта Certificate. В отличие от объекта Microsoft CAPICOM. CertificateStatus, для данного объекта реализованы только свойство **ValidationCertificates**, **ErrorStatuses**, **Result**.
 */
class CPCertificateStatus {
    public function __construct() {}
    
    /**
     * Является ли сертификат валидным
     *
     * @return int
     */
    public function get_Result(): int {}
    
    /**
     * @return int
     */
    public function get_CheckFlag(): int {}
    
    /**
     * @param int $flag
     * @return void
     */
    public function set_CheckFlag(int $flag): void {}
    
    /**
     * @return eCPKeyUsage
     */
    public function EKU(): eCPKeyUsage {}
    
    /**
     * @return string
     */
    public function get_VerificationTime(): string {}
    
    /**
     * @param string $str
     * @return void
     */
    public function set_VerificationTime(string $str): void {}
    
    /**
     * @return int
     */
    public function get_UrlRetrievalTimeout(): int {}
    
    /**
     * @param int $urt
     * @return void
     */
    public function set_UrlRetrievalTimeout(int $urt): void {}
    
    /**
     * @return string
     */
    public function CertificatePolicies(): string {}
    
    /**
     * @return string
     */
    public function ApplicationPolicies(): string {}
    
    /**
     * @return CPCertificates
     */
    public function get_ValidationCertificates(): CPCertificates {}
    
}

/**
 * Объект CertificatesОписывает коллекцию сертификатов. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. Certificates. Объект данного класса нельзя создать. Данный объект возвращает свойство Certificates объектов Store и CadesSignedData. В отличие от объекта Microsoft CAPICOM. Certificates, для данного объекта реализованы только следующие методы и свойства: **Find**, **Item**, **Count**. > Метод **Find** данного объекта в качестве критерия поиска может принимать дату. > При использовании CAdESCOM следует передавать тип VAR_DATE, а в КриптоПро ЭЦП Browser plug-in - объект JScript Date.
 */
class CPCertificates {
    public function __construct() {}
    
    /**
     * @param int $type
     * @param mixed $query
     * @param int $validOnly
     * @return CPCertificates
     */
    public function Find(int $type, mixed $query, int $validOnly): CPCertificates {}
    
    /**
     * @param int $idx
     * @return CPCertificate
     */
    public function Item(int $idx): CPCertificate {}
    
    /**
     * @return int
     */
    public function Count(): int {}
    
}

/**
 * Объект EKUОписывает расширение EKU сертификата. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. EKU. Объект данного класса нельзя создать. Данный объект является членом коллекции EKUs.
 */
class CPEKU {
    public function __construct() {}
    
    /**
     * Возвращает имя EKU
     *
     * @return int
     */
    public function get_Name(): int {}
    
    /**
     * Устанавливает имя EKU
     *
     * @param int $name
     * @return void
     */
    public function set_Name(int $name): void {}
    
    /**
     * Возвращает объектный идентификатор EKU
     *
     * @return string
     */
    public function get_OID(): string {}
    
    /**
     * Устанавливает объектный идентификатор EKU
     *
     * @param string $oID
     * @return void
     */
    public function set_OID(string $oID): void {}
    
}

/**
 * Объект EKUsОписывает коллекцию расширений EKU сертификата. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. EKUs. Объект данного класса нельзя создать. Данный объект возвращает свойство EKUs объекта ExtendedKeyUsage.
 */
class CPEKUs {
    public function __construct() {}
    
    /**
     * Добавляет EKU в коллекцию
     *
     * @param object $item
     * @return void
     */
    public function Add(object $item): void {}
    
    /**
     * Возвращает количество EKU в коллекции
     *
     * @return int
     */
    public function get_Count(): int {}
    
    /**
     * Возвращает EKU с заданным индексом
     *
     * @param int $idx
     * @return object
     */
    public function get_Item(int $idx): object {}
    
    /**
     * Удаляет все EKU из коллекции
     *
     * @return void
     */
    public function Clear(): void {}
    
    /**
     * Удаляет EKU из коллекции
     *
     * @param int $idx
     * @return void
     */
    public function Remove(int $idx): void {}
    
}

/**
 * Объект EncodedDataОписывает закодированный блок данных. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. EncodedData. Объект данного класса нельзя создать. Данный объект возвращают свойства EncodedKey и EncodedParameters объекта PublicKey. В отличие от объекта Microsoft CAPICOM. EncodedData, для данного объекта реализованы только свойство **Value** и метод **Format**. В асинхронной версии плагина свойство **Value** реализовано как метод. На вход аргументом может передан тип кодировки, который следует использовать при кодировании возвращаемого значения. Аргумент должен иметь значение типа CAPICOM_ENCODING_TYPE. По умолчанию используется CAPICOM_ENCODE_BASE64. Метод **Format** не реализован на *nix платформах. > Свойство **Value** в КриптоПро ЭЦП Browser plug-in не поддерживает кодировку CAPICOM_ENCODE_BINARY. См. Особенности работы с бинарными данными.
 */
class CPEncodedData {
    public function __construct() {}
    
    /**
     * @param int $mL
     * @return string
     */
    public function Format(int $mL): string {}
    
    /**
     * @param int $type
     * @return string
     */
    public function get_Value(int $type): string {}
    
}

/**
 * Объект CPEnvelopedDataЗашифрованное сообщение. Объект CPEnvelopedData предоставляет интерфейсы ICPEnvelopedData, ICPEnvelopedData2 и интерфейс, аналогичный интерфейсу объекта CAPICOM. EnvelopedData. В отличие от объекта CAPICOM. EnvelopedData, объект CPEnvelopedData работает с единственным алгоритмом шифрования - ГОСТ 28147-89. > Метод Encrypt в КриптоПро ЭЦП Browser plug-in не поддерживает кодировку CAPICOM_ENCODE_BINARY. См. Особенности работы с бинарными данными. Объект CPEnvelopedData может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта CPEnvelopedData: CAdESCOM. CPEnvelopedData.
 */
class CPEnvelopedData {
    public function __construct() {}
    
    /**
     * Данные для шифрования
     *
     * @return string
     */
    public function get_Content(): string {}
    
    /**
     * Данные для шифрования
     *
     * @param string $str
     * @return void
     */
    public function set_Content(string $str): void {}
    
    /**
     * Способ кодирования данных
     *
     * @return int
     */
    public function get_ContentEncoding(): int {}
    
    /**
     * Способ кодирования данных
     *
     * @param int $type
     * @return void
     */
    public function set_ContentEncoding(int $type): void {}
    
    /**
     * Выполняет операцию шифрования
     *
     * @param int $type
     * @return string
     */
    public function Encrypt(int $type): string {}
    
    /**
     * @param string $str
     * @return mixed
     */
    public function Decrypt(string $str): mixed {}
    
    /**
     * @return mixed
     */
    public function get_Algorithm(): mixed {}
    
    /**
     * Коллекция сертификатов, для которых выполняется шифрование
     *
     * @return recipients
     */
    public function get_Recipients(): recipients {}
    
}

/**
 * Объект ExtendedKeyUsageОписывает расширенное использование ключа. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. ExtendedKeyUsage. Объект данного класса нельзя создать. Данный объект возвращает метод ExtendedKeyUsage объекта CPCertificate.
 */
class CPExtendedKeyUsage {
    public function __construct() {}
    
    /**
     * Присутствует ли расширение
     *
     * @return int
     */
    public function get_IsPresent(): int {}
    
    /**
     * Является ли расширение критическим
     *
     * @return int
     */
    public function get_IsCritical(): int {}
    
    /**
     * @return eCPKeyUsage_col
     */
    public function get_EKUs(): eCPKeyUsage_col {}
    
}

/**
 * Объект CPHashedDataХэш-значение данных. Объект HashedData предоставляет интерфейсы ICPHashedData, ICPHashedData2 и интерфейс, аналогичный интерфейсу объекта CAPICOM. HashedData. В отличие от объекта CAPICOM. HashedData, объект HashedData поддерживает алгоритм хэширования ГОСТ Р 34. 11-94 и ГОСТ Р 34. 11-2012. Объект HashedData может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта HashedData: CAdESCOM. HashedData.
 */
class CPHashedData {
    public function __construct() {}
    
    /**
     * @param string $sVal
     * @return mixed
     */
    public function Hash(string $sVal): mixed {}
    
    /**
     * Позволяет проинициализировать объект готовым хэш-значением
     *
     * @param string $sVal
     * @return mixed
     */
    public function SetHashValue(string $sVal): mixed {}
    
    /**
     * Возвращает результат операции хэширования
     *
     * @return string
     */
    public function get_Value(): string {}
    
    /**
     * Данные для установки ключа, используемого для вычисления HMAC
     *
     * @param string $val
     * @return void
     */
    public function set_Key(string $val): void {}
    
    /**
     * Данные для установки ключа, используемого для вычисления HMAC
     *
     * @return string
     */
    public function get_Key(): string {}
    
    /**
     * @param int $algorithm
     * @return void
     */
    public function set_Algorithm(int $algorithm): void {}
    
    /**
     * @return int
     */
    public function get_Algorithm(): int {}
    
    /**
     * Способ кодирования данных для хэширования
     *
     * @param int $type
     * @return void
     */
    public function set_DataEncoding(int $type): void {}
    
    /**
     * Способ кодирования данных для хэширования
     *
     * @return int
     */
    public function get_DataEncoding(): int {}
    
}

/**
 * Объект KeyUsageОписывает расширение KeyUsage сертификата. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. KeyUsage. Объект данного класса нельзя создать. Данный объект возвращает метод KeyUsage объекта CPCertificate.
 */
class CPKeyUsage {
    public function __construct() {}
    
    /**
     * Присутствует ли расширение
     *
     * @return bool
     */
    public function get_IsPresent(): bool {}
    
    /**
     * @return bool
     */
    public function get_IsCritical(): bool {}
    
    /**
     * Установлен ли бит digitalSignature
     *
     * @return bool
     */
    public function get_IsDigitalSignatureEnabled(): bool {}
    
    /**
     * Установлен ли бит nonRepudiationEnabled
     *
     * @return bool
     */
    public function get_IsNonRepudiationEnabled(): bool {}
    
    /**
     * Установлен ли бит keyEncipherment
     *
     * @return bool
     */
    public function get_IsKeyEnciphermentEnabled(): bool {}
    
    /**
     * Установлен ли бит dataEncipherment
     *
     * @return bool
     */
    public function get_IsDataEnciphermentEnabled(): bool {}
    
    /**
     * Установлен ли бит keyAgreement
     *
     * @return bool
     */
    public function get_IsKeyAgreementEnabled(): bool {}
    
    /**
     * Установлен ли бит keyCertSign
     *
     * @return bool
     */
    public function get_IsKeyCertSignEnabled(): bool {}
    
    /**
     * @return bool
     */
    public function get_IsCRLSignEnabled(): bool {}
    
    /**
     * Установлен ли бит encipherOnly
     *
     * @return bool
     */
    public function get_IsEncipherOnlyEnabled(): bool {}
    
    /**
     * Установлен ли бит decipherOnly
     *
     * @return bool
     */
    public function get_IsDecipherOnlyEnabled(): bool {}
    
}

/**
 * Объект OIDОписывает объектный идентификатор. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. OID. Объект данного класса нельзя создать. Данный объект возвращает свойство OID объекта CPAttribute.
 */
class CPOID {
    public function __construct() {}
    
    /**
     * Возвращает или устанавливает OID
     *
     * @return string
     */
    public function get_Value(): string {}
    
    /**
     * Возвращает или устанавливает OID
     *
     * @param string $str
     * @return void
     */
    public function set_Value(string $str): void {}
    
    /**
     * @return string
     */
    public function get_FriendlyName(): string {}
    
}

/**
 * Объект PrivateKeyОписывает закрытый ключ сертификата. Обьект предоставляет интерфейс CAdESCOM. ICPPrivateKey, CAdESCOM. ICPPrivateKey2, CAdESCOM. ICPPrivateKey4 и интерфейс аналогичный интерфейсу объекта CAPICOM. PrivateKey. Объект данного класса нельзя создать. Данный объект возвращает свойство PrivateKey объекта CPCertificate. В отличие от объекта Microsoft CAPICOM. PrivateKey, для данного объекта реализованы только следующие методы и свойства: **ContainerName**, **KeySpec**, **ProviderName**, **ProviderType**, **UniqueContainerName**, **KeyPin**, **CachePin**, **ChangePin**.
 */
class CPPrivateKey {
    public function __construct() {}
    
    /**
     * @return string
     */
    public function get_ContainerName(): string {}
    
    /**
     * Возвращает уникальное имя контейнера закрытого ключа
     *
     * @return string
     */
    public function get_UniqueContainerName(): string {}
    
    /**
     * Возвращает имя криптографического провайдера
     *
     * @return string
     */
    public function get_ProviderName(): string {}
    
    /**
     * Возвращает тип криптографического провайдера
     *
     * @return int
     */
    public function get_ProviderType(): int {}
    
    /**
     * @return int
     */
    public function get_KeySpec(): int {}
    
    /**
     * @return mixed
     */
    public function get_UECardholderData(): mixed {}
    
    /**
     * @return mixed
     */
    public function get_UECardWelcomeText(): mixed {}
    
}

/**
 * Объект PublicKeyОписывает открытый ключ сертификата. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. PublicKey. Объект данного класса нельзя создать. Данный объект возвращает метод PublicKey объекта CPCertificate.
 */
class CPPublicKey {
    public function __construct() {}
    
    /**
     * @return string
     */
    public function get_Algorithm(): string {}
    
    /**
     * Возвращает длину открытого ключа в битах
     *
     * @return int
     */
    public function get_Length(): int {}
    
    /**
     * Возвращает значение открытого ключа
     *
     * @return encoded_data
     */
    public function get_EncodedKey(): encoded_data {}
    
    /**
     * Возвращает параметры алгоритма открытого ключа
     *
     * @return encoded_data
     */
    public function get_EncodedParameters(): encoded_data {}
    
}

/**
 * Объект RawSignatureЗначение электронной подписи. Объект RawSignature предоставляет интерфейс IRawSignature. Объект RawSignature может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта RawSignature: CAdESCOM. RawSignature.
 */
class CPRawSignature {
    public function __construct() {}
    
    /**
     * Проверяет значение электронной подписи на основе переданного хэш-значения
     *
     * @param object $zHashedData
     * @param string $sVal
     * @param object $val
     * @return mixed
     */
    public function VerifyHash(object $zHashedData, string $sVal, object $val): mixed {}
    
    /**
     * @param object $zHashedData
     * @param object $zCert
     * @return string
     */
    public function SignHash(object $zHashedData, object $zCert): string {}
    
}

/**
 * Объект RecipientsОписывает коллекцию сертификатов для шифрования. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. Recipients. Объект данного класса нельзя создать. Данный объект возвращает свойство Recipients объекта CPEnvelopedData.
 */
class CPRecipients {
    /**
     * @param object $recipientsZval
     * @return void
     */
    public function Add(object $recipientsZval): void {}
    
    /**
     * Возвращает число объектов в коллекции
     *
     * @return int
     */
    public function get_Count(): int {}
    
    /**
     * Возвращает объект с заданным индексом
     *
     * @param int $index
     * @return CPCertificate
     */
    public function get_Item(int $index): CPCertificate {}
    
    /**
     * Удаляет все сертификаты из коллекции
     *
     * @return void
     */
    public function Clear(): void {}
    
}

/**
 * Объект CPSignedData предоставляет методы для создания и проверки электронной подписи
 */
class CPSignedData {
    public function __construct() {}
    
    /**
     * Создает усовершенствованную подпись CAdES
     *
     * @param object $zSigner
     * @param int $cadesType
     * @param int $detached
     * @param int $encodingType
     * @return string
     */
    public function SignCades(object $zSigner, int $cadesType, int $detached, int $encodingType): string {}
    
    /**
     * @param object $zHashedData
     * @param object $zSigner
     * @param int $cadesType
     * @param int $encodingType
     * @return string
     */
    public function SignHash(object $zHashedData, object $zSigner, int $cadesType, int $encodingType): string {}
    
    /**
     * @param object $zSigner
     * @param int $detached
     * @param int $encodingType
     * @return string
     */
    public function Sign(object $zSigner, int $detached, int $encodingType): string {}
    
    /**
     * @param object $zSigner
     * @param int $encodingType
     * @return string
     */
    public function CoSign(object $zSigner, int $encodingType): string {}
    
    /**
     * @param object $zSigner
     * @param int $cadesType
     * @param int $encodingType
     * @return string
     */
    public function CoSignCades(object $zSigner, int $cadesType, int $encodingType): string {}
    
    /**
     * @param object $zSigner
     * @param object $zHashedData
     * @param int $cadesType
     * @param int $encodingType
     * @return string
     */
    public function CoSignHash(object $zSigner, object $zHashedData, int $cadesType, int $encodingType): string {}
    
    /**
     * @param int $cadesType
     * @param string $tSAAddress
     * @param int $addressLen
     * @return string
     */
    public function EnhanceCades(int $cadesType, string $tSAAddress, int $addressLen): string {}
    
    /**
     * @param string $sSignedMessage
     * @param ?int $signedMessage
     * @param ?int $cadesType
     * @return mixed
     */
    public function VerifyCades(string $sSignedMessage, ?int $signedMessage = null, ?int $cadesType = null): mixed {}
    
    /**
     * @param object $zHashedData
     * @param string $sSignedMessage
     * @param ?int $signedMessage
     * @return mixed
     */
    public function VerifyHash(object $zHashedData, string $sSignedMessage, ?int $signedMessage = null): mixed {}
    
    /**
     * Проверяет подпись
     *
     * @param string $sSignedMessage
     * @param int $signedMessage
     * @param int $detached
     * @return mixed
     */
    public function Verify(string $sSignedMessage, int $signedMessage, int $detached): mixed {}
    
    /**
     * Кодирование содержимого
     *
     * @param int $type
     * @return void
     */
    public function set_ContentEncoding(int $type): void {}
    
    /**
     * Кодирование содержимого
     *
     * @return int
     */
    public function get_ContentEncoding(): int {}
    
    /**
     * Содержимое для подписи
     *
     * @param string $sVal
     * @return void
     */
    public function set_Content(string $sVal): void {}
    
    /**
     * Содержимое для подписи
     *
     * @return string
     */
    public function get_Content(): string {}
    
    /**
     * @return signers
     */
    public function get_Signers(): signers {}
    
    /**
     * @return CPCertificates
     */
    public function get_Certificates(): CPCertificates {}
    
}

/**
 * Объект SignedXMLПодписанный документ XML. Объект SignedXML предоставляет интерфейс ISignedXML. Объект SignedXML может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта SignedXML: CAdESCOM. SignedXML.
 */
class CPSignedXML {
    public function __construct() {}
    
    /**
     * @param string $str
     * @return void
     */
    public function set_Content(string $str): void {}
    
    /**
     * @return string
     */
    public function get_Content(): string {}
    
    /**
     * Тип подписи
     *
     * @param int $ltype
     * @return void
     */
    public function set_SignatureType(int $ltype): void {}
    
    /**
     * Uniform Resource Identifier (URI) алгоритма хэширования
     *
     * @param string $str
     * @return void
     */
    public function set_DigestMethod(string $str): void {}
    
    /**
     * Uniform Resource Identifier (URI) алгоритма подписи
     *
     * @param string $str
     * @return void
     */
    public function set_SignatureMethod(string $str): void {}
    
    /**
     * Коллекция подписей
     *
     * @return signers
     */
    public function get_Signers(): signers {}
    
    /**
     * @param object $zsignedXml
     * @param string $dataStr
     * @return string
     */
    public function Sign(object $zsignedXml, string $dataStr): string {}
    
    /**
     * Проверяет подпись под документом XML
     *
     * @param string $strMes
     * @param string $strPath
     * @return mixed
     */
    public function Verify(string $strMes, string $strPath): mixed {}
    
}

/**
 * Объект CPSignerОбъект, задающий параметры создания и содержащий информацию об усовершенствованной подписи. Объект CPSigner предоставляет интерфейсы ICPSigner6, ICPSigner5, ICPSigner4, ICPSigner3, ICPSigner2, ICPSigner и интерфейс, аналогичный CAPICOM. Signer. Объект CPSigner может создан и является безопасным для использования в сценариях элементов ActiveX. Идентификатор ProgID для объекта CPSigner: CAdESCOM. CPSigner.
 */
class CPSigner {
    public function __construct() {}
    
    /**
     * Сертификат подписанта
     *
     * @return CPCertificate
     */
    public function get_Certificate(): CPCertificate {}
    
    /**
     * Сертификат подписанта
     *
     * @param object $cert
     * @return void
     */
    public function set_Certificate(object $cert): void {}
    
    /**
     * Параметры сертификата подписанта
     *
     * @return int
     */
    public function get_Options(): int {}
    
    /**
     * Параметры сертификата подписанта
     *
     * @param int $opt
     * @return void
     */
    public function set_Options(int $opt): void {}
    
    /**
     * @return CPAttributes_col
     */
    public function get_AuthenticatedAttributes(): CPAttributes_col {}
    
    /**
     * Коллекция неподписанных атрибутов
     *
     * @return CPAttributes_col
     */
    public function get_UnauthenticatedAttributes(): CPAttributes_col {}
    
    /**
     * Адрес службы штампов времени
     *
     * @return string
     */
    public function get_TSAAddress(): string {}
    
    /**
     * Адрес службы штампов времени
     *
     * @param string $str
     * @return void
     */
    public function set_TSAAddress(string $str): void {}
    
    /**
     * Коллекция СОС
     *
     * @return mixed
     */
    public function get_CRLs(): mixed {}
    
    /**
     * Коллекция ответов служб актуальных статусов
     *
     * @return mixed
     */
    public function get_OCSPResponses(): mixed {}
    
    /**
     * Время подписи из атрибута signingTime
     *
     * @return string
     */
    public function get_SigningTime(): string {}
    
    /**
     * Время подписи из штампа времени на значение подписи
     *
     * @return string
     */
    public function get_SignatureTimeStampTime(): string {}
    
    /**
     * Пин-код для доступа к закрытому ключу
     *
     * @param string $str
     * @return void
     */
    public function set_KeyPin(string $str): void {}
    
}

/**
 * Объект CPSignersКоллекция объектов CPSigner. Объект CPSigners предоставляет интерфейс аналогичный CAPICOM. Signers. Объект данного класса нельзя создать. Данный объект возвращает свойство Signers объекта CadesSignedData.
 */
class CPSigners {
    /**
     * Возвращает количество объектов Signer в коллекции
     *
     * @return int
     */
    public function get_Count(): int {}
    
    /**
     * Возвращает объект Signer с заданным индексом
     *
     * @param int $index
     * @return signer
     */
    public function get_Item(int $index): signer {}
    
}

/**
 * Объект StoreОписывает хранилище сертификатов. Реализует интерфейс, аналогичный интерфейсу объекта CAPICOM. Store, а интерфейсы ICPStore2 и IEventSource. В отличие от объекта Microsoft CAPICOM. Store, для данного объекта реализованы только следующие методы и свойства: **Open**, **Close**, **Certificates**, **Location**, **Name**, **Add**, **Remove**. В методе **Open** для параметра Location поддерживаются только значения CAPICOM_CURRENT_USER_STORE, CAPICOM_LOCAL_MACHINE_STORE, CAPICOM_MEMORY_STORE, CAPICOM_SMART_CARD_USER_STORE, CADESCOM_CONTAINER_STORE. Параметр CADESCOM_CONTAINER_STORE предназачен для перечисления всех сертификатов со связанным контейнером закрытого ключа. Работа с хранилищем CAPICOM_SMART_CARD_USER_STORE поддерживается только с КриптоПро CSP 5. 0. 11823 и выше. Метод **Add** поддерживается для хранилищ Root, CA и AddressBook (CA, AddressBook в версиях плагина 2. 0. 15400+) раздела CAPICOM_CURRENT_USER_STORE, а для хранилища типа CADESCOM_MEMORY_STORE.
 */
class CPStore {
    public function __construct() {}
    
    /**
     * Открывает хранилище сертификатов
     *
     * @param int $location
     * @param string $name
     * @param int $mode
     * @return mixed
     */
    public function Open(int $location, string $name, int $mode): mixed {}
    
    /**
     * Закрывает хранилище сертификатов
     *
     * @return mixed
     */
    public function Close(): mixed {}
    
    /**
     * @return CPCertificates
     */
    public function get_Certificates(): CPCertificates {}
    
    /**
     * Возвращает расположение хранилища сертификатов
     *
     * @return int
     */
    public function get_Location(): int {}
    
    /**
     * Возвращает имя хранилища
     *
     * @return string
     */
    public function get_Name(): string {}
    
}

/**
 * Объект SymmetricAlgorithm предоставляет методы для работы с симметричным шифрованием
 */
class SymmetricAlgorithm {
    public function __construct() {}
    
    /**
     * Возвращает данные диверсификации
     *
     * @return string
     */
    public function get_DiversData(): string {}
    
    /**
     * Устанавливает данные диверсификации
     *
     * @param string $str
     * @return void
     */
    public function set_DiversData(string $str): void {}
    
    /**
     * Возвращает вектор инициализации
     *
     * @return string
     */
    public function get_IV(): string {}
    
    /**
     * Устанавливает вектор инициализации
     *
     * @param string $str
     * @return void
     */
    public function set_IV(string $str): void {}
    
    /**
     * Шифрует данные
     *
     * @param string $str
     * @param int $isFinal
     * @return string
     */
    public function Encrypt(string $str, int $isFinal): string {}
    
    /**
     * Расшифровывает данные
     *
     * @param string $str
     * @param ?int $isFinal
     * @return string
     */
    public function Decrypt(string $str, ?int $isFinal = null): string {}
    
    /**
     * Генерирует ключ
     *
     * @param ?int $algo
     * @return mixed
     */
    public function GenerateKey(?int $algo = null): mixed {}
    
    /**
     * Диверсифицирует ключ
     *
     * @return symmetric_algorithm
     */
    public function DiversifyKey(): symmetric_algorithm {}
    
    /**
     * Импортирует ключ
     *
     * @param string $dataStr
     * @param object $ldataStr
     * @param ?string $zRecipient
     * @return mixed
     */
    public function ImportKey(string $dataStr, object $ldataStr, ?string $zRecipient = null): mixed {}
    
    /**
     * Экспортирует ключ
     *
     * @param object $zRecipient
     * @return string
     */
    public function ExportKey(object $zRecipient): string {}
    
}

/**
 * Объект Version описывает информацию о версии
 */
class Version {
    public function __construct() {}
    
    /**
     * Возвращает старший номер версии
     *
     * @return int
     */
    public function get_MajorVersion(): int {}
    
    /**
     * Возвращает младший номер версии
     *
     * @return int
     */
    public function get_MinorVersion(): int {}
    
    /**
     * Возвращает номер сборки
     *
     * @return int
     */
    public function get_BuildVersion(): int {}
    
    /**
     * Возвращает строковое представление версии
     *
     * @return string
     */
    public function toString(): string {}
    
}

