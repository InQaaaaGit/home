#!/bin/bash

# Скрипт для тестирования отправки еженедельных отчетов
# Usage: ./test_report.sh <user_id> [date_from] [date_to]

# Определяем путь к Moodle
MOODLE_DIR="/home/inq/projects/moodle4container/app"
SCRIPT_PATH="${MOODLE_DIR}/local/cdo_ag_tools/cli/send_weekly_quiz_report.php"

# Проверяем наличие user_id
if [ -z "$1" ]; then
    echo "Ошибка: укажите ID пользователя"
    echo "Usage: $0 <user_id> [date_from] [date_to]"
    echo ""
    echo "Examples:"
    echo "  $0 123                           # Текущая неделя"
    echo "  $0 123 2025-10-20 2025-10-26    # Указанный период"
    exit 1
fi

USER_ID="$1"

# Формируем команду
CMD="php ${SCRIPT_PATH} --userid=${USER_ID}"

# Добавляем опциональные параметры даты
if [ -n "$2" ] && [ -n "$3" ]; then
    CMD="${CMD} --datefrom=$2 --dateto=$3"
fi

# Выводим команду для отладки
echo "Выполняется команда:"
echo "$CMD"
echo ""
echo "========================================="
echo ""

# Выполняем команду
eval "$CMD"

# Сохраняем код возврата
EXIT_CODE=$?

echo ""
echo "========================================="
echo "Код завершения: $EXIT_CODE"

exit $EXIT_CODE

