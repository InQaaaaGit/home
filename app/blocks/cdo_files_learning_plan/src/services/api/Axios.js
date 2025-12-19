import axios from "axios";

const host = window.location.protocol + '//' + window.location.hostname
const API = '/blocks/cdo_files_learning_plan';

const Axios = axios.create({
    baseURL: host + API,
})

export default Axios;