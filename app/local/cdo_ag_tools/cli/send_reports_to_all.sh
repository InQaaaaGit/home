#!/bin/bash

# Скрипт для массовой отправки еженедельных отчетов всем пользователям с оценками
# Usage: ./send_reports_to_all.sh [date_from] [date_to]

# Определяем путь к Moodle
MOODLE_DIR="/home/inq/projects/moodle4container/app"
SCRIPT_DIR="${MOODLE_DIR}/local/cdo_ag_tools/cli"

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Проверяем наличие скриптов
if [ ! -f "${SCRIPT_DIR}/list_users_with_quiz_grades.php" ]; then
    echo -e "${RED}Ошибка: файл list_users_with_quiz_grades.php не найден${NC}"
    exit 1
fi

if [ ! -f "${SCRIPT_DIR}/send_weekly_quiz_report.php" ]; then
    echo -e "${RED}Ошибка: файл send_weekly_quiz_report.php не найден${NC}"
    exit 1
fi

# Формируем команду для получения списка пользователей
LIST_CMD="php ${SCRIPT_DIR}/list_users_with_quiz_grades.php"

# Добавляем опциональные параметры даты
if [ -n "$1" ] && [ -n "$2" ]; then
    DATE_FROM="$1"
    DATE_TO="$2"
    LIST_CMD="${LIST_CMD} --datefrom=${DATE_FROM} --dateto=${DATE_TO}"
    SEND_PARAMS="--datefrom=${DATE_FROM} --dateto=${DATE_TO}"
    echo -e "${YELLOW}Период: с ${DATE_FROM} по ${DATE_TO}${NC}"
else
    SEND_PARAMS=""
    echo -e "${YELLOW}Период: текущая неделя${NC}"
fi

echo "========================================="
echo "Массовая рассылка еженедельных отчетов"
echo "========================================="
echo ""

# Получаем список пользователей
echo "Получение списка пользователей..."
USER_IDS=$(${LIST_CMD} 2>/dev/null | tail -n +4 | head -n -3 | awk '{print $1}' | grep -E '^[0-9]+$')

if [ -z "$USER_IDS" ]; then
    echo -e "${YELLOW}Нет пользователей с оценками за указанный период${NC}"
    exit 0
fi

# Подсчитываем количество пользователей
USER_COUNT=$(echo "$USER_IDS" | wc -l)
echo -e "${GREEN}Найдено пользователей: ${USER_COUNT}${NC}"
echo ""

# Запрашиваем подтверждение
echo -e "${YELLOW}Вы уверены, что хотите отправить отчеты ${USER_COUNT} пользователям?${NC}"
read -p "Продолжить? (y/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Отменено пользователем"
    exit 0
fi

echo ""
echo "Начинаем отправку..."
echo "========================================="
echo ""

# Счетчики
SUCCESS_COUNT=0
FAIL_COUNT=0
CURRENT=0

# Отправляем отчеты
while IFS= read -r userid; do
    CURRENT=$((CURRENT + 1))
    echo -e "${YELLOW}[${CURRENT}/${USER_COUNT}]${NC} Отправка отчета пользователю ID: ${userid}"
    
    # Формируем команду отправки
    SEND_CMD="php ${SCRIPT_DIR}/send_weekly_quiz_report.php --userid=${userid} ${SEND_PARAMS}"
    
    # Выполняем команду и захватываем вывод
    OUTPUT=$(${SEND_CMD} 2>&1)
    EXIT_CODE=$?
    
    if [ $EXIT_CODE -eq 0 ]; then
        SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
        echo -e "${GREEN}  ✓ Успешно${NC}"
    else
        FAIL_COUNT=$((FAIL_COUNT + 1))
        echo -e "${RED}  ✗ Ошибка (код: ${EXIT_CODE})${NC}"
        
        # Выводим первые 2 строки ошибки для диагностики
        echo "$OUTPUT" | head -n 2 | sed 's/^/    /'
    fi
    
    echo ""
    
    # Небольшая задержка между отправками, чтобы не перегружать систему
    sleep 0.5
    
done <<< "$USER_IDS"

# Итоговая статистика
echo "========================================="
echo "Отправка завершена"
echo "========================================="
echo ""
echo -e "Всего пользователей: ${USER_COUNT}"
echo -e "${GREEN}Успешно отправлено: ${SUCCESS_COUNT}${NC}"

if [ $FAIL_COUNT -gt 0 ]; then
    echo -e "${RED}Ошибок: ${FAIL_COUNT}${NC}"
else
    echo -e "Ошибок: ${FAIL_COUNT}"
fi

echo ""

# Возвращаем код ошибки, если были неудачи
if [ $FAIL_COUNT -gt 0 ]; then
    exit 1
else
    exit 0
fi

