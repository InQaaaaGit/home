export function validateSurveyForm(formData) {
    const errors = {};

    // Список уровней образования, для которых нужно скрывать поля документа об образовании
    const hideDocumentLevels = ['1', '2', '3', '4']; // ID уровней от дошкольного до среднего общего
    const shouldHideEducationDocFields = hideDocumentLevels.includes(formData.education_level);

    // Telephone
    if (!formData.telephone) {
        errors.telephone = 'Поле Телефон обязательно для заполнения.';
    } else if (!/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/.test(formData.telephone)) {
        errors.telephone = 'Неверный формат телефона.';
    }

    // Middle Name
    if (!formData.middleName) {
        errors.middleName = 'Поле Отчество обязательно для заполнения.';
    }

    // Citizenship
    if (!formData.citizenship) {
        errors.citizenship = 'Поле Гражданство обязательно для заполнения.';
    }

    // SNILS
    if (formData.citizenship === 'Россия' && !formData.snils) {
        errors.snils = 'Поле СНИЛС обязательно для заполнения для граждан России.';
    } else if (formData.snils && !/^\d{3}-\d{3}-\d{3} \d{2}$/.test(formData.snils)) {
        errors.snils = 'Неверный формат СНИЛС.';
    }

    //INN - делаем необязательным полем
    if (formData.inn && !/^\d{12}$/.test(formData.inn)) {
        errors.inn = 'Неверный формат ИНН.';
    }

    // Sex - No validation needed as it has a default value

    // Birthday
    if (!formData.birthday) {
        errors.birthday = 'Поле Дата рождения обязательно для заполнения.';
    }

    // serial
    if (!formData.serial) {
        errors.serial = 'Поле Серия паспорта обязательно для заполнения.';
    }
    // number
    if (!formData.number) {
        errors.number = 'Поле Номер паспорта обязательно для заполнения.';
    }
    // whenIssued
    if (!formData.whenIssued) {
        errors.whenIssued = 'Поле Когда выдан паспорт обязательно для заполнения.';
    }
    // whoIssued
    if (!formData.whoIssued) {
        errors.whoIssued = 'Поле Кем выдан паспорт обязательно для заполнения.';
    }
    // education_level
    if (!formData.education_level) {
        errors.education_level = 'Поле Уровень образования обязательно для заполнения.';
    }
    // Валидируем поля документа об образовании только если они не скрыты
    if (!shouldHideEducationDocFields) {
        // educationType
        if (!formData.educationType) {
            errors.educationType = 'Поле Тип образования обязательно для заполнения.';
        }
        // serialEdu
        if (!formData.serialEdu) {
            errors.serialEdu = 'Поле Серия документа об образовании обязательно для заполнения.';
        }
        // numberEdu
        if (!formData.numberEdu) {
            errors.numberEdu = 'Поле Номер документа об образовании обязательно для заполнения.';
        }
        // whenIssuedEdu
        if (!formData.whenIssuedEdu) {
            errors.whenIssuedEdu = 'Поле Когда выдан документ об образовании обязательно для заполнения.';
        }
        // whoIssuedEdu
        if (!formData.whoIssuedEdu) {
            errors.whoIssuedEdu = 'Поле Кем выдан документ об образовании обязательно для заполнения.';
        }
    }
    // addressOfRegistration
    if (!formData.addressOfRegistration) {
        errors.addressOfRegistration = 'Поле Адрес регистрации обязательно для заполнения.';
    }
    // addressOfActualResidence
    if (!formData.addressOfActualResidence) {
        errors.addressOfActualResidence = 'Поле Адрес фактического проживания обязательно для заполнения.';
    }

    // personalData
    if (!formData.personalData) {
        errors.personalData = 'Необходимо согласие на обработку персональных данных.';
    }

    if (!formData.informationAboutPayer) {
        errors.informationAboutPayer = 'Поле Плательщик обязательно для заполнения.';
    } else {
        // Validate nested fields based on informationAboutPayer
        if (formData.informationAboutPayer === 'legalEntity') {
            validateLegalEntity(formData, errors);
        } else if (formData.informationAboutPayer === 'AnotherPayer') {
            validateIndividual(formData, errors);
        }
    }
    return errors;
}

function validateLegalEntity(formData, errors) {
    if (!formData.fullnameLE) {
        errors.fullnameLE = 'Поле Полное наименование обязательно для заполнения.';
    }
    if (!formData.shortnameLE) {
        errors.shortnameLE = 'Поле Сокращенное наименование обязательно для заполнения.';
    }
    if (!formData.legalAddress) {
        errors.legalAddress = 'Поле Юридический адрес обязательно для заполнения.';
    }
    if (!formData.postalAddress) {
        errors.postalAddress = 'Поле Почтовый адрес обязательно для заполнения.';
    }
    if (!formData.telephoneLE) { // Corrected field name
        errors.telephoneLE = 'Поле Телефон обязательно для заполнения.';
    } else if (!/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/.test(formData.telephoneLE)) { // Use telephoneLE
        errors.telephoneLE = 'Неверный формат телефона.';
    }
    if (!formData.innLE) { // Corrected field name
        errors.innLE = 'Поле ИНН обязательно для заполнения.';
    } else if (!/^\d{10,12}$/.test(formData.innLE)) { // Use innLE
        errors.innLE = 'Неверный формат ИНН (10 или 12 цифр).';
    }
    if (!formData.kpp) {
        errors.kpp = 'Поле КПП обязательно для заполнения.';
    } else if (!/^\d{9}$/.test(formData.kpp)) {
        errors.kpp = 'Неверный формат КПП (9 цифр).';
    }
    if (!formData.bankDetails) {
        errors.bankDetails = 'Поле Банковские реквизиты обязательно для заполнения.';
    }
    if (!formData.accountNumber) {
        errors.accountNumber = 'Поле Номер счета обязательно для заполнения.';
    } else if (!/^\d{20}$/.test(formData.accountNumber)) {
        errors.accountNumber = 'Неверный формат номера счета (20 цифр).';
    }
    if (!formData.bik) {
        errors.bik = 'Поле БИК обязательно для заполнения.';
    } else if (!/^\d{9}$/.test(formData.bik.replace(/\D/g, ''))) {
        errors.bik = 'Неверный формат БИК (9 цифр).';
    }
    return errors; // Explicitly return errors
}

function validateIndividual(formData, errors) {
    if (!formData.individual) {
        formData.individual = {};
    }
    if (!errors.individual) {
        errors.individual = {};
    }
    if (!formData.individual.fullName) {
        errors.individual.fullName = 'Поле ФИО обязательно для заполнения.';
    }
    if (!formData.individual.citizenship) {
        errors.individual.citizenship = 'Поле Гражданство обязательно для заполнения.';
    }
    if (!formData.individual.birthDate) {
        errors.individual.birthDate = 'Поле Дата рождения обязательно для заполнения.';
    }
    if (!formData.individual.passportSeries) {
        errors.individual.passportSeries = 'Поле Серия паспорта обязательно для заполнения.';
    }
    if (!formData.individual.passportNumber) {
        errors.individual.passportNumber = 'Поле Номер паспорта обязательно для заполнения.';
    }
    if (!formData.individual.passportIssuedBy) {
        errors.individual.passportIssuedBy = 'Поле Кем выдан паспорт обязательно для заполнения.';
    }
    if (!formData.individual.passportIssuedDate) {
        errors.individual.passportIssuedDate = 'Поле Когда выдан паспорт обязательно для заполнения.';
    }
    if (!formData.individual.registrationAddress) {
        errors.individual.registrationAddress = 'Поле Адрес по прописке обязательно для заполнения.';
    }
    if (!formData.individual.residentialAddress) {
        errors.individual.residentialAddress = 'Поле Адрес местожительства обязательно для заполнения.';
    }
    if (formData.individual.inn && !/^\d{12}$/.test(formData.individual.inn)) {
        errors.individual.inn = 'Неверный формат ИНН.';
    }
    if (formData.individual.citizenship === 'Россия' && !formData.individual.snils) {
        errors.individual.snils = 'Поле СНИЛС обязательно для заполнения для граждан России.';
    } else if (formData.individual.snils && !/^\d{3}-\d{3}-\d{3} \d{2}$/.test(formData.individual.snils)) {
        errors.individual.snils = 'Неверный формат СНИЛС.';
    }
    if (!formData.individual.phoneNumber) {
        errors.individual.phoneNumber = 'Поле Телефон обязательно для заполнения.';
    } else if (!/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/.test(formData.individual.phoneNumber)) {
        errors.individual.phoneNumber = 'Неверный формат телефона.';
    }
    if (!formData.individual.email) {
        errors.individual.email = 'Поле Email обязательно для заполнения.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.individual.email)) {
        errors.individual.email = 'Неверный формат email.';
    }
    return errors; // Explicitly return errors
}
