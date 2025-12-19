import Axios from "./Axios";

const resource = '/update.php';

const prepareFormData = (objectData) => {
    return Object.keys(objectData).reduce((formData, key) => {
        let value = objectData[key];
        if (typeof value === 'boolean')
            formData.append(key, Number(value));
        else if (Array.isArray(value))
            value.forEach((item) => {
                formData.append(`${key}[]`, item);
            })
        else if (value === null)
            formData.append(key, '');
        else
            formData.append(key, value);
        return formData;
    }, new FormData())
}

const deleteProgramFile = (data) => {
    let form_data = prepareFormData(data);
    form_data.append('update_mode', 'delete_file_program');
    return Axios.post(resource, form_data)
}

const deleteProgramLink = (data) => {
    let form_data = prepareFormData(data);
    form_data.append('update_mode', 'delete_link');
    return Axios.post(resource, form_data)
}

const deleteDisciplineFile = (data) => {
    let form_data = prepareFormData(data);
    form_data.append('update_mode', 'delete_file');
    return Axios.post(resource, form_data)
}

export {
    deleteProgramFile,
    deleteProgramLink,
    deleteDisciplineFile,
}