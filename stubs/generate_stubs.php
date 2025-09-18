<?php
/**
 * Генератор PHP stubs для PHPCades
 * Парсит .cpp файлы исходников PHPCades для создания .stub.php файлов
 * Использует JSON карту документации для добавления описаний в PHPDoc
 */

// Глобальная переменная для хранения карты документации
$documentationMap = null;

/**
 * Загружает карту документации из JSON файла
 */
function loadDocumentationMap($jsonFile) {
    global $documentationMap;
    
    if (file_exists($jsonFile)) {
        $json = file_get_contents($jsonFile);
        $documentationMap = json_decode($json, true);
        
        if ($documentationMap === null) {
            echo "⚠️ Ошибка загрузки JSON карты документации\n";
            $documentationMap = [];
        } else {
            echo "✅ Загружена карта документации: " . count($documentationMap) . " классов\n";
        }
    } else {
        echo "⚠️ JSON карта документации не найдена: $jsonFile\n";
        $documentationMap = [];
    }
}

/**
 * Ищет описание класса в карте документации
 */
function getClassDescription($className) {
    global $documentationMap;
    
    if (isset($documentationMap[$className]['description'])) {
        return $documentationMap[$className]['description'];
    }
    
    return '';
}

/**
 * Ищет описание метода в карте документации
 */
function getMethodDescription($className, $methodName) {
    global $documentationMap;
    
    if (isset($documentationMap[$className]['methods'][$methodName]['description'])) {
        return $documentationMap[$className]['methods'][$methodName]['description'];
    }
    
    return '';
}

/**
 * Извлекает имя свойства из имени метода (геттер/сеттер)
 */
function extractPropertyName($methodName) {
    if (strpos($methodName, 'get_') === 0) {
        return substr($methodName, 4);
    } elseif (strpos($methodName, 'set_') === 0) {
        return substr($methodName, 4);
    }
    return '';
}

/**
 * Ищет описание свойства в карте документации (для геттеров/сеттеров)
 */
function getPropertyDescription($className, $methodName) {
    global $documentationMap;
    
    $propertyName = extractPropertyName($methodName);
    
    if (!empty($propertyName) && isset($documentationMap[$className]['properties'][$propertyName]['description'])) {
        return $documentationMap[$className]['properties'][$propertyName]['description'];
    }
    
    return '';
}

/**
 * Получает описания параметров метода из карты документации
 */
function getParameterDescriptions($className, $methodName) {
    global $documentationMap;
    
    $descriptions = [];
    if (isset($documentationMap[$className]['methods'][$methodName]['parameters'])) {
        foreach ($documentationMap[$className]['methods'][$methodName]['parameters'] as $param) {
            $descriptions[$param['name']] = $param['description'];
        }
    }
    
    return $descriptions;
}

/**
 * Получает return_type из карты документации для методов и свойств
 */
function getReturnTypeFromMap($className, $methodName) {
    global $documentationMap;

    if (isset($documentationMap[$className]['methods'][$methodName]['return_type'])) {
        return $documentationMap[$className]['methods'][$methodName]['return_type'];
    }

    $propertyName = extractPropertyName($methodName);
    
    if (!empty($propertyName) && isset($documentationMap[$className]['properties'][$propertyName]['return_type'])) {
        if (strpos($methodName, 'set_') === 0) {
            return 'void';
        }
        return $documentationMap[$className]['properties'][$propertyName]['return_type'];
    }
    
    return '';
}

/**
 * Парсит константы из исходников PHPCades
 */
function parseConstants($srcDir) {
    $constants = [];
    
    // Поиск файлов с константами (обычно dllmain.cpp)
    $cppFiles = glob("$srcDir/*.cpp");
    
    foreach ($cppFiles as $file) {
        $content = file_get_contents($file);
        
        // Парсим REGISTER_LONG_CONSTANT
        if (preg_match_all('/REGISTER_LONG_CONSTANT\s*\(\s*"([^"]+)"\s*,\s*([^,]+)\s*,/', $content, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $name = $matches[1][$i];
                $value = trim($matches[2][$i]);
                
                // Преобразуем значение в корректный PHP формат
                if (is_numeric($value)) {
                    $constants[$name] = (int)$value;
                } else {
                    $constants[$name] = $value;
                }
            }
        }
    }
    
    return $constants;
}

function parsePhpCadesClasses($srcDir) {
    $classes = [];
    
    // Поиск всех .cpp файлов
    $cppFiles = glob("$srcDir/*.cpp");
    
    foreach ($cppFiles as $file) {
        if (strpos($file, 'PHPCades') === false) {
            continue;
        }
        
        echo "Парсинг файла: " . basename($file) . "\n";
        $content = file_get_contents($file);
        
        // Извлекаем имя класса из INIT_CLASS_ENTRY
        if (preg_match('/INIT_CLASS_ENTRY\s*\(\s*ce\s*,\s*"([^"]+)"\s*,/', $content, $matches)) {
            $className = $matches[1];
            echo "  Найден класс: $className\n";
            
            // Извлекаем методы из массива zend_function_entry
            $methods = [];
            if (preg_match('/zend_function_entry\s+\w*methods\[\]\s*=\s*\{([^}]+)\}/', $content, $methodMatches)) {
                $methodsBlock = $methodMatches[1];
                
                // Парсим каждый PHP_ME
                preg_match_all('/PHP_ME\s*\([^,]+,\s*(\w+)[^)]*\)/', $methodsBlock, $methodNames);
                
                foreach ($methodNames[1] as $methodName) {
                    echo "    Найден метод: $methodName\n";
                    
                    // Ищем реализацию метода для определения параметров
                    $params = parseMethodParameters($content, $methodName);
                    $returnType = parseReturnType($content, $methodName);
                    
                    $methods[$methodName] = [
                        'params' => $params,
                        'return' => $returnType
                    ];
                }
            }
            
            $classes[$className] = [
                'methods' => $methods
            ];
        }
    }
    
    return $classes;
}

function getParamTypeFromChar($char, $optional) {
    switch ($char) {
        case 'l':
            return $optional ? '?int' : 'int';
        case 's':
            return $optional ? '?string' : 'string';
        case 'z':
            return 'mixed';
        case 'b':
            return $optional ? '?bool' : 'bool';
        case 'd':
            return $optional ? '?float' : 'float';
        case 'o':
        case 'O':
            return 'object';
        case 'a':
            return 'array';
        default:
            return null;
    }
}

function normalizeParameterName($varName, $paramType, $methodName) {
    $varName = preg_replace('/^(l|sz|str|p|n|b|dw)([A-Z])/', '$2', $varName);
    
    if (strpos($varName, '_') !== false) {
        $parts = explode('_', strtolower($varName));
        $varName = $parts[0];
        for ($i = 1; $i < count($parts); $i++) {
            $varName .= ucfirst($parts[$i]);
        }
    } else {
        $varName = lcfirst($varName);
    }
    
    return $varName;
}

function parseMethodParameters($content, $methodName) {
    $params = [];
    
    // Ищем блок метода
    if (preg_match("/PHP_METHOD\s*\([^,]+,\s*$methodName\s*\)[^{]*\{(.*?)(?=^PHP_METHOD|\n})/ms", $content, $methodBlock)) {
        $methodContent = $methodBlock[1];
        
        // Ищем полный вызов zend_parse_parameters с параметрами
        if (preg_match('/zend_parse_parameters\s*\([^"]*"([^"]*)"([^)]+)\)/', $methodContent, $parseMatch)) {
            $formatString = $parseMatch[1];
            $parametersString = $parseMatch[2];
            
            $variableNames = [];
            if (preg_match_all('/&(\w+)/', $parametersString, $varMatches)) {
                $variableNames = $varMatches[1];
            }
            
            // Разбираем формат параметров и сопоставляем с именами
            $optional = false;
            $paramIndex = 0;
            $varIndex = 0;
            
            for ($i = 0; $i < strlen($formatString); $i++) {
                $char = $formatString[$i];
                
                if ($char === '|') {
                    $optional = true;
                    continue;
                }
                
                $paramType = '';
                $paramName = isset($variableNames[$varIndex]) ? 
                    normalizeParameterName($variableNames[$varIndex], $paramType, $methodName) : 
                    "param$paramIndex";
                
                $paramType = getParamTypeFromChar($char, $optional);
                if ($paramType === null) {
                    continue;
                }
                
                $params[] = [
                    'type' => $paramType,
                    'name' => $paramName,
                    'optional' => $optional
                ];
                
                $varIndex++;
                $paramIndex++;
                
                if ($char === 's' && isset($variableNames[$varIndex]) && 
                    (strpos($variableNames[$varIndex], 'len') !== false || 
                     strpos($variableNames[$varIndex], 'size') !== false)) {
                    $varIndex++;
                }
            }
        }
    }
    
    return $params;
}

function parseReturnType($content, $methodName) {
    if (preg_match("/PHP_METHOD\s*\([^,]+,\s*$methodName\s*\)[^{]*\{(.*?)(?=^PHP_METHOD|\n})/ms", $content, $methodBlock)) {
        $methodContent = $methodBlock[1];
        
        if (strpos($methodContent, 'RETURN_ATL_STRING') !== false || 
            strpos($methodContent, 'RETURN_PROXY_STRING') !== false ||
            strpos($methodContent, 'RETURN_STRINGL') !== false) {
            return 'string';
        }
        
        if (strpos($methodContent, 'RETURN_LONG') !== false) {
            return 'int';
        }
        
        if (strpos($methodContent, 'RETURN_TRUE') !== false || 
            strpos($methodContent, 'RETURN_FALSE') !== false) {
            return 'bool';
        }
    }
    
    return '';
}

function generateStubHeader() {
    $content = "<?php\n";
    $content .= "/** @noinspection PhpUnused */\n";
    $content .= "/** @noinspection PhpInconsistentReturnPointsInspection */\n";
    $content .= "/** @noinspection PhpUndefinedClassInspection */\n";
    $content .= "/** @noinspection PhpReturnDocTypeMismatchInspection */\n\n";
    $content .= "/**\n";
    $content .= " * PHPCades Stubs\n";
    $content .= " * Автоматически сгенерированные заглушки для PHPCades расширения\n";
    $content .= " * \n";
    $content .= " * Генератор: generate_stubs.php\n";
    $content .= " * Дата: " . date('r') . "\n";
    $content .= " */\n\n";
    $content .= "\n/** @generate-class-entries */\n\n";
    
    return $content;
}

function generateStubConstants($constants) {
    if (empty($constants)) {
        return '';
    }
    
    $content = "// Константы расширения php_cpcsp\n";
    foreach ($constants as $name => $value) {
        if (is_string($value)) {
            $content .= "const $name = '$value';\n";
        } else {
            $content .= "const $name = $value;\n";
        }
    }
    $content .= "\n";
    
    return $content;
}

function generateStubClass($className, $classData) {
    $content = '';
    
    $classDescription = getClassDescription($className);
    if (!empty($classDescription)) {
        $content .= "/**\n";
        $content .= " * " . $classDescription . "\n";
        $content .= " */\n";
    }
    
    $content .= "class $className {\n";
    
    foreach ($classData['methods'] as $methodName => $methodData) {
        $methodDescription = getMethodDescription($className, $methodName);
        
        if (empty($methodDescription)) {
            $methodDescription = getPropertyDescription($className, $methodName);
        }

        $mapReturnType = getReturnTypeFromMap($className, $methodName);
        
        $hasParams = !empty($methodData['params']);
        $hasReturn = !empty($methodData['return']) || !empty($mapReturnType);
        $hasDescription = !empty($methodDescription);
        
        if ($hasDescription || $hasParams || $hasReturn) {
            $content .= "    /**\n";
            
            if ($hasDescription) {
                $content .= "     * " . $methodDescription . "\n";
                if ($hasParams || $hasReturn) {
                    $content .= "     *\n";
                }
            }
            
            $paramDescriptions = getParameterDescriptions($className, $methodName);

            foreach ($methodData['params'] as $param) {
                $paramName = $param['name'];
                $paramDescription = isset($paramDescriptions[$paramName]) ? ' ' . $paramDescriptions[$paramName] : '';
                $content .= "     * @param {$param['type']} \${$paramName}{$paramDescription}\n";
            }
            
            if ($hasReturn) {
                $returnType = !empty($mapReturnType) ? $mapReturnType : $methodData['return'];
                $content .= "     * @return {$returnType}\n";
            }
            
            $content .= "     */\n";
        }
        
        $params = [];
        foreach ($methodData['params'] as $param) {
            $paramStr = $param['type'] . ' $' . $param['name'];
            if ($param['optional']) {
                $paramStr .= ' = null';
            }
            $params[] = $paramStr;
        }
        
        $paramStr = implode(', ', $params);
        $returnType = $methodData['return'];

        if ($returnType === '') {
            $content .= "    public function $methodName($paramStr) {}\n";
        } else {
            $content .= "    public function $methodName($paramStr): $returnType {}\n";
        }
        $content .= "    \n";
    }
    
    $content .= "}\n\n";
    
    return $content;
}

function generateStubFile($classes, $constants, $outputFile) {
    $stubContent = generateStubHeader();
    $stubContent .= generateStubConstants($constants);
    
    foreach ($classes as $className => $classData) {
        $stubContent .= generateStubClass($className, $classData);
    }
    
    file_put_contents($outputFile, $stubContent);
    echo "✅ Финальный файл stubs сохранен: $outputFile\n";
}

// Основная логика
echo "=== Генератор PHP stubs для PHPCades ===\n";

// Путь по умолчанию для исходников PHPCades
$defaultSrcDir = '/opt/cprocsp/src/phpcades';
$srcDir = $argc >= 2 ? $argv[1] : $defaultSrcDir;

echo "Рабочая папка: " . __DIR__ . "\n";

// Проверка наличия исходников PHPCades
echo "--- Проверка исходников PHPCades ---\n";
if (is_dir($srcDir)) {
    echo "✅ Исходники PHPCades найдены: $srcDir\n";
} else {
    echo "❌ Исходники не найдены в $srcDir\n";
    echo "Убедитесь, что пакет cprocsp установлен и исходники доступны\n";
    if ($argc < 2) {
        echo "Или укажите путь к исходникам: php generate_stubs.php <путь_к_исходникам>\n";
    }
    exit(1);
}

// Генерация PHP stubs
echo "--- Генерация PHP stubs ---\n";
echo "Источник: $srcDir\n";

$classes = parsePhpCadesClasses($srcDir);
echo "\nНайдено классов: " . count($classes) . "\n";

// Парсинг констант
echo "--- Парсинг констант ---\n";
$constants = parseConstants($srcDir);
echo "Найдено констант: " . count($constants) . "\n";

// Загрузка карты документации
echo "--- Загрузка карты документации ---\n";
$jsonFile = 'phpcades_documentation_map.json';
loadDocumentationMap($jsonFile);

$outputFile = 'php_cpcsp-phpcades-stubs.php';
generateStubFile($classes, $constants, $outputFile);

// Проверка создания финального файла
if (!file_exists($outputFile)) {
    echo "❌ Ошибка: файл $outputFile не был создан\n";
    exit(1);
}

echo "✅ Генерация завершена успешно\n\n";

// Показ результата
echo "=== РЕЗУЛЬТАТ ===\n";
echo "Файл stubs: " . __DIR__ . "/$outputFile\n\n";

echo "Размер файла:\n";
$fileSize = filesize($outputFile);
$fileSizeFormatted = number_format($fileSize / 1024, 1) . 'K';
echo sprintf("%-30s %8s %s\n", 
    date('M d H:i', filemtime($outputFile)), 
    $fileSizeFormatted, 
    $outputFile
);

echo "\nПервые 10 строк:\n";
$lines = file($outputFile, FILE_IGNORE_NEW_LINES);
for ($i = 0; $i < min(10, count($lines)); $i++) {
    echo $lines[$i] . "\n";
}

echo "\n✅ Генерация stubs завершена успешно!\n";
?>
