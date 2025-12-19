#!/bin/bash
###############################################################################
# Автоматизированный workflow для исправления completion
#
# Этот скрипт проводит полный цикл:
# 1. Создание backup
# 2. Анализ проблем
# 3. Сброс и пересчет completion
#
# Использование:
#   ./fix_completion_workflow.sh COURSE_ID [USER_IDS]
#
# Примеры:
#   ./fix_completion_workflow.sh 123                  # Все пользователи
#   ./fix_completion_workflow.sh 123 "45,67,89"      # Конкретные пользователи
#
###############################################################################

set -e  # Выход при ошибке

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Функция для вывода заголовков
print_header() {
    echo -e "\n${BLUE}═══════════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"
}

# Функция для вывода информации
print_info() {
    echo -e "${GREEN}ℹ $1${NC}"
}

# Функция для вывода предупреждений
print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Функция для вывода ошибок
print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Функция для подтверждения действия
confirm() {
    read -p "$(echo -e ${YELLOW}$1 [y/N]: ${NC})" response
    case "$response" in
        [yY][eE][sS]|[yY]) 
            return 0
            ;;
        *)
            return 1
            ;;
    esac
}

# Проверка параметров
if [ $# -lt 1 ]; then
    print_error "Необходимо указать COURSE_ID"
    echo "Использование: $0 COURSE_ID [USER_IDS]"
    echo "Примеры:"
    echo "  $0 123              # Все пользователи курса"
    echo "  $0 123 \"45,67,89\"   # Конкретные пользователи"
    exit 1
fi

COURSE_ID=$1
USER_IDS=${2:-""}
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="/tmp/completion_backup_course${COURSE_ID}_${TIMESTAMP}.json"
REPORT_FILE="/tmp/completion_report_course${COURSE_ID}_${TIMESTAMP}.csv"

# Получаем директорию скрипта (completion_tools/)
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

print_header "COMPLETION FIX WORKFLOW"
print_info "Course ID: $COURSE_ID"
if [ -n "$USER_IDS" ]; then
    print_info "User IDs: $USER_IDS"
else
    print_info "Обработка: ВСЕ пользователи курса"
fi
print_info "Timestamp: $TIMESTAMP"

###############################################################################
# ШАГ 1: BACKUP
###############################################################################

print_header "ШАГ 1: Создание резервной копии"

if confirm "Создать backup completion данных?"; then
    print_info "Создание backup в $BACKUP_FILE..."
    
    if [ -n "$USER_IDS" ]; then
        php "$SCRIPT_DIR/backup_completion.php" --courseid="$COURSE_ID" --userids="$USER_IDS" --output="$BACKUP_FILE"
    else
        php "$SCRIPT_DIR/backup_completion.php" --courseid="$COURSE_ID" --output="$BACKUP_FILE"
    fi
    
    if [ -f "$BACKUP_FILE" ]; then
        print_info "✓ Backup создан: $BACKUP_FILE"
        BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
        print_info "  Размер: $BACKUP_SIZE"
    else
        print_error "Не удалось создать backup"
        exit 1
    fi
else
    print_warning "Backup пропущен. РЕКОМЕНДУЕТСЯ создать backup вручную!"
fi

###############################################################################
# ШАГ 2: АНАЛИЗ
###############################################################################

print_header "ШАГ 2: Анализ проблем с completion"

print_info "Запуск анализа..."

if [ -n "$USER_IDS" ]; then
    php "$SCRIPT_DIR/analyze_completion.php" --courseid="$COURSE_ID" --userids="$USER_IDS" --export="$REPORT_FILE" --verbose
else
    php "$SCRIPT_DIR/analyze_completion.php" --courseid="$COURSE_ID" --export="$REPORT_FILE" --verbose
fi

if [ -f "$REPORT_FILE" ]; then
    print_info "✓ Отчет создан: $REPORT_FILE"
    
    # Подсчитываем количество проблем (минус заголовок)
    ISSUES_COUNT=$(($(wc -l < "$REPORT_FILE") - 1))
    
    if [ $ISSUES_COUNT -le 0 ]; then
        print_info "Проблем не обнаружено! Ничего исправлять не нужно."
        exit 0
    fi
    
    print_warning "Обнаружено проблем: $ISSUES_COUNT"
    
    if confirm "Просмотреть отчет в CSV?"; then
        if command -v column &> /dev/null; then
            head -20 "$REPORT_FILE" | column -t -s','
            if [ $ISSUES_COUNT -gt 19 ]; then
                echo "... (показаны первые 19 записей из $ISSUES_COUNT)"
            fi
        else
            head -20 "$REPORT_FILE"
        fi
    fi
else
    print_info "Отчет не создан (возможно, проблем не обнаружено)"
fi

###############################################################################
# ШАГ 3: СБРОС (DRY-RUN)
###############################################################################

print_header "ШАГ 3: Предпросмотр изменений (DRY-RUN)"

if confirm "Запустить предпросмотр изменений?"; then
    print_info "Запуск в режиме dry-run..."
    
    if [ -n "$USER_IDS" ]; then
        php "$SCRIPT_DIR/reset_completion.php" --courseid="$COURSE_ID" --userids="$USER_IDS" --dry-run --verbose
    else
        php "$SCRIPT_DIR/reset_completion.php" --courseid="$COURSE_ID" --all --dry-run --verbose
    fi
else
    print_warning "Предпросмотр пропущен"
fi

###############################################################################
# ШАГ 4: ПРИМЕНЕНИЕ ИЗМЕНЕНИЙ
###############################################################################

print_header "ШАГ 4: Применение изменений"

print_warning "ВНИМАНИЕ! Сейчас будут применены изменения к базе данных."
print_warning "Убедитесь что вы создали backup!"

if confirm "Применить изменения и пересчитать completion?"; then
    print_info "Применение изменений..."
    
    if [ -n "$USER_IDS" ]; then
        php "$SCRIPT_DIR/reset_completion.php" --courseid="$COURSE_ID" --userids="$USER_IDS" --recalculate --verbose
    else
        php "$SCRIPT_DIR/reset_completion.php" --courseid="$COURSE_ID" --all --recalculate --verbose
    fi
    
    print_info "✓ Изменения применены"
else
    print_warning "Изменения НЕ применены"
    print_info "Для ручного применения используйте:"
    if [ -n "$USER_IDS" ]; then
        echo "  php $SCRIPT_DIR/reset_completion.php --courseid=$COURSE_ID --userids=\"$USER_IDS\" --recalculate"
    else
        echo "  php $SCRIPT_DIR/reset_completion.php --courseid=$COURSE_ID --all --recalculate"
    fi
    exit 0
fi

###############################################################################
# ШАГ 5: ПРОВЕРКА
###############################################################################

print_header "ШАГ 5: Проверка результатов"

if confirm "Запустить повторный анализ для проверки?"; then
    print_info "Запуск анализа..."
    
    VERIFY_REPORT="/tmp/completion_verify_course${COURSE_ID}_${TIMESTAMP}.csv"
    
    if [ -n "$USER_IDS" ]; then
        php "$SCRIPT_DIR/analyze_completion.php" --courseid="$COURSE_ID" --userids="$USER_IDS" --export="$VERIFY_REPORT"
    else
        php "$SCRIPT_DIR/analyze_completion.php" --courseid="$COURSE_ID" --export="$VERIFY_REPORT"
    fi
    
    if [ -f "$VERIFY_REPORT" ]; then
        REMAINING_ISSUES=$(($(wc -l < "$VERIFY_REPORT") - 1))
        
        if [ $REMAINING_ISSUES -le 0 ]; then
            print_info "✓ Все проблемы исправлены!"
        else
            print_warning "Осталось проблем: $REMAINING_ISSUES"
            print_info "Отчет: $VERIFY_REPORT"
        fi
    fi
fi

###############################################################################
# ЗАВЕРШЕНИЕ
###############################################################################

print_header "ЗАВЕРШЕНО"

print_info "Созданные файлы:"
[ -f "$BACKUP_FILE" ] && echo "  - Backup: $BACKUP_FILE"
[ -f "$REPORT_FILE" ] && echo "  - Отчет (до): $REPORT_FILE"
[ -f "$VERIFY_REPORT" ] && echo "  - Отчет (после): $VERIFY_REPORT"

print_info "\nРекомендации:"
echo "  1. Проверьте результаты в интерфейсе Moodle"
echo "  2. Сохраните backup файл на случай необходимости восстановления"
echo "  3. При необходимости восстановления используйте:"
echo "     php $SCRIPT_DIR/restore_completion.php --input=\"$BACKUP_FILE\""

print_info "\nГотово!"

