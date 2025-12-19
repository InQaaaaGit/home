# Completion Tools для Moodle

Набор инструментов для диагностики, сброса и восстановления completion (завершения курсов) в Moodle.

## Проблема

У некоторых пользователей выставился статус completion по элементам курсов, хотя они не выполняли условия этих элементов.

## Быстрый старт

### ⭐ Умный пересчет (рекомендуется для исправления ошибок)

Обрабатывает **только ошибочные** completion записи, корректные оставляет нетронутыми:

```bash
# Для одного курса
php fix_invalid_completion.php --courseid=COURSE_ID --dry-run --verbose
php fix_invalid_completion.php --courseid=COURSE_ID --verbose

# Для всех курсов категории (рекурсивно, включая подкатегории)
php fix_invalid_completion_by_category.php --categoryid=CAT_ID --dry-run
php fix_invalid_completion_by_category.php --categoryid=CAT_ID --verbose
```

### Автоматический workflow (для комплексной обработки)

```bash
./fix_completion_workflow.sh COURSE_ID
```

### Ручной режим

```bash
# 1. Анализ проблемы
php analyze_completion.php --courseid=COURSE_ID --verbose

# 2. Создание backup
php backup_completion.php --courseid=COURSE_ID

# 3. Умный пересчет (только ошибочные)
php fix_invalid_completion.php --courseid=COURSE_ID --verbose

# ИЛИ полный сброс и пересчет всех
php reset_completion.php --courseid=COURSE_ID --userids=USER_IDS --recalculate
```

## Доступные инструменты

| Скрипт | Описание |
|--------|----------|
| `fix_invalid_completion.php` ⭐ | **Умный пересчет** - обрабатывает только ошибочные записи (1 курс) |
| `fix_invalid_completion_by_category.php` ⭐ | **Умный пересчет по категории** - рекурсивно для всех курсов |
| `analyze_completion.php` | Анализ и выявление проблем с completion |
| `show_user_completion.php` | Просмотр completion конкретного пользователя |
| `reset_completion.php` | Полный сброс и пересчет completion данных |
| `backup_completion.php` | Создание резервной копии данных |
| `restore_completion.php` | Восстановление данных из backup |
| `fix_completion_workflow.sh` | Автоматизированный workflow |

## Документация

📚 **Начните здесь:**
- [`docs/COMPLETION_README_RU.md`](docs/COMPLETION_README_RU.md) - Полное руководство на русском
- [`docs/COMPLETION_QUICK_START.txt`](docs/COMPLETION_QUICK_START.txt) - Быстрая шпаргалка с командами

📖 **Дополнительно:**
- [`docs/COMPLETION_TOOLS_README.txt`](docs/COMPLETION_TOOLS_README.txt) - Подробная документация
- [`docs/COMPLETION_TOOLS_INDEX.txt`](docs/COMPLETION_TOOLS_INDEX.txt) - Индекс всех инструментов

## Справка

Для любого скрипта доступна встроенная справка:

```bash
php fix_invalid_completion.php --help
php analyze_completion.php --help
php reset_completion.php --help
php show_user_completion.php --help
php backup_completion.php --help
php restore_completion.php --help
```

## Примеры использования

### ⭐ Умный пересчет - обработка только ошибочных completion (РЕКОМЕНДУЕТСЯ)

**Для одного курса:**
```bash
# Просмотр что будет исправлено (dry-run)
php fix_invalid_completion.php --courseid=123 --dry-run --verbose

# Применение исправлений для всех пользователей курса
php fix_invalid_completion.php --courseid=123 --verbose

# Применение исправлений для конкретных пользователей
php fix_invalid_completion.php --courseid=123 --userids=45,67,89 --verbose
```

**Для всех курсов категории (рекурсивно):**
```bash
# Просмотр что будет исправлено во всех курсах категории
php fix_invalid_completion_by_category.php --categoryid=5 --dry-run

# Применение исправлений для всех курсов (включая подкатегории)
php fix_invalid_completion_by_category.php --categoryid=5 --verbose

# Только прямые курсы категории (без подкатегорий)
php fix_invalid_completion_by_category.php --categoryid=5 --no-recursive --verbose
```

### Анализ проблем в курсе

```bash
php analyze_completion.php --courseid=123
```

### Экспорт проблем в CSV

```bash
php analyze_completion.php --courseid=123 --export=/tmp/problems.csv
```

### Просмотр информации о пользователе

```bash
php show_user_completion.php --courseid=123 --userid=45
```

### Создание backup перед изменениями

```bash
php backup_completion.php --courseid=123
```

### Сброс completion (с предпросмотром)

```bash
# Сначала просмотр
php reset_completion.php --courseid=123 --userids=45,67,89 --dry-run -v

# Затем применение
php reset_completion.php --courseid=123 --userids=45,67,89 --recalculate
```

### Восстановление из backup

```bash
php restore_completion.php --input=/tmp/backup.json
```

## Типы обнаруживаемых проблем

- ❌ **Not Viewed** - элемент не просмотрен (требовался просмотр)
- ❌ **No Grade** - нет оценки (требовалась оценка)
- ❌ **Grade Below Pass** - оценка ниже проходного балла
- ❌ **No Submission** - работа не отправлена
- ❌ **No Quiz Attempts** - нет попыток прохождения теста
- ❌ **Insufficient Posts** - недостаточно сообщений на форуме
- ❌ **Invalid Pass/Fail State** - некорректный статус сдачи

## ⚠️ Важно

1. **Всегда создавайте backup** перед изменениями
2. **Используйте --dry-run** для предпросмотра изменений
3. **Начинайте с analyze_completion.php** для выявления проблем
4. **Проверяйте результаты** в интерфейсе Moodle после применения

## Структура проекта

```
completion_tools/
├── README.md                      # Этот файл
├── analyze_completion.php         # Анализ проблем
├── reset_completion.php           # Сброс completion
├── show_user_completion.php       # Просмотр пользователя
├── backup_completion.php          # Создание backup
├── restore_completion.php         # Восстановление backup
├── fix_completion_workflow.sh     # Автоматизированный workflow
└── docs/                          # Документация
    ├── COMPLETION_README_RU.md    # Полное руководство
    ├── COMPLETION_QUICK_START.txt # Быстрая шпаргалка
    ├── COMPLETION_TOOLS_README.txt # Подробная документация
    └── COMPLETION_TOOLS_INDEX.txt # Индекс инструментов
```

## Требования

- Moodle 4.0+
- PHP 8.1+
- CLI доступ к серверу
- Права администратора Moodle

## Лицензия

GNU GPL v3 or later

---

**Создано для:** local_cdo_ag_tools plugin  
**Версия:** 1.0  
**Дата:** 2025-12-15

