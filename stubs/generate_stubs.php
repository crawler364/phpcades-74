<?php
/**
 * Stub generator for php_CPCSP (CryptoPro CAdES) PHP extension.
 *
 * How to run:
 *   php generate_stubs.php [outputFile] [extensionName]
 *
 * - outputFile (optional): path to write the stub file. Default: phpcades.stub.php in this directory
 * - extensionName (optional): extension name to reflect. Tries common variants automatically if omitted
 *
 * The script will reflect the loaded extension and write a PHP stub file that
 * declares all constants, functions, classes, interfaces, traits, their methods,
 * properties, and parameters (with types where available).
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

// Resolve paths and arguments
$baseDir = __DIR__;
$outFile = $argv[1] ?? ($baseDir . DIRECTORY_SEPARATOR . 'php_cpcsp-phpcades-stubs.php');
$requestedExt = $argv[2] ?? null;

// Candidate extension names
$candidates = array_values(array_unique(array_filter([
    $requestedExt,
    'php_CPCSP',   // most common
    'phpcades',
    'cpcsp',
    'php_cpcsp',
])));

$extName = null;
foreach ($candidates as $cand) {
    if (!$cand) { continue; }
    if (extension_loaded($cand)) { $extName = $cand; break; }
}

if ($extName === null) {
    fwrite(STDERR, "Extension not loaded. Tried names: " . implode(', ', $candidates) . "\n");
    fwrite(STDERR, "Loaded extensions: " . implode(', ', get_loaded_extensions()) . "\n");
    exit(2);
}

$ext = new ReflectionExtension($extName);

// Containers for grouping by namespace
$nsConsts = [];
$nsFunctions = [];
$nsClasses = [];

// Collect constants (top-level)
foreach ($ext->getConstants() as $name => $value) {
    [$ns, $short] = splitNamespace($name);
    $nsConsts[$ns][$short] = $value;
}

// Collect functions
foreach ($ext->getFunctions() as $fname => $rf) {
    [$ns, $short] = splitNamespace($rf->getName());
    $nsFunctions[$ns][$short] = $rf;
}

// Collect classes/interfaces/traits
foreach ($ext->getClasses() as $rc) {
    [$ns, $short] = splitNamespace($rc->getName());
    $nsClasses[$ns][$short] = $rc;
}

ksort($nsConsts);
ksort($nsFunctions);
ksort($nsClasses);

$out = [];
$out[] = '<?php';
$out[] = '/** @noinspection DuplicatedCode */';
$out[] = '/** @noinspection PhpDefineCanBeReplacedWithConstInspection */';
$out[] = '/** @noinspection PhpReturnDocTypeMismatchInspection */';
$out[] = '/** @noinspection PhpMultipleClassDeclarationsInspection */';
$out[] = '/** @noinspection PhpInconsistentReturnPointsInspection */';
$out[] = '/** @noinspection PhpUnused */';
$out[] = '';
$out[] = '/**';
$out[] = ' * Автоматически сгенерированный файл стабсов для расширения CryptoPro CAdES (php_CPCSP).';
$out[] = ' *';
$out[] = ' * Внимание: файл создан программно и предназначен для IDE-подсказок (автодополнение, статический анализ).';
$out[] = ' * Методы и параметры описаны на русском языке. В спорных местах указаны обобщённые типы.';
$out[] = ' * Для точной информации ориентируйтесь на установленную у вас версию расширения и официальную документацию.';
$out[] = ' */';
$out[] = '';

// Render per-namespace blocks
$allNamespaces = array_unique(array_merge(array_keys($nsConsts), array_keys($nsFunctions), array_keys($nsClasses)));
sort($allNamespaces);

foreach ($allNamespaces as $ns) {
    $openNs = $ns !== '' ? "namespace {$ns} {" : null;
    $closeNs = $ns !== '' ? '}' : null;

    if ($openNs) { $out[] = $openNs; }

    // Constants
    if (!empty($nsConsts[$ns])) {
        ksort($nsConsts[$ns]);
        foreach ($nsConsts[$ns] as $name => $_val) {
            $out[] = 'const ' . $name . ' = ' . exportDefault($_val) . ';';
        }
        $out[] = '';
    }

    // Functions
    if (!empty($nsFunctions[$ns])) {
        ksort($nsFunctions[$ns]);
        foreach ($nsFunctions[$ns] as $name => $rf) {
            $out[] = renderFunction($rf) . ' {}';
        }
        $out[] = '';
    }

    // Classes / Interfaces / Traits
    if (!empty($nsClasses[$ns])) {
        ksort($nsClasses[$ns], SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($nsClasses[$ns] as $name => $rc) {
            $out = array_merge($out, renderClassLike($rc));
        }
    }

    if ($closeNs) { $out[] = $closeNs; $out[] = ''; }
}

file_put_contents($outFile, implode("\n", $out));

fwrite(STDOUT, "Stub written to: {$outFile}\n");

// -------- Rendering helpers --------

function splitNamespace(string $fullName): array {
    if (strpos($fullName, '\\') === false) { return ['', $fullName]; }
    $pos = strrpos($fullName, '\\');
    return [substr($fullName, 0, $pos), substr($fullName, $pos + 1)];
}

function renderFunction(ReflectionFunctionAbstract $rf): string {
    $name = splitNamespace($rf->getName())[1];
    $params = [];
    foreach ($rf->getParameters() as $p) {
        $params[] = renderParam($p);
    }
    // If reflection provides no parameters, try generic inference for functions by name
    if (empty($params)) {
        $params = inferFunctionParams($name) ?? [];
    }
    $ret = $rf->hasReturnType() ? (': ' . renderType($rf->getReturnType())) : '';
    $attrs = method_exists($rf, 'getAttributes') ? renderAttributes($rf->getAttributes()) : '';
    $doc = renderDocComment($rf->getDocComment());
    if ($doc === '') {
        $doc = createFunctionDoc($name, $params, $ret ? substr($ret, 2) : null);
    }
    return $doc . $attrs . 'function ' . $name . '(' . implode(', ', $params) . ")" . $ret;
}

function renderClassLike(ReflectionClass $rc): array {
    $lines = [];
    $kind = $rc->isInterface() ? 'interface' : ($rc->isTrait() ? 'trait' : 'class');

    $classShort = splitNamespace($rc->getName())[1];
    $header = $kind . ' ' . $classShort;

    // extends / implements
    $parents = [];
    if ($rc->getParentClass()) {
        $parents[] = 'extends ' . $rc->getParentClass()->getShortName();
    }
    $ifaces = $rc->getInterfaceNames();
    if ($ifaces) {
        $shortIfaces = array_map(function ($fqn) { return (new ReflectionClass($fqn))->getShortName(); }, $ifaces);
        $parents[] = 'implements ' . implode(', ', $shortIfaces);
    }
    if ($parents) { $header .= ' ' . implode(' ', $parents); }

    $modifiers = [];
    if ($rc->isAbstract() && !$rc->isInterface() && !$rc->isTrait()) { $modifiers[] = 'abstract'; }
    if ($rc->isFinal()) { $modifiers[] = 'final'; }

    $doc = renderDocComment($rc->getDocComment());
    if ($doc === '') { $doc = createClassDoc($classShort); }
    $attrs = method_exists($rc, 'getAttributes') ? renderAttributes($rc->getAttributes()) : '';

    $lines[] = $doc . $attrs . implode(' ', $modifiers) . ($modifiers ? ' ' : '') . $header . ' {';

    // Class constants
    foreach ($rc->getReflectionConstants() as $const) {
        if ($const->getDeclaringClass()->getName() !== $rc->getName()) continue;
        $vis = $const->isPublic() ? 'public' : ($const->isProtected() ? 'protected' : 'private');
        $valueCode = 'null';
        try {
            $valueCode = exportDefault($const->getValue());
        } catch (Throwable $e) {
            $valueCode = 'null';
        }
        $lines[] = '    ' . $vis . ' const ' . $const->getName() . ' = ' . $valueCode . ';';
    }

    // Properties
    foreach ($rc->getProperties() as $rp) {
        if ($rp->getDeclaringClass()->getName() !== $rc->getName()) continue;
        $vis = $rp->isPublic() ? 'public' : ($rp->isProtected() ? 'protected' : 'private');
        $static = $rp->isStatic() ? ' static' : '';
        $type = $rp->hasType() ? (renderType($rp->getType()) . ' ') : '';
        $docP = renderDocComment($rp->getDocComment());
        $attrsP = method_exists($rp, 'getAttributes') ? renderAttributes($rp->getAttributes()) : '';
        $lines[] = '    ' . $docP . $attrsP . $vis . $static . ' ' . $type . '$' . $rp->getName() . ';';
    }

    // Methods
    $methods = $rc->getMethods();
    usort($methods, function(ReflectionMethod $a, ReflectionMethod $b) {
        if ($a->getDeclaringClass()->getName() === $b->getDeclaringClass()->getName()) {
            return strcasecmp($a->getName(), $b->getName());
        }
        return strcmp($a->getDeclaringClass()->getName(), $b->getDeclaringClass()->getName());
    });

    foreach ($methods as $rm) {
        if ($rm->getDeclaringClass()->getName() !== $rc->getName()) continue;
        $vis = $rm->isPublic() ? 'public' : ($rm->isProtected() ? 'protected' : 'private');
        $static = $rm->isStatic() ? ' static' : '';
        $abstract = $rm->isAbstract();
        $final = $rm->isFinal() ? ' final' : '';
        $params = [];
        foreach ($rm->getParameters() as $p) { $params[] = renderParam($p); }
        if (empty($params)) {
            // Reflection has no info — try mapping and heuristics
            $params = inferMethodParams($classShort, $rm->getName()) ?? inferHeuristicParams($rm->getName());
        }
        $ret = $rm->hasReturnType() ? (': ' . renderType($rm->getReturnType())) : '';
        $docM = renderDocComment($rm->getDocComment());
        if ($docM === '') { $docM = createMethodDoc($classShort, $rm->getName(), $params, $ret ? substr($ret, 2) : null); }
        $attrsM = method_exists($rm, 'getAttributes') ? renderAttributes($rm->getAttributes()) : '';

        $signature = '    ' . $docM . $attrsM . $vis . $final . ($abstract ? ' abstract' : '') . $static . ' function ' . $rm->getName() . '(' . implode(', ', $params) . ")" . $ret;
        if ($rc->isInterface() || $abstract) {
            $lines[] = $signature . ';';
        } else {
            $lines[] = $signature . ' {}';
        }
    }

    $lines[] = '}';
    $lines[] = '';
    return $lines;
}

function inferFunctionParams(string $name): ?array {
    // No known global functions in php_CPCSP to infer. Return null by default.
    return null;
}

function inferHeuristicParams(string $method): array {
    // Generic heuristics if we have no mapping and no reflection info
    if (stripos($method, 'get_') === 0) return [];
    if (stripos($method, 'set_') === 0) return ['$value'];
    switch ($method) {
        case 'Add': return ['$item'];
        case 'Remove': return ['int $index'];
        case 'Item': return ['int $index'];
        case 'Clear': return [];
        case 'Count': return [];
        case 'Open': return ['$location', '$name', '$mode'];
        case 'Close': return [];
        default: return [];
    }
}

function inferMethodParams(string $classShort, string $method): ?array {
    static $MAP = null;
    if ($MAP === null) {
        $MAP = [
            'CPCertificates' => [
                // Find(certFindType:int, criteria:mixed, validOnly:bool=false)
                'Find' => ['int $findType', '$criteria', 'bool $validOnly = false'],
                'Item' => ['int $index'],
                'Count' => [],
            ],
            'CPAttributes' => [
                'Add' => ['CPAttribute $attribute'],
                'Assign' => ['CPAttributes $sourceAttributes'],
                'Remove' => ['int $index'],
                'get_Item' => ['int $index'],
                'get_Count' => [],
                'Clear' => [],
            ],
            'CPEKUs' => [
                'Add' => ['CPEKU $eku'],
                'Remove' => ['int $index'],
                'get_Item' => ['int $index'],
                'get_Count' => [],
                'Clear' => [],
            ],
            'CPRecipients' => [
                'Add' => ['CPCertificate $certificate'],
                'Clear' => [],
                'get_Item' => ['int $index'],
                'get_Count' => [],
            ],
            'CPSigners' => [
                'get_Item' => ['int $index'],
                'get_Count' => [],
            ],
            'CPSignedData' => [
                'Sign' => ['CPSigner $signer', 'bool $detached = false', 'int $encodingType = 0'],
                'SignCades' => ['CPSigner $signer', 'int $cadesType', 'bool $detached = false', 'int $encodingType = 0'],
                'CoSign' => ['CPSigner $signer', 'int $encodingType = 0'],
                'CoSignCades' => ['CPSigner $signer', 'int $cadesType', 'int $encodingType = 0'],
                'SignHash' => ['CPHashedData $hashedData', 'CPSigner $signer', 'int $cadesType', 'int $encodingType = 0'],
                'CoSignHash' => ['CPSigner $signer', 'CPHashedData $hashedData', 'int $cadesType', 'int $encodingType = 0'],
                'Verify' => ['string $signedMessage', 'bool $detached = false', 'int $verifyFlag = 0'],
                'VerifyCades' => ['string $signedMessage', 'int $cadesType = 0', 'bool $detached = false'],
                'VerifyHash' => ['CPHashedData $hashedData', 'string $signedMessage', 'int $cadesType = 0'],
                'EnhanceCades' => ['int $cadesType', 'string $tsaAddress', 'int $encodingType = 0'],
                'set_Content' => ['string $content'],
                'set_ContentEncoding' => ['int $encoding'],
            ],
            'CPEnvelopedData' => [
                'Encrypt' => ['int $encodingType = 0'],
                'Decrypt' => ['string $encryptedMessage'],
                'set_Content' => ['string $content'],
                'set_ContentEncoding' => ['int $encoding'],
            ],
            'CPHashedData' => [
                'Hash' => ['string $data'],
                'SetHashValue' => ['string $hashValue'],
                'set_Algorithm' => ['int $algorithm'],
                'set_DataEncoding' => ['int $encoding'],
                'set_Key' => ['$key'],
            ],
            'CPEncodedData' => [
                'Format' => ['int $formatType'],
            ],
            'CPStore' => [
                'Open' => ['int $location', 'string $name', 'int $mode'],
                'Close' => [],
            ],
            'CPCertificate' => [
                'Export' => ['int $encodingType'],
                'GetInfo' => ['int $infoType'],
            ],
            'CPAttribute' => [
                'set_Name' => ['string $name'],
                'set_OID' => ['$oid'],
                'set_Value' => ['$value'],
                'set_ValueEncoding' => ['int $encoding'],
            ],
            'CPEKU' => [
                'set_Name' => ['string $name'],
                'set_OID' => ['string $oid'],
            ],
            'CPOID' => [
                'set_Value' => ['string $value'],
            ],
            'CPAlgorithm' => [
                'set_Name' => ['string $name'],
                'set_KeyLength' => ['int $length'],
            ],
            'CPBasicConstraints' => [
                'set_IsCritical' => ['bool $isCritical'],
                'set_IsPresent' => ['bool $isPresent'],
            ],
            'CPSigner' => [
                'set_Certificate' => ['CPCertificate $certificate'],
                'set_Options' => ['int $options'],
                'set_KeyPin' => ['string $pin'],
                'set_TSAAddress' => ['string $url'],
            ],
            'CPSignedXML' => [
                'Sign' => ['CPSigner $signer'],
                'Verify' => [],
                'set_DigestMethod' => ['string $algorithm'],
                'set_SignatureMethod' => ['string $algorithm'],
                'set_SignatureType' => ['int $type'],
                'set_Content' => ['string $xml'],
            ],
            'SymmetricAlgorithm' => [
                'Encrypt' => ['string $data'],
                'Decrypt' => ['string $encryptedData'],
                'GenerateKey' => [],
                'DiversifyKey' => ['string $diversifier'],
                'ExportKey' => ['$options = null'],
                'ImportKey' => ['string $blob'],
                'set_IV' => ['string $iv'],
                'set_DiversData' => ['string $data'],
            ],
        ];
    }
    if (isset($MAP[$classShort][$method])) {
        return $MAP[$classShort][$method];
    }
    // Also check for method without get_/set_ prefix normalized through heuristics
    return null;
}

function renderParam(ReflectionParameter $p): string {
    $type = $p->hasType() ? (renderType($p->getType()) . ' ') : '';
    $byRef = $p->isPassedByReference() ? '&' : '';
    $vari = $p->isVariadic() ? '...' : '';
    $def = '';
    if ($p->isOptional()) {
        if ($p->isDefaultValueAvailable()) {
            try {
                $dv = $p->getDefaultValue();
                $def = ' = ' . exportDefault($dv);
            } catch (ReflectionException $e) {
                $def = ' = null';
            }
        } else {
            // Optional without default value means = null in stubs (safe for analysis)
            $def = ' = null';
        }
    }
    $name = '$' . $p->getName();
    return $type . $byRef . $vari . $name . $def;
}

function renderType(ReflectionType $t): string {
    $cls = is_object($t) ? get_class($t) : '';
    if (PHP_VERSION_ID >= 80000 && $cls === 'ReflectionUnionType') {
        $types = method_exists($t, 'getTypes') ? $t->getTypes() : [];
        return implode('|', array_map('renderType', $types));
    }
    if (PHP_VERSION_ID >= 80100 && $cls === 'ReflectionIntersectionType') {
        $types = method_exists($t, 'getTypes') ? $t->getTypes() : [];
        return implode('&', array_map('renderType', $types));
    }
    // ReflectionNamedType (or PHP 7.4 simple type)
    $allowsNull = $t->allowsNull();
    $name = method_exists($t, 'getName') ? $t->getName() : (string)$t;
    $lower = strtolower($name);
    $builtin = in_array($lower, ['int','float','string','bool','array','object','callable','iterable','mixed','void','null','false','true'], true);
    $nullablePrefix = ($allowsNull && $lower !== 'mixed' && $lower !== 'null') ? '?' : '';
    return $nullablePrefix . $name;
}

function exportDefault($v): string {
    switch (gettype($v)) {
        case 'NULL': return 'null';
        case 'boolean': return $v ? 'true' : 'false';
        case 'integer':
        case 'double': return (string)$v;
        case 'string': return var_export($v, true);
        case 'array': return '[]'; // stubs do not need concrete values
        default: return 'null';
    }
}

function renderDocComment($doc): string {
    if (!$doc) return '';
    // Keep as-is but on one line above next declaration
    $doc = trim($doc);
    return $doc . "\n";
}

function createFunctionDoc(string $name, array $paramSigs, ?string $retType): string {
    $params = [];
    foreach ($paramSigs as $sig) {
        [$type, $pname] = parseParamSig($sig);
        $params[] = [$type ?: 'mixed', $pname, 'Параметр ' . $pname . '.'];
    }
    $summary = 'Глобальная функция ' . $name . '.';
    $return = $retType ?: 'mixed';
    return buildDocBlock($summary, $params, $return);
}

function renderAttributes(array $attrs): string {
    if (empty($attrs)) return '';
    $lines = [];
    foreach ($attrs as $a) {
        $args = [];
        foreach ($a->getArguments() as $k => $v) {
            $exported = exportDefault($v);
            if (is_string($k)) { $args[] = $k . ': ' . $exported; } else { $args[] = $exported; }
        }
        $lines[] = '#[' . $a->getName() . (empty($args) ? '' : '(' . implode(', ', $args) . ')') . ']';
    }
    return implode("\n", $lines) . (empty($lines) ? '' : "\n");
}

// ---------------- Auto PHPDoc generation (Russian) ----------------

function createClassDoc(string $classShort): string {
    static $CLASS_DESC = [
        'About' => 'Информация о версиях и сборках криптопровайдера и плагина.',
        'CPAlgorithm' => 'Алгоритм шифрования/хеширования/подписи и его параметры.',
        'CPAttribute' => 'Атрибут CMS/CAdES (OID, имя, значение, кодировка).',
        'CPAttributes' => 'Коллекция атрибутов CMS/CAdES.',
        'CPBasicConstraints' => 'Расширение сертификата BasicConstraints.',
        'CPCertificate' => 'Сертификат X.509 с методами экспорта и получения свойств.',
        'CPCertificates' => 'Коллекция сертификатов с поиском и доступом по индексу.',
        'CPCertificateStatus' => 'Параметры и результат проверки статуса сертификата.',
        'CPEKU' => 'Один OID из расширенного назначения ключа (EKU).',
        'CPEKUs' => 'Коллекция OID из расширенного назначения ключа (EKU).',
        'CPEncodedData' => 'Двоичные данные, представленные в закодированном виде.',
        'CPEnvelopedData' => 'Сообщение CMS EnvelopedData (шифрование для получателей).',
        'CPExtendedKeyUsage' => 'Расширение сертификата ExtendedKeyUsage.',
        'CPHashedData' => 'Объект вычисления/хранения хеш-значения.',
        'CPKeyUsage' => 'Расширение сертификата KeyUsage.',
        'CPOID' => 'Объектный идентификатор (OID) с дружественным именем.',
        'CPPrivateKey' => 'Сведения о закрытом ключе, связанном с сертификатом.',
        'CPPublicKey' => 'Сведения об открытом ключе сертификата.',
        'CPRawSignature' => 'Низкоуровневые операции подписи/проверки по хешу.',
        'CPRecipients' => 'Коллекция получателей для EnvelopedData.',
        'CPSignedData' => 'Подписываемые данные и операции CAdES/PKCS#7.',
        'CPSignedXML' => 'Подпись XML (XMLDSIG) и проверка подписи.',
        'CPSigner' => 'Настройки подписанта и атрибуты подписи.',
        'CPSigners' => 'Коллекция подписантов из подписи.',
        'CPStore' => 'Хранилище сертификатов.',
        'SymmetricAlgorithm' => 'Симметричный алгоритм шифрования (ключ, IV, операции).',
        'Version' => 'Версия библиотеки/плагина.',
    ];
    $summary = $CLASS_DESC[$classShort] ?? ('Класс ' . $classShort . ' — объект API CryptoPro CAdES.');
    return buildDocBlock($summary);
}

function createMethodDoc(string $classShort, string $method, array $paramSigs, ?string $retType): string {
    [$summary, $paramDescMap, $retDocType] = methodDocLookup($classShort, $method, $retType);
    $params = [];
    foreach ($paramSigs as $sig) {
        [$type, $name] = parseParamSig($sig);
        $desc = $paramDescMap[$name] ?? ('Параметр ' . $name . '.');
        $params[] = [$type ?: 'mixed', $name, $desc];
    }
    $returnType = $retDocType ?: inferReturnDocType($classShort, $method, $retType);
    return buildDocBlock($summary, $params, $returnType);
}

function methodDocLookup(string $classShort, string $method, ?string $retType): array {
    // Returns [summary, paramDescMap, returnType|null]
    $M = [
        'CPSignedData' => [
            'set_Content' => ['Устанавливает содержимое, которое будет подписано.', ['content' => 'Данные для подписи (строка).'], 'void'],
            'set_ContentEncoding' => ['Задаёт кодировку содержимого.', ['encoding' => 'Тип кодирования данных: ENCODE_BASE64, ENCODE_BINARY и т. п.'], 'void'],
            'get_Content' => ['Возвращает текущее содержимое для подписи.', [], 'string'],
            'get_ContentEncoding' => ['Возвращает текущий тип кодирования содержимого.', [], 'int'],
            'Sign' => ['Создаёт подпись PKCS#7 (CMS) над содержимым.', ['signer' => 'Подписант и его параметры.', 'detached' => 'Если true — создаётся отсоединённая подпись.'], 'string'],
            'SignCades' => ['Создаёт подпись CAdES указанного типа.', ['signer' => 'Подписант и его параметры.', 'cadesType' => 'Тип CAdES: CADES_BES, CADES_T, CADES_X_LONG_TYPE_1.', 'detached' => 'Если true — отсоединённая подпись.'], 'string'],
            'SignHash' => ['Создаёт подпись по заранее вычисленному хешу.', ['signer' => 'Подписант и его параметры.'], 'string'],
            'CoSign' => ['Добавляет дополнительную подпись к уже подписанным данным.', ['signer' => 'Подписант для дополнительной подписи.'], 'string'],
            'CoSignCades' => ['Добавляет дополнительную CAdES-подпись указанного типа.', ['signer' => 'Подписант для дополнительной подписи.', 'cadesType' => 'Тип CAdES: CADES_BES, CADES_T...'], 'string'],
            'CoSignHash' => ['Добавляет подпись по заранее вычисленному хешу.', ['signer' => 'Подписант для дополнительной подписи.'], 'string'],
            'Verify' => ['Проверяет подпись PKCS#7 (CMS).', ['signedMessage' => 'Подписанное сообщение (PKCS#7).', 'detached' => 'Если true — проверяется отсоединённая подпись.'], 'bool'],
            'VerifyCades' => ['Проверяет подпись CAdES указанного типа.', ['signedMessage' => 'Подписанное сообщение (PKCS#7/CAdES).', 'cadesType' => 'Тип CAdES для проверки.', 'detached' => 'Если true — проверяется отсоединённая подпись.'], 'bool'],
            'VerifyHash' => ['Проверяет подпись по ранее вычисленному хешу.', ['hash' => 'Объект CPHashedData или строка хеш-значения.', 'signedMessage' => 'Подписанное сообщение (PKCS#7).'], 'bool'],
            'EnhanceCades' => ['Усиливает подпись до указанного уровня CAdES.', ['signature' => 'Подпись (PKCS#7) для усиления.', 'cadesType' => 'Желаемый уровень CAdES.'], 'string'],
            'get_Signers' => ['Возвращает коллекцию подписантов.', [], 'CPSigners'],
            'get_Certificates' => ['Возвращает сертификаты из подписи.', [], 'CPCertificates'],
        ],
        'CPSignedXML' => [
            'set_Content' => ['Задаёт XML-документ для подписи.', ['xml' => 'XML-строка.'], 'void'],
            'set_DigestMethod' => ['Устанавливает алгоритм хеширования для XML-подписи.', ['algorithm' => 'URI/имя алгоритма (например, GOST, SHA256).'], 'void'],
            'set_SignatureMethod' => ['Устанавливает алгоритм подписи для XML.', ['algorithm' => 'URI/имя алгоритма подписи.'], 'void'],
            'set_SignatureType' => ['Выбирает тип XML-подписи.', ['type' => 'Тип подписи: XML_SIGNATURE_TYPE_ENVELOPED, ENVELOPING, TEMPLATE.'], 'void'],
            'Sign' => ['Создаёт XML-подпись.', ['signer' => 'Подписант и его параметры.'], 'string'],
            'Verify' => ['Проверяет XML-подпись.', [], 'bool'],
        ],
        'CPSigner' => [
            'set_Certificate' => ['Назначает сертификат, которым будет выполняться подпись.', ['certificate' => 'Сертификат подписанта.'], 'void'],
            'set_KeyPin' => ['Задаёт PIN-код (пароль) для доступа к закрытому ключу.', ['pin' => 'PIN/пароль.'], 'void'],
            'set_Options' => ['Устанавливает флаги/опции подписи.', ['options' => 'Битовая маска опций.'], 'void'],
            'set_TSAAddress' => ['Указывает адрес службы меток времени (TSA).', ['url' => 'URL TSA.'], 'void'],
            'get_Certificate' => ['Возвращает текущий сертификат подписанта.', [], 'CPCertificate'],
        ],
        'CPStore' => [
            'Open' => ['Открывает хранилище сертификатов.', ['location' => 'Расположение хранилища (CURRENT_USER_STORE, LOCAL_MACHINE_STORE и т. п.).', 'name' => 'Имя хранилища (например, "My").', 'mode' => 'Режим открытия (STORE_OPEN_*)'], 'void'],
            'Close' => ['Закрывает хранилище сертификатов.', [], 'void'],
            'get_Certificates' => ['Возвращает коллекцию сертификатов хранилища.', [], 'CPCertificates'],
        ],
        'CPCertificates' => [
            'Find' => ['Ищет сертификаты по критерию.', ['findType' => 'Тип поиска (CERTIFICATE_FIND_*).', 'criteria' => 'Критерий поиска.', 'validOnly' => 'Если true — только действительные.'], 'CPCertificates'],
            'Item' => ['Возвращает сертификат по индексу.', ['index' => '1-индексация или 0-? зависит от сборки.'], 'CPCertificate'],
            'Count' => ['Количество сертификатов в коллекции.', [], 'int'],
        ],
        'CPRecipients' => [
            'Add' => ['Добавляет сертификат получателя.', ['certificate' => 'Сертификат получателя.'], 'void'],
            'Clear' => ['Очищает список получателей.', [], 'void'],
            'get_Item' => ['Возвращает получателя по индексу.', ['index' => 'Индекс получателя.'], 'CPCertificate'],
            'get_Count' => ['Возвращает количество получателей.', [], 'int'],
        ],
        'CPEnvelopedData' => [
            'set_Content' => ['Задаёт открытый текст сообщения для шифрования.', ['content' => 'Данные, которые будут зашифрованы.'], 'void'],
            'set_ContentEncoding' => ['Устанавливает кодировку содержимого.', ['encoding' => 'Тип кодирования данных.'], 'void'],
            'Encrypt' => ['Шифрует данные для указанных получателей.', ['recipients' => 'Коллекция получателей (CPRecipients) или совместимый тип.'], 'string'],
            'Decrypt' => ['Расшифровывает зашифрованное сообщение.', ['encryptedMessage' => 'Зашифрованное сообщение (PKCS#7/CMS).'], 'string'],
        ],
        'CPHashedData' => [
            'Hash' => ['Вычисляет хеш от данных.', ['data' => 'Данные, для которых вычисляется хеш.'], 'void'],
            'SetHashValue' => ['Устанавливает готовое хеш-значение.', ['hashValue' => 'Хеш-значение (строка, hex/base64).'], 'void'],
            'set_Algorithm' => ['Выбирает алгоритм хеширования.', ['algorithm' => 'Алгоритм: CADESCOM_HASH_ALGORITHM_* или GOST и т. п.'], 'void'],
            'set_DataEncoding' => ['Задаёт кодирование входных данных.', ['encoding' => 'Тип кодирования: ENCODE_BASE64, ENCODE_BINARY...'], 'void'],
            'set_Key' => ['Устанавливает ключ (для HMAC и др.).', ['key' => 'Ключ или объект ключа.'], 'void'],
            'get_Value' => ['Возвращает вычисленный хеш.', [], 'string'],
        ],
        'CPCertificate' => [
            'Export' => ['Экспортирует сертификат в указанной кодировке.', ['encodingType' => 'Тип кодировки: ENCODE_BASE64/ENCODE_BINARY и т. п.'], 'string'],
            'GetInfo' => ['Возвращает информацию по коду.', ['infoType' => 'Код информации CERT_INFO_*.'], 'mixed'],
            'BasicConstraints' => ['Возвращает расширение BasicConstraints.', [], 'CPBasicConstraints'],
            'ExtendedKeyUsage' => ['Возвращает расширение ExtendedKeyUsage.', [], 'CPExtendedKeyUsage'],
            'FindPrivateKey' => ['Возвращает информацию о связке с закрытым ключом.', [], 'CPPrivateKey'],
            'HasPrivateKey' => ['Проверяет наличие связанного закрытого ключа.', [], 'bool'],
            'Import' => ['Импортирует сертификат из внутреннего представления.', [], 'void'],
            'IsValid' => ['Выполняет базовую проверку валидности.', [], 'CPCertificateStatus'],
            'KeyUsage' => ['Возвращает расширение KeyUsage.', [], 'CPKeyUsage'],
            'PrivateKey' => ['Возвращает информацию о закрытом ключе.', [], 'CPPrivateKey'],
            'PublicKey' => ['Возвращает информацию о открытом ключе.', [], 'CPPublicKey'],
        ],
    ];

    $summary = null; $paramDesc = []; $ret = null;
    if (isset($M[$classShort][$method])) {
        [$summary, $paramDesc, $ret] = $M[$classShort][$method];
    } else {
        // Heuristics
        if ($method === '__construct') {
            $summary = 'Создаёт новый экземпляр класса ' . $classShort . '.';
            $ret = 'void';
        } elseif (stripos($method, 'get_') === 0) {
            $summary = 'Возвращает значение свойства ' . substr($method, 4) . '.';
            $ret = $retType ?: 'mixed';
        } elseif (stripos($method, 'set_') === 0) {
            $summary = 'Устанавливает значение свойства ' . substr($method, 4) . '.';
            $ret = 'void';
            // Default param name 'value' if only one param without explicit mapping
            $paramDesc['value'] = 'Новое значение.';
        } else {
            switch ($method) {
                case 'Add': $summary = 'Добавляет элемент в коллекцию.'; $ret = 'void'; $paramDesc['item'] = 'Добавляемый элемент.'; break;
                case 'Remove': $summary = 'Удаляет элемент из коллекции.'; $ret = 'void'; $paramDesc['index'] = 'Индекс удаляемого элемента.'; break;
                case 'Item': $summary = 'Возвращает элемент по индексу.'; $ret = 'mixed'; $paramDesc['index'] = 'Индекс элемента.'; break;
                case 'Clear': $summary = 'Очищает коллекцию.'; $ret = 'void'; break;
                case 'Count': $summary = 'Возвращает количество элементов.'; $ret = 'int'; break;
                case 'Open': $summary = 'Открывает объект/ресурс.'; $ret = 'void'; $paramDesc += ['location' => 'Расположение.', 'name' => 'Имя.', 'mode' => 'Режим.']; break;
                case 'Close': $summary = 'Закрывает объект/ресурс.'; $ret = 'void'; break;
                case 'Format': $summary = 'Форматирует данные согласно указанному типу.'; $ret = 'string'; $paramDesc['formatType'] = 'Тип формата.'; break;
                case 'Export': $summary = 'Экспортирует данные в указанной кодировке.'; $ret = 'string'; $paramDesc['encodingType'] = 'Тип кодировки.'; break;
                case 'Import': $summary = 'Импортирует данные из внутреннего представления.'; $ret = 'void'; break;
                default: $summary = 'Выполняет операцию ' . $method . '.'; $ret = $retType ?: 'mixed'; break;
            }
        }
    }
    return [$summary, $paramDesc, $ret];
}

function inferReturnDocType(string $classShort, string $method, ?string $retType): string {
    if ($retType) return $retType;
    if (stripos($method, 'get_') === 0) return 'mixed';
    if (stripos($method, 'set_') === 0) return 'void';
    switch ($method) {
        case 'Count': return 'int';
        case 'Open':
        case 'Close':
        case '__construct': return 'void';
        default: return 'mixed';
    }
}

function parseParamSig(string $sig): array {
    // returns [type|null, name]
    $type = null; $name = 'param';
    if (preg_match('/^\s*([^$]+)?\$(\w+)/', $sig, $m)) {
        $type = trim($m[1] ?? '') ?: null;
        $name = $m[2];
    } elseif (preg_match('/\$(\w+)/', $sig, $m)) {
        $name = $m[1];
    }
    return [$type, $name];
}

function buildDocBlock(string $summary, array $params = [], ?string $return = null): string {
    $lines = ["/**", ' * ' . trim($summary)];
    if (!empty($params)) {
        $lines[] = ' *';
        foreach ($params as [$type, $name, $desc]) {
            $lines[] = ' * @param ' . ($type ?: 'mixed') . ' $' . $name . ' ' . trim($desc);
        }
    }
    if ($return !== null) {
        $lines[] = ' *';
        $lines[] = ' * @return ' . $return;
    }
    $lines[] = ' */';
    return implode("\n", $lines) . "\n";
}
