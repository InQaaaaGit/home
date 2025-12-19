#!/bin/bash

# Путь к директории amd относительно положения скрипта
AMD_DIR="amd"

# Проверяем существование директории amd
if [ ! -d "$AMD_DIR" ]; then
    echo "Ошибка: Директория $AMD_DIR не существует!"
    exit 1
fi

# Проверяем существование поддиректорий build и src
if [ ! -d "$AMD_DIR/build" ]; then
    echo "Ошибка: Директория $AMD_DIR/build не существует!"
    exit 1
fi

if [ ! -d "$AMD_DIR/src" ]; then
    echo "Ошибка: Директория $AMD_DIR/src не существует!"
    exit 1
fi

# Очищаем существующие символические ссылки в src (опционально)
# Закомментируйте следующие 2 строки, если не хотите удалять существующие файлы
echo "Удаление существующих символических ссылок в директории src..."
find "$AMD_DIR/src" -type l -delete

echo "Создание символических ссылок..."

# Перебираем все файлы из build
for file in "$AMD_DIR/build"/*.min.js; do
    # Проверяем, что файл существует и это регулярный файл
    if [ -f "$file" ]; then
        # Получаем только имя файла без пути
        filename=$(basename "$file")
        
        # Создаем имя для ссылки, убирая .min из названия
        linkname="${filename/.min.js/.js}"
        
        # Создаем относительный путь для символической ссылки
        # (относительно src директории)
        relative_path="../build/$filename"
        
        # Создаем символическую ссылку
        ln -sf "$relative_path" "$AMD_DIR/src/$linkname"
        
        echo "Создана ссылка: $AMD_DIR/src/$linkname -> $relative_path"
    fi
done

echo "Готово!" 