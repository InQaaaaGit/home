<script setup>
import {ref, onMounted} from "vue";
import {useMTOStore} from "../store/mto";

const storeMTO = useMTOStore();
const programs = ref([]);
const editing  = ref({});
const loading = ref(false);
const loadingModal    = ref(false);
const expandedDetails = ref([]);

const loadData = async () => {
  try {
    loading.value = true;
    const response = await storeMTO.getStructureInfo("edu_program");

    if (response.error) {
      programs.value = [];
    } else {
      programs.value = response || [];
      expandedDetails.value = Array(programs.value.length).fill(false);
    }
  } catch (error) {
    console.error("Ошибка загрузки данных:", error);
    programs.value = [];
  } finally {
    loading.value = false;
  }
};

const errors = ref({});
const showModal   = ref(false);
const currentItem = ref({});
const openEditModal = (item) => {
  loadingModal.value = false;
  errors.value = {};
  currentItem.value = { ...item };
  showModal.value = true;
};

const validateForm = () => {
  errors.value = {};

  if (!currentItem.value.Specialty) {
    errors.value.Specialty = 'Поле "Специальность" обязательно для заполнения';
  }

  if (!currentItem.value.Guid_Specialty) {
    errors.value.Guid_Specialty = 'Поле "GUID Специальности" обязательно для заполнения';
  }

  if (currentItem.value.Guid_Profile && !currentItem.value.Profile) {
    errors.value.Profile = 'Поле "Образовательная программа" обязательно для заполнения, если отсутствует GUID';
  }

  // Возвращаем true, если нет ошибок
  return Object.keys(errors.value).length === 0;
};

const saveChanges = async () => {
  if (validateForm()) {
    try {
      editing.value = {};
      loadingModal.value = true;
      const patch = await storeMTO.patchEducationProgram(currentItem.value);

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
          <th>Специальность</th>
          <th>Образовательная программа</th>
          <th style="width: 120px;">Год набора</th>
          <th style="width: 120px;">Реализуемая</th>
          <th style="width: 50px;"></th>
        </tr>
      </thead>
      <tbody>
      <tr v-for="(program, index) in programs" :key="index">
        <td> <small>{{ index +1 }}</small> </td>
        <td><b>{{ program.Specialty }}</b></td>
        <td>{{ program.Profile }}</td>
        <td>{{ program.YearSet }}</td>
        <td>
          <span v-if="program.Realize">Да</span>
          <span v-else>Нет</span>
        </td>
        <td class="text-right">
          <button class="edit-room btn btn-link" data-toggle="modal" data-target="#modal_edit_program" @click="openEditModal(program)">
            <i class="fas fa-pencil-alt"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- Модальное окно -->

    <div  id="modal_edit_program"
        class="modal"
        tabindex="-1"
        aria-hidden="true"
        v-if="showModal"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Образовательная программа</h4>
            <button
                type="button"
                class="btn-close"
                aria-label="Close"
                @click="showModal = false"
                data-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body"  v-if="!loadingModal">
            <input v-model="currentItem.Guid_Specialty" type="hidden" />
            <input v-model="currentItem.Guid_Profile" type="hidden" />
            <div class="mb-3">
              <label for="disciplineSpeciality" class="form-label">Специальность: *</label>
              <input
                  id="disciplineSpeciality"
                  v-model="currentItem.Specialty"
                  type="text"
                  class="form-control"
              />
              <small v-if="errors.Specialty" class="text-danger">{{ errors.Specialty }}</small>
            </div>
            <div class="mb-3">
              <label for="disciplineProfile" class="form-label">Образовательная программа: <span v-if="currentItem.Guid_Profile">*</span></label>
              <input
                  id="disciplineProfile"
                  v-model="currentItem.Profile"
                  type="text"
                  class="form-control"
                  :disabled="currentItem.Guid_Profile!==''?false:true"
              />
              <small v-if="errors.Profile" class="text-danger">{{ errors.Profile }}</small>
            </div>


            <div class="mb-3">
              <label for="disciplineYearSet" class="form-label" >Год набора:</label>
              <input  id="disciplineYearSet"
                      v-model="currentItem.YearSet"
                      type="text"
                      class="form-control" disabled
              />
            </div>
            <div class="mb-3">
              <label class="form-label" >Реализуемая:</label>
              <input type="text" class="form-control" disabled v-if="currentItem.Realize" value="Да" />
              <input type="text" class="form-control" disabled v-else value="Нет" />
            </div>
          </div>
          <div class="modal-body text-center" v-else >
             <div v-if="!editing.message" class="spinner-border text-primary" role="status"></div>
              <div v-else :class="editing.styles">{{editing.message}}</div>
          </div>
          <div class="modal-footer">
            <div><small v-if="errors.Guid_Specialty" class="text-danger">{{ errors.Guid_Specialty }}</small></div>
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
