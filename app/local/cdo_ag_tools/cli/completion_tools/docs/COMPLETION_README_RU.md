# Инструменты для исправления Completion в Moodle

## Описание проблемы

У некоторых пользователей выставился статус completion (завершения) по элементам курсов, хотя они не выполняли условия этих элементов. Это может произойти из-за:

- Ошибок в плагинах
- Сбоев при импорте/восстановлении курсов
- Некорректной миграции данных
- Багов в Moodle
- Ручного вмешательства в базу данных

## Решение

Создан набор CLI инструментов для диагностики, сброса и восстановления completion данных.

## Быстрый старт

### 1. Анализ проблемы

```bash
cd /путь/к/moodle/local/cdo_ag_tools/cli/completion_tools
php analyze_completion.php --courseid=COURSE_ID --verbose
```

Это покажет всех пользователей с некорректными completion статусами.

### 2. Создание backup (рекомендуется!)

```bash
php backup_completion.php --courseid=COURSE_ID
```

### 3. Просмотр что будет сделано

```bash
php reset_completion.php --courseid=COURSE_ID --userids=1,2,3 --dry-run --verbose
```

### 4. Применение исправлений

```bash
php reset_completion.php --courseid=COURSE_ID --userids=1,2,3 --recalculate
```

## Автоматизированный workflow

Для автоматизации всего процесса используйте:

```bash
./fix_completion_workflow.sh COURSE_ID
```

Этот скрипт выполнит все шаги интерактивно:
1. Создаст backup
2. Проанализирует проблемы
3. Покажет предпросмотр изменений
4. Применит исправления (с подтверждением)
5. Проверит результат

## Доступные инструменты

| Скрипт | Назначение |
|--------|-----------|
| `analyze_completion.php` | Анализ и выявление проблем |
| `show_user_completion.php` | Просмотр детальной информации о пользователе |
| `reset_completion.php` | Сброс и пересчет completion |
| `backup_completion.php` | Создание резервной копии |
| `restore_completion.php` | Восстановление из резервной копии |
| `fix_completion_workflow.sh` | Автоматизированный workflow |

## Типы обнаруживаемых проблем

- **Not Viewed** - требуется просмотр, но элемент не просмотрен
- **No Grade** - требуется оценка, но оценка отсутствует
- **Grade Below Pass** - оценка ниже проходного балла
- **No Submission** - задание завершено, но работа не отправлена
- **No Quiz Attempts** - тест завершен, но нет попыток
- **Insufficient Posts** - недостаточно сообщений на форуме
- **Invalid Pass/Fail State** - некорректный статус сдачи/провала

## Примеры использования

### Анализ конкретного курса

```bash
php analyze_completion.php --courseid=123
```

### Экспорт проблем в CSV для дальнейшего анализа

```bash
php analyze_completion.php --courseid=123 --export=problems.csv
```

### Просмотр информации о конкретном пользователе

```bash
php show_user_completion.php --courseid=123 --userid=45
```

### Сброс completion для нескольких пользователей

```bash
# Сначала тест
php reset_completion.php --courseid=123 --userids=45,67,89 --dry-run --verbose

# Затем применение
php reset_completion.php --courseid=123 --userids=45,67,89 --recalculate
```

### Сброс completion для всех пользователей курса

```bash
php reset_completion.php --courseid=123 --all --recalculate
```

### Создание и восстановление backup

```bash
# Создание backup
php backup_completion.php --courseid=123 --output=/tmp/my_backup.json

# Восстановление (сначала тест)
php restore_completion.php --input=/tmp/my_backup.json --dry-run

# Восстановление (применение)
php restore_completion.php --input=/tmp/my_backup.json
```

## Безопасность

⚠️ **ВАЖНО:**

1. **ВСЕГДА** создавайте backup перед массовыми изменениями
2. **ВСЕГДА** используйте `--dry-run` перед применением изменений
3. Тестируйте на небольшой группе пользователей сначала
4. Проверяйте результаты в интерфейсе Moodle после применения
5. Сохраняйте backup файлы для возможности отката

### Создание MySQL backup

Дополнительно к JSON backup рекомендуется создать MySQL backup:

```bash
mysqldump -u USER -p DATABASE_NAME \
  mdl_course_modules_completion \
  mdl_course_completions \
  > completion_backup_$(date +%Y%m%d_%H%M%S).sql
```

## Параметры командной строки

### Общие для всех скриптов

- `--help`, `-h` - Показать справку
- `--verbose`, `-v` - Подробный вывод
- `--courseid=ID`, `-c` - ID курса

### analyze_completion.php

- `--userids=IDS` - Список ID пользователей через запятую
- `--export=FILE` - Экспорт результатов в CSV
- `--show-valid` - Показывать также корректные completion

### reset_completion.php

- `--userids=IDS` - Список ID пользователей
- `--all` - Все пользователи курса
- `--cmid=ID` - ID конкретного элемента курса
- `--recalculate` - Пересчитать completion после сброса
- `--dry-run` - Режим просмотра без изменений

### show_user_completion.php

- `--userid=ID` - ID пользователя
- `--username=NAME` - Username пользователя

### backup_completion.php

- `--userids=IDS` - Список ID пользователей (по умолчанию все)
- `--output=FILE` - Путь к файлу backup
- `--format=FORMAT` - Формат: json или sql

### restore_completion.php

- `--input=FILE` - Путь к файлу backup
- `--dry-run` - Режим просмотра
- `--force` - Перезаписать существующие записи

## Документация

Для подробной информации см.:

- `COMPLETION_QUICK_START.txt` - Быстрый старт и основные команды
- `COMPLETION_TOOLS_README.txt` - Полная документация
- `COMPLETION_TOOLS_INDEX.txt` - Индекс всех инструментов

## Получение справки

Для любого скрипта:

```bash
php script_name.php --help
```

## Устранение неполадок

### Ошибка: "Completion отключен для данного курса"

Убедитесь что в курсе включено отслеживание завершения:
- Настройки курса → Отслеживание завершения → Включить отслеживание завершения

### Ошибка: "Course module ID XXX не существует"

При восстановлении из backup некоторые элементы курса могли быть удалены. Это нормально, такие записи будут пропущены.

### Скрипт работает слишком долго

Для больших курсов (> 1000 пользователей) обработка может занять время. Используйте `--verbose` для отслеживания прогресса.

### После сброса проблемы остались

1. Проверьте что использовали флаг `--recalculate`
2. Запустите встроенный Moodle cron: `php admin/cli/cron.php`
3. Проверьте логи Moodle на наличие ошибок

## Альтернативные решения

Если скрипты не помогают, рассмотрите:

1. **Встроенный CLI Moodle:**
   ```bash
   php admin/cli/completion_cron.php
   ```

2. **Сброс через интерфейс:**
   Администрирование → Курсы → Сбросить курс → Completion

3. **Обновление Moodle:**
   Проблема может быть связана с багом в старой версии

## Требования

- Moodle 4.0+
- PHP 8.1+
- CLI доступ к серверу
- Права администратора Moodle

## Лицензия

GNU GPL v3 or later

## Автор

Создано для local_cdo_ag_tools plugin

