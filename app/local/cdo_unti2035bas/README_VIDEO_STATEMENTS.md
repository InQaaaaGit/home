# Video Statements Management

## Описание

Интерфейс управления video statements для потоков в плагине CDO UNTI2035 БАС.

## Функциональность

### Основные возможности:

1. **Получение потока по flow_id** - обязательный параметр для доступа к интерфейсу
2. **Отображение информации о потоке**:
   - ID потока (flow_id)
   - Название курса
   - Название группы
3. **Таблица пользователей потока** с следующими колонками:
   - ID пользователя
   - Полное имя (с ссылкой на профиль)
   - Email
   - Логин (username)
   - Действия для каждого пользователя

### Действия с пользователями:

- **Просмотр statements** - ссылка на просмотр video statements конкретного пользователя
- **Отправить statements** - ссылка на отправку video statements для пользователя

### Массовые действия:

- **Массовая отправка statements** - отправка statements для всех пользователей потока
- **Отчет по statements** - просмотр общего отчета по statements потока

## Использование

### URL доступа:
```
/local/cdo_unti2035bas/pages/video_statement_management.php?flow_id=<ID_ПОТОКА>
```

### Параметры:
- `flow_id` (обязательный, INT) - ID потока в системе UNTI

### Требования:
- Пользователь должен быть авторизован
- Пользователь должен иметь права `moodle/site:config`

## Интеграция

### Добавление ссылки в таблицу потоков:

В таблице потоков (`/local/cdo_unti2035bas/streams.php`) для каждого потока с непустым flow_id добавлена кнопка "Управление video statements".

### Архитектурные компоненты:

1. **Repository method**: `stream_repository::find_by_flow_id(int $flowId)`
2. **Moodle service**: используется для получения информации о курсах и группах
3. **Group functions**: `groups_get_members()` для получения участников группы

## Дизайн

Интерфейс следует единому стилю приложения с использованием:
- Responsive CSS Grid для информационных блоков
- Карточный дизайн для информации о потоке
- Стандартные Moodle таблицы для списка пользователей
- Согласованные цветовые схемы и отступы

## Локализация

Поддерживается русская и английская локализация:
- `videostatementsmanagement` - Управление video statements
- `flowinfo` - Информация о потоке
- `streamusers` - Пользователи потока
- `bulkactions` - Массовые действия
- И другие связанные строки

## Будущие расширения

Интерфейс подготовлен для интеграции со следующими страницами:
- `/pages/user_video_statements.php` - детальный просмотр statements пользователя
- `/pages/send_user_video_statements.php` - отправка statements для пользователя  
- `/pages/bulk_send_video_statements.php` - массовая отправка
- `/pages/bulk_video_statements_report.php` - отчеты

## Код

Основной файл: `pages/video_statement_management.php`
Стили: `styles.css` (секция Video Statements Management)
Локализация: `lang/ru/local_cdo_unti2035bas.php`, `lang/en/local_cdo_unti2035bas.php` 