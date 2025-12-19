-- Скрипт для проверки созданных индексов
-- Выполните этот скрипт в MySQL/MariaDB для проверки оптимизации

USE moodle; -- замените на имя вашей базы данных

-- Проверка индексов в таблице local_cdo_ok
SELECT 
    'local_cdo_ok' AS table_name,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columns,
    INDEX_TYPE,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'mdl_local_cdo_ok'
GROUP BY INDEX_NAME, INDEX_TYPE, NON_UNIQUE
ORDER BY INDEX_NAME;

-- Проверка индексов в таблице local_cdo_ok_answer
SELECT 
    'local_cdo_ok_answer' AS table_name,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columns,
    INDEX_TYPE,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'mdl_local_cdo_ok_answer'
GROUP BY INDEX_NAME, INDEX_TYPE, NON_UNIQUE
ORDER BY INDEX_NAME;

-- Проверка индексов в таблице local_cdo_ok_active_group
SELECT 
    'local_cdo_ok_active_group' AS table_name,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columns,
    INDEX_TYPE,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'mdl_local_cdo_ok_active_group'
GROUP BY INDEX_NAME, INDEX_TYPE, NON_UNIQUE
ORDER BY INDEX_NAME;

-- Проверка индексов в таблице local_cdo_ok_confirm_answers
SELECT 
    'local_cdo_ok_confirm_answers' AS table_name,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columns,
    INDEX_TYPE,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'mdl_local_cdo_ok_confirm_answers'
GROUP BY INDEX_NAME, INDEX_TYPE, NON_UNIQUE
ORDER BY INDEX_NAME;

-- Статистика по таблицам
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)',
    ROUND((INDEX_LENGTH / 1024 / 1024), 2) AS 'Indexes Size (MB)',
    ROUND((DATA_LENGTH / 1024 / 1024), 2) AS 'Data Size (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME LIKE 'mdl_local_cdo_ok%'
ORDER BY TABLE_NAME;

-- Проверка эффективности индексов (запустите после использования системы)
SELECT 
    OBJECT_SCHEMA,
    OBJECT_NAME,
    INDEX_NAME,
    COUNT_STAR AS 'Rows Scanned',
    COUNT_READ AS 'Rows Read',
    COUNT_FETCH AS 'Rows Fetched'
FROM performance_schema.table_io_waits_summary_by_index_usage
WHERE OBJECT_SCHEMA = DATABASE()
    AND OBJECT_NAME LIKE 'mdl_local_cdo_ok%'
ORDER BY OBJECT_NAME, COUNT_STAR DESC;






