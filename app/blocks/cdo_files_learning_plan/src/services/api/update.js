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

const putProgramFile = (data) => {
    let form_data = prepareFormData(data);
    return Axios.post(resource,
        form_data,
        {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        }
    )
}

const putProgramLink = (data) => {
    let form_data = prepareFormData(data);
    return Axios.post(resource,
        form_data
    )
}

const putDisciplineNotes = (data) => {
    let form_data = prepareFormData(data);
    return Axios.post(resource,
        form_data
    )
}

const putDisciplineFiles = (data) => {
    let form_data = prepareFormData(data);
    return Axios.post(resource,
        form_data
    )
}

const putProgramFileDescription = (data) => {
    let form_data = prepareFormData(data);
    return Axios.post(resource,
        form_data
    )
}

export {
    putProgramFile,
    putProgramLink,
    putDisciplineNotes,
    putDisciplineFiles,
    putProgramFileDescription,
}