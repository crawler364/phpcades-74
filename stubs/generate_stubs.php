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
 * Ищет описание свойства в карте документации (для геттеров/сеттеров)
 */
function getPropertyDescription($className, $methodName) {
    global $documentationMap;
    
    // Извлекаем имя свойства из геттера/сеттера
    $propertyName = '';
    if (strpos($methodName, 'get_') === 0) {
        $propertyName = substr($methodName, 4);
    } elseif (strpos($methodName, 'set_') === 0) {
        $propertyName = substr($methodName, 4);
    }
    
    if (!empty($propertyName) && isset($documentationMap[$className]['properties'][$propertyName]['description'])) {
        return $documentationMap[$className]['properties'][$propertyName]['description'];
    }
    
    return '';
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

function normalizeParameterName($varName, $paramType, $methodName) {
    // Убираем типовые префиксы C++
    $varName = preg_replace('/^(l|sz|str|p|n|b|dw)([A-Z])/', '$2', $varName);
    
    // Приводим к camelCase
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
            
            echo "      Формат параметров: $formatString\n";
            echo "      Строка параметров: $parametersString\n";
            
            // Извлекаем имена переменных из строки параметров
            $variableNames = [];
            if (preg_match_all('/&(\w+)/', $parametersString, $varMatches)) {
                $variableNames = $varMatches[1];
                echo "      Найденные переменные: " . implode(', ', $variableNames) . "\n";
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
                
                switch ($char) {
                    case 'l':
                        $paramType = $optional ? '?int' : 'int';
                        break;
                    case 's':
                        $paramType = $optional ? '?string' : 'string';
                        // Для строк часто есть еще параметр длины
                        $params[] = [
                            'type' => $paramType,
                            'name' => $paramName,
                            'optional' => $optional
                        ];
                        $varIndex++;
                        $paramIndex++;
                        
                        // Проверяем, есть ли параметр длины
                        if (isset($variableNames[$varIndex]) && 
                            (strpos($variableNames[$varIndex], 'len') !== false || 
                             strpos($variableNames[$varIndex], 'size') !== false)) {
                            // Параметр длины обычно не включается в PHP API
                            $varIndex++;
                        }
                        continue 2;
                    case 'z':
                        $paramType = 'mixed';
                        break;
                    case 'b':
                        $paramType = $optional ? '?bool' : 'bool';
                        break;
                    case 'd':
                        $paramType = $optional ? '?float' : 'float';
                        break;
                    case 'o':
                    case 'O':
                        $paramType = 'object';
                        break;
                    case 'a':
                        $paramType = 'array';
                        break;
                    default:
                        continue 2;
                }
                
                $params[] = [
                    'type' => $paramType,
                    'name' => $paramName,
                    'optional' => $optional
                ];
                
                $varIndex++;
                $paramIndex++;
            }
        }
    }
    
    return $params;
}

function parseReturnType($content, $methodName) {
    // Ищем блок метода
    if (preg_match("/PHP_METHOD\s*\([^,]+,\s*$methodName\s*\)[^{]*\{(.*?)(?=^PHP_METHOD|\n})/ms", $content, $methodBlock)) {
        $methodContent = $methodBlock[1];
        
        // String возвраты
        if (strpos($methodContent, 'RETURN_ATL_STRING') !== false || 
            strpos($methodContent, 'RETURN_PROXY_STRING') !== false ||
            strpos($methodContent, 'RETURN_STRINGL') !== false) {
            return 'string';
        }
        
        // Integer возвраты  
        if (strpos($methodContent, 'RETURN_LONG') !== false) {
            return 'int';
        }
        
        // Boolean возвраты
        if (strpos($methodContent, 'RETURN_TRUE') !== false || 
            strpos($methodContent, 'RETURN_FALSE') !== false) {
            return 'bool';
        }
    }
    
    return '';
}

function generateStubFile($classes, $outputFile) {
    // Создаем заголовок с комментариями и датой
    $stubContent = "<?php\n";
    $stubContent .= "/** @noinspection PhpUnused */\n";
    $stubContent .= "/** @noinspection PhpInconsistentReturnPointsInspection */\n";
    $stubContent .= "/** @noinspection PhpUndefinedClassInspection */\n\n";
    $stubContent .= "/**\n";
    $stubContent .= " * PHPCades Stubs\n";
    $stubContent .= " * Автоматически сгенерированные заглушки для PHPCades расширения\n";
    $stubContent .= " * \n";
    $stubContent .= " * Генератор: generate_stubs.php\n";
    $stubContent .= " * Дата: " . date('r') . "\n";
    $stubContent .= " */\n\n";
    $stubContent .= "\n/** @generate-class-entries */\n\n";
    
    foreach ($classes as $className => $classData) {
        // Добавляем описание класса из карты документации
        $classDescription = getClassDescription($className);
        if (!empty($classDescription)) {
            $stubContent .= "/**\n";
            $stubContent .= " * " . $classDescription . "\n";
            $stubContent .= " */\n";
        }
        
        $stubContent .= "class $className {\n";
        
        foreach ($classData['methods'] as $methodName => $methodData) {
            // Получаем описание метода из карты документации
            $methodDescription = getMethodDescription($className, $methodName);
            
            // Если описания метода нет, пробуем найти описание свойства (для геттеров/сеттеров)
            if (empty($methodDescription)) {
                $methodDescription = getPropertyDescription($className, $methodName);
            }
            
            // Генерируем PHPDoc с описанием, @param и @return
            $hasParams = !empty($methodData['params']);
            $hasReturn = !empty($methodData['return']);
            $hasDescription = !empty($methodDescription);
            
            if ($hasDescription || $hasParams || $hasReturn) {
                $stubContent .= "    /**\n";
                
                // Добавляем описание метода если есть
                if ($hasDescription) {
                    $stubContent .= "     * " . $methodDescription . "\n";
                    if ($hasParams || $hasReturn) {
                        $stubContent .= "     *\n";
                    }
                }
                
                // Добавляем @param для каждого параметра
                foreach ($methodData['params'] as $param) {
                    $stubContent .= "     * @param {$param['type']} \${$param['name']}\n";
                }
                
                // Добавляем @return если есть возвращаемое значение
                if ($hasReturn) {
                    $stubContent .= "     * @return {$methodData['return']}\n";
                }
                
                $stubContent .= "     */\n";
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
                $stubContent .= "    public function $methodName($paramStr) {}\n";
            } else {
                $stubContent .= "    public function $methodName($paramStr): $returnType {}\n";
            }
            $stubContent .= "    \n";
        }
        
        $stubContent .= "}\n\n";
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

// Загрузка карты документации
echo "--- Загрузка карты документации ---\n";
$jsonFile = 'phpcades_documentation_map.json';
loadDocumentationMap($jsonFile);

$outputFile = 'php_cpcsp-phpcades-stubs.php';
generateStubFile($classes, $outputFile);

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
