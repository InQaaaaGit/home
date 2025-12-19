import {getSettings, getEducationPrograms, getEducationProgram} from './get'
import {
    putDisciplineFiles,
    putDisciplineNotes,
    putProgramFile,
    putProgramFileDescription,
    putProgramLink
} from './update'
import {deleteDisciplineFile, deleteProgramFile, deleteProgramLink} from './delete'

export const api = {
    get: {
        getSettings,
        getEducationPrograms,
        getEducationProgram
    },
    update: {
        putProgramFile,
        putProgramLink,
        putDisciplineNotes,
        putDisciplineFiles,
        putProgramFileDescription,
    },
    delete: {
        deleteProgramFile,
        deleteProgramLink,
        deleteDisciplineFile,
    }
}
