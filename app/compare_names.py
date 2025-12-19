import pandas as pd

def find_duplicate_surnames(file_path, column_name):
    # Читаем Excel файл
    df = pd.read_excel(file_path)
    
    # Проверяем, существует ли указанный столбец
    if column_name not in df.columns:
        print(f"Столбец '{column_name}' не найден в файле")
        return
    
    # Находим дубликаты фамилий
    duplicates = df[df[column_name].duplicated(keep=False)]
    
    if duplicates.empty:
        print("Дубликаты фамилий не найдены")
    else:
        print("\nНайденные дубликаты фамилий:")
        print(duplicates.sort_values(by=column_name))
        
        # Сохраняем результаты в новый Excel файл
        output_file = "duplicate_surnames.xlsx"
        duplicates.to_excel(output_file, index=False)
        print(f"\nРезультаты сохранены в файл: {output_file}")

if __name__ == "__main__":
    # Запрашиваем у пользователя путь к файлу и название столбца
    file_path = input("Введите путь к Excel файлу: ")
    column_name = input("Введите название столбца с фамилиями: ")
    
    find_duplicate_surnames(file_path, column_name) 