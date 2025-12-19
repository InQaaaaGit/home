import Axios from "./Axios";

const resource = '/get_data.php';

const getSettings = () => {
    return Axios.get(resource, {
        params: {
            type: 'settings'
        }
    })
}

const getEducationPrograms = (secretary = '') => {
    return Axios.get(resource, {
        params: {
            type: 'education_programs',
            secretary: secretary
        }
    })
}

const getEducationProgram = (docNumber) => {
    return Axios.get(resource, {
        params: {
            type: 'education_program',
            doc_number: docNumber,
        }
    })
}

export {
    getSettings,
    getEducationPrograms,
    getEducationProgram
}