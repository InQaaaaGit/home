<script setup>
import {ref, onMounted} from "vue";
import {useMTOStore}    from "../store/mto";

const storeMTO       = useMTOStore();
const disciplines    = ref([]);
const loading        = ref(true);
const showModal      = ref(false);
const currentItem    = ref({});
const loadingModal   = ref(false);
const errors         = ref({});
const editing        = ref({});


const loadData = async () => {
  try {
    const response = await storeMTO.getStructureInfo("discipline");
    if (response.error) {
      disciplines.value = [];
    } else {
      const programs = response.data.education_program || [];
      disciplines.value = programs.flatMap(program => {
        const {
          uid: program_uid,
          name: program_name,
          academic_year,
          speciality,
          specialisation,
          discipline
        } = program;

        if (!discipline || discipline.length === 0) return [];

        return discipline.map(d => ({
          disciplines_uid: d.uid,
          disciplines_name: d.name,
          specialisation_uid: specialisation?.uid || "",
          specialisation_name: specialisation?.name || "",
          speciality_uid: speciality?.uid || "",
          speciality_name: speciality?.name || "",
          academic_year_uid: academic_year?.uid || "",
          academic_year_name: academic_year?.name || "",
          program_uid,
          program_name,
          room: d.room || []
        }));
      });
    }
  } catch (error) {
    console.error("Ошибка загрузки данных:", error);
    disciplines.value = [];
  } finally {
    loading.value = false;
  }
};



const openEditModal  = (item) => {
  loadingModal.value = false;
  errors.value       = {};
  currentItem.value  = { ...item };
  showModal.value    = true;
};

const validateForm = () => {
  errors.value = {};

  if (!currentItem.value.disciplines_name) {
    errors.value.disciplines_name = 'Поле "Название" обязательно для заполнения';
  }
  if (!currentItem.value.disciplines_uid) {
    errors.value.disciplines_uid = 'Поле "uid" обязательно для заполнения';
  }

  // Возвращаем true, если нет ошибок
  return Object.keys(errors.value).length === 0;
};


const saveChanges = async () => {
  if (validateForm()) {
    try {
      editing.value = {};
      loadingModal.value = true;

      const patch = await storeMTO.patchDiscipline(currentItem.value);

      await loadData();

      if (patch.error) {
        console.error(patch);
        editing.value = {
          styles: 'alert alert-danger alert-block',
          message: `Ошибка сохранения данных!`
        }
      }
      else {
        editing.value = {
          styles: 'alert alert-success alert-block',
          message: `Данные сохранены`
        }
      }

    } catch (error) {
      console.error("Ошибка сохранения данных:", error);
      editing.value = {
        styles: 'alert alert-danger alert-block',
        message: `Ошибка сохранения данных!`
      }
    }

    finally { }

  } else {
    console.log('Ошибки в форме:', errors.value);
  }
};

onMounted(loadData);
</script>

<template>
  <div>
    <div v-if="loading" class="text-center my-3">
      <div class="spinner-border text-primary" role="status"></div>
    </div>
    <table v-else class="table border-bottom">
      <thead>
      <tr>
        <th style="width: 50px;">#</th>
        <th>Наименование дисциплины (модуля) в соответствии с учебным планом</th>
        <th>Образовательная программа</th>
        <th>Период обучения</th>
        <th style="width: 50px;"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(discipline, index) in disciplines" :key="discipline.uid">
        <td> <small>{{ index +1 }}</small> </td>
        <td>
          <b>{{ discipline.disciplines_name }}</b> <br>
          <small v-if="discipline.speciality_name">{{ discipline.speciality_name }}</small>
          <small v-if="discipline.speciality_name && discipline.specialisation_name">/</small>
          <small v-if="discipline.specialisation_name">{{ discipline.speciality_name }}</small>
        </td>
        <td>{{ discipline.program_name }}</td>
        <td>{{ discipline.academic_year_name }}</td>
        <td class="text-right" style="min-width: 110px;">
          <button class="edit-room btn btn-link" data-toggle="modal" data-target="#modal_edit_discipline" @click="openEditModal(discipline)">
            <i class="fas fa-pencil-alt"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>


    <!-- Модальное окно -->

    <div  id="modal_edit_discipline"
          class="modal"
          tabindex="-1"
          aria-hidden="true"
          v-show="showModal"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Дисциплина</h4>
            <button
                type="button"
                class="btn-close"
                aria-label="Close"
                @click="showModal = false"
                data-dismiss="modal" data-target="#modal_edit_discipline"
            ></button>
          </div>
          <div class="modal-body" v-if="!loadingModal">
            <input v-model="currentItem.disciplines_uid" type="hidden" />
            <div class="mb-3">
              <label for="disciplineName" class="form-label">Название: *</label>
              <textarea
                  id="disciplineName"
                  v-model="currentItem.disciplines_name"
                  rows="1"
                  class="form-control">
                </textarea>
              <small v-if="errors.disciplines_name" class="text-danger">{{ errors.name }}</small>
            </div>
            <div class="mb-3">
              <label for="disciplineAcademicYearName" class="form-label">Период обучения:</label>
              <input  id="disciplineAcademicYearName"
                      type="text"
                      class="form-control" disabled
                      v-model="currentItem.academic_year_name"
              />
            </div>
            <div class="mb-3">
              <label for="disciplineSpecialityName" class="form-label">Специальность:</label>
              <input  id="disciplineSpecialityName"
                      type="text"
                      class="form-control" disabled
                      v-model="currentItem.speciality_name"
              />
            </div>
            <div class="mb-3">
              <label for="disciplineSpecialityName" class="form-label">Специализация:</label>
              <input  id="disciplineSpecialityName"
                      type="text"
                      class="form-control" disabled
                      v-model="currentItem.specialisation_name"
              />
            </div>
            <div class="mb-3">
              <label for="disciplineSpecialisationName" class="form-label">Образовательная программа:</label>
              <input  id="disciplineSpecialisationName"
                      type="text"
                      class="form-control" disabled
                      v-model="currentItem.program_name"
              />
            </div>
          </div>
          <div class="modal-body text-center" v-else >
            <div v-if="!editing.message" class="spinner-border text-primary" role="status"></div>
            <div v-else :class="editing.styles">{{editing.message}}</div>
          </div>
          <div class="modal-footer">
            <div><small v-if="errors.disciplines_uid" class="text-danger">{{ errors.disciplines_uid }}</small></div>
            <button type="button" class="btn btn-primary" @click="saveChanges()" v-if="!loadingModal">
              Сохранить изменения
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно -->



  </div>
</template>
