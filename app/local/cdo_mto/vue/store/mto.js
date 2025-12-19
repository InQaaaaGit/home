import { defineStore } from 'pinia';
import * as moodleAjax from 'core/ajax';

export const useMTOStore = defineStore('MTO', {
    state: () => ({
        buildingInfo: {
            error: '',
            data: []
        },
    }),
    actions: {
        async getStructureInfo(structureType) {
            let args = {
                mode: "table",
                structure_type: structureType,
            };

            if (structureType === 'discipline') {
                args.mode = 'education_program';
            }

            const request = {
                methodname: 'local_cdo_mto_get_'+structureType+'_info',
                args
            };

            try {
                return await moodleAjax.call([request])[0];
            }
            catch (error) {
                console.error(`Ошибка получения данных для ${structureType} data:`, error);
                return { error: `Ошибка получения данных для ${structureType}` };
            }
        },

        async patchEducationProgram(currentItem) {
            let args = {
                specialty_guid:   currentItem.Guid_Specialty,
                newnamespecialty: currentItem.Specialty,
                ...(currentItem.Guid_Profile && { profile_guid: currentItem.Guid_Profile }),
                ...(currentItem.Profile && { newnameprofile: currentItem.Profile }),
            };

            const request = {
                methodname: 'local_cdo_mto_patch_education_program',
                args
            };

            try {
                return await moodleAjax.call([request])[0];
            }
            catch (error) {
                console.error(`Ошибка передачи данных:`, error);
                return { message: `Ошибка передачи данных`, error: error };
            }
        },

        async patchDiscipline(currentItem) {
            let args = {
                name: currentItem.disciplines_name,
                uid: currentItem.disciplines_uid
            };

            const request = {
                methodname: 'local_cdo_mto_patch_discipline',
                args
            };

            try {
                return await moodleAjax.call([request])[0];
            }
            catch (error) {
                console.error(`Ошибка передачи данных:`, error);
                return { message: `Ошибка передачи данных`, error: error };
            }
        },

        async postBuilding(methodname, currentItem, building) {
            let args = {
                object_name:   currentItem.name,
                ...(currentItem.uid && { object_uid: currentItem.uid }),
                ...(building.building_address && { building_address: building.building_address }),
                ...(building.building_docsanit && { building_docsanit: building.building_docsanit }),
                ...(building.building_docfire && { building_docfire: building.building_docfire }),
                ...(building.building_owner && { building_owner: building.building_owner }),
                ...(building.building_usagedoc && { building_usagedoc: building.building_usagedoc }),
                ...(building.building_cadastre && { building_cadastre: building.building_cadastre }),
                ...(building.building_usagetype && { building_usagetype: building.building_usagetype }),
                ...(building.building_registry && { building_registry: building.building_registry }),
                ...(building.building_purpose && { building_purpose: building.building_purpose }),
                ...(building.building_4disabled && { building_4disabled: building.building_4disabled }),
            };

            const request = {
                methodname,
                args
            };

            try {
                return await moodleAjax.call([request])[0];
            }
            catch (error) {
                return { message: `Ошибка передачи данных`, error: error };
            }
        },

        async postRoom(methodname, currentItem, parent,  room) {

            let args = {
                object_name:  currentItem.name,
                building_uid: parent.uid,
                ...(currentItem.uid && { object_uid: currentItem.uid }),
                ...(room.room_capacity && { room_capacity: room.room_capacity }),
                ...(room.room_area && { room_area: room.room_area }),
                ...(room.room_number && { room_number: room.room_number }),
                ...(room.room_technumber && { room_technumber: room.room_technumber }),
                ...(room.room_description && { room_description: room.room_description }),
            };

            const request = {
                methodname,
                args
            };

            try {
                return await moodleAjax.call([request])[0];
            }
            catch (error) {
                return { message: `Ошибка передачи данных`, error: error };
            }
        },

        async deleteBuilding(building) {
            let args = { object_uid:   building.uid };

            const request = {
                methodname: 'local_cdo_mto_del_building',
                args
            };

            try {
                return await moodleAjax.call([request])[0];
            }
            catch (error) {
                return { message: `Ошибка передачи данных`, error: error };
            }
        },
    },
});
