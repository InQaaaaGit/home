# Исправление ошибок в языковых файлах

## Проблема

```
PHP Warning: Undefined variable $a in /app/local/cdo_vkr/lang/en/local_cdo_vkr.php on line 11
PHP Warning: Undefined variable $a in /app/local/cdo_vkr/lang/ru/local_cdo_vkr.php on line 11
```

## Причина

В языковых файлах использовались **двойные кавычки** `"..."` вместо **одинарных** `'...'`.

В PHP внутри двойных кавычек переменная `{$a}` пытается интерпретироваться как PHP-переменная, но переменной `$a` не существует в контексте загрузки языкового файла.

В Moodle языковых файлах **всегда** нужно использовать одинарные кавычки, чтобы `{$a}` оставались как текст и затем обрабатывались функцией `get_string()`.

## Исправление

### Было (неправильно):

```php
$string['not_found_files'] = "В указанном ВКР файлов не найдено.";
$string['successful_deleting_file'] = "Успешно удалено {$a} файлов ВКР";
$string['not_created_mega_pro_record'] = "Данные для таблицы MOBJECT или DOC не созданы";
```

### Стало (правильно):

```php
$string['not_found_files'] = 'В указанном ВКР файлов не найдено.';
$string['successful_deleting_file'] = 'Успешно удалено {$a} файлов ВКР';
$string['not_created_mega_pro_record'] = 'Данные для таблицы MOBJECT или DOC не созданы';
```

## Что изменено

✅ `lang/en/local_cdo_vkr.php` - строки 10-12  
✅ `lang/ru/local_cdo_vkr.php` - строки 10-12  

Двойные кавычки заменены на одинарные.

## Следующий шаг

**Очистите кеш Moodle:**

### Вариант 1: Через веб-интерфейс
```
Администрирование → Разработка → Очистить кеш → "Очистить весь кеш"
```

### Вариант 2: Через Docker CLI
```bash
docker exec -it <имя_контейнера> php admin/cli/purge_caches.php
```

## Результат

После очистки кеша ошибки `PHP Warning: Undefined variable $a` исчезнут.

---

## Правило для всех языковых файлов Moodle

**❌ НЕ используйте двойные кавычки:**
```php
$string['key'] = "Text with {$a} variable";  // НЕПРАВИЛЬНО!
```

**✅ Используйте одинарные кавычки:**
```php
$string['key'] = 'Text with {$a} variable';  // ПРАВИЛЬНО!
```

**Исключение:** Двойные кавычки можно использовать, если нет placeholders типа `{$a}`:
```php
$string['key'] = "Simple text without variables";  // Допустимо, но не рекомендуется
$string['key'] = 'Simple text without variables';  // Предпочтительно
```

**Best practice:** Всегда используйте одинарные кавычки в языковых файлах!

