<script setup>
import {ref, onMounted, computed} from "vue";
import {useMTOStore} from "../store/mto";

const storeMTO = useMTOStore();

const parents = ref([]);
const rooms   = ref([]);
const loading = ref(true);
const expandedDetails = ref([]);

const translations = {
  room_capacity:    "Количество посадочных мест",
  room_area:        "Площадь помещения",
  room_special:     "Специализация",
  room_number:      "Номер аудитории",
  room_technumber:  "Технический номер помещения",
  room_description: "Описание",
  room_equipment:   "Оборудование",
  room_type:        "Назначение",
};

// Функция для загрузки данных
const loadData = async () => {
  try {
    const response = await storeMTO.getStructureInfo("room");

    if (response.error) {
      rooms.value = [];
    } else {
      rooms.value   = response.data.room || [];
      parents.value = transformRoomsToParents(rooms.value);
      expandedDetails.value = Array(rooms.value.length).fill(false);
    }
  } catch (error) {
    console.error("Ошибка загрузки данных:", error);
    rooms.value = [];
  } finally {
    loading.value = false; // Завершаем загрузку
  }
};

function transformRoomsToParents(data) {
  const parentsMap = {};

  data.forEach((roomData) => {
    const { parent, element, element_characteristics } = roomData;
    const parentUid = parent.uid;

    if (!parentsMap[parentUid]) {
      parentsMap[parentUid] = {
          name: parent.name,
          uid: parent.uid,
          code: parent.code,
          rooms: []
      };
    }

    parentsMap[parentUid].rooms.push({
      name: element.name,
      uid: element.uid,
      code: element.code,
      element_characteristics
    });
  });
  return Object.values(parentsMap);
}

const toggleDetails = (index) => {
  expandedDetails.value[index] = !expandedDetails.value[index];
};

const translateKey = (key) => translations[key] || key;

const filteredCharacteristics = computed(() => {
  return (characteristics) =>
      Object.entries(characteristics).filter(
          ([key, value]) => value.value && value.value.trim() !== ""
      );
});

const mode         = ref('add');
const errors       = ref({});
const showModal    = ref(false);
const currentItem  = ref({});
const parentItem   = ref({});
const loadingModal = ref(false);
const editing      = ref({});

const roomCharacteristics = ref({
  room_capacity: '',
  room_area: '',
  room_number: '',
  room_technumber: '',
  room_description: ''
});

const openEditModal = (mod, item, parent_uid = null) => {
  mode.value = mod;
  loadingModal.value = false;
  errors.value = {};
  if (Object.keys(item).length === 0) {
    for (const key in roomCharacteristics.value) {
      roomCharacteristics.value[key] = '';
    }
    currentItem.value = {};
  } else {
    currentItem.value = { ...item };
    for (const key in roomCharacteristics.value) {
      roomCharacteristics.value[key] =
          item.element_characteristics[key]?.value || '';
    }
  }

  if(parent_uid) {
    parentItem.value.uid = parent_uid;
  }
  showModal.value = true;
};

const validateForm = () => {
  errors.value = {};

  if (!currentItem.value.name) {
    errors.value.name = 'Поле "Название" обязательно для заполнения';
  }
  if (!currentItem.value.uid && mode.value === 'edit') {
    errors.value.uid = 'Поле "uid" обязательно для заполнения';
  }

  if (!parentItem.value.uid) {
    errors.value.parent = 'Поле "GUID Корпуса" обязательно для заполнения';
  }

  // Возвращаем true, если нет ошибок
  return Object.keys(errors.value).length === 0;
};

const saveChanges = async () => {
  if (validateForm()) {
    try {
      editing.value = {};
      loadingModal.value = true;
      let methodname;
      mode.value === 'edit'?
          methodname = 'local_cdo_mto_patch_room':
          methodname = 'local_cdo_mto_add_room';

      const patch = await storeMTO.postRoom(methodname, currentItem.value, parentItem.value, roomCharacteristics.value);

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

const deleteItem = async (room) => {
  if (confirm(`Вы уверены, что хотите удалить аудиторию "${room.name}"?`)) {
    const index = rooms.value.findIndex(
        (b) => b.element.uid === room.uid
    );

    if (index !== -1) {
      try {
        showToast(`Удаление аудитории "${room.name}"`, "info");
        const patch = await storeMTO.deleteBuilding(room);
        // const patch = true;

        if (patch.error) {
          console.error("Ошибка удаления::: ", patch);
          showToast(`Ошибка удаления аудитории "${room.name}"!`, "danger");
        } else {
          rooms.value.splice(index, 1);
          parents.value = transformRoomsToParents(rooms.value);
          showToast(`Аудитория "${room.name}" успешно удалена`, "success");
        }
      } catch (error) {
        console.error("Ошибка удаления: ", error);
        showToast("Произошла ошибка при удалении аудитории!", "danger");
      }
    } else {
      console.warn(`Аудитория с UID: ${room.uid} не найдено`);
      showToast(`Аудитория "${room.name}" не найдена!`, "warning");
    }
  }
};

const showToast = (message, type = "success") => {
  const toastId = `room-toast-${Date.now()}`;
  const toastHTML = `
    <div id="${toastId}" class="alert alert-block alert-${type} alert-dismissible show" role="alert" aria-live="assertive" aria-atomic="true">
          ${message}
          <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Отклонить это уведомление</span>
        </button>
    </div>
  `;

  const toastContainer = document.getElementById("room_toasts");
  toastContainer.insertAdjacentHTML("beforeend", toastHTML);

  setTimeout(() => {
    const toastElement = document.getElementById(toastId);
    if (toastElement) toastElement.remove();
  }, 5000);
};

onMounted(loadData);
</script>

<template>
  <div>
    <div class="toast-container"> <div id="room_toasts"></div> </div>
    <div v-if="loading" class="text-center my-3">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div v-else>
      <div
          v-for="(parent, index) in parents"
          :key="parent.uid"
          class="mb-2"
      >
        <div class="row">
          <div class="col cursor-pointer text-primary" style="cursor: pointer" @click="toggleDetails(parent.uid)">
            <b class="mx-3">{{ parent.name }}</b>
          </div>
          <div class="col text-right py-2">
            <button  data-toggle="modal" data-target="#modal_edit_room"
                     class="btn btn-primary"
                     @click="openEditModal('add', {}, parent.uid)"
            >
              Добавить помещение сюда
            </button> &nbsp;
          </div>
        </div>

        <hr class="m-0" v-show="!expandedDetails[parent.uid]">

        <div class="accordion-body" v-show="expandedDetails[parent.uid]">

          <table class="table border-bottom">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th>Название</th>
                <th class="text-right">{{translations.room_capacity}}</th>
                <th class="text-right">{{translations.room_area}}</th>
                <th class="text-right">{{translations.room_number}}</th>
                <th class="text-right">{{translations.room_technumber}}</th>
                <th>{{translations.room_description}}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <tr v-for="(room, index) in parent.rooms" :key="room.uid">
              <td> <small>{{ index +1 }}</small> </td>
              <td> {{ room.name }} </td>
              <td class="text-right px-4"> {{ room.element_characteristics.room_capacity.value }} </td>
              <td class="text-right px-4"> {{ room.element_characteristics.room_area.value }} </td>
              <td class="text-right px-4"> {{ room.element_characteristics.room_number.value }} </td>
              <td class="text-right px-4"> {{ room.element_characteristics.room_technumber.value }} </td>
              <td> <small>{{ room.element_characteristics.room_description.value }} </small></td>
              <td class="text-right" style="min-width: 110px;">
                <button class="edit-room btn btn-link" data-toggle="modal" data-target="#modal_edit_room" @click="openEditModal('edit', room, parent.uid)">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="btn btn-link" @click="deleteItem(room)">
                  <i class="fas fa-times"></i>
                </button>
              </td>
            </tr>
            </tbody>
          </table>

        </div>

      </div>
    </div>


    <div class="row">
      <div class="col-6">
      </div>
      <div class="col-6">
        <div class="text-right my-3">
          <button  data-toggle="modal" data-target="#modal_edit_room"
                   class="btn btn-primary"
                   @click="openEditModal('add', {})"
          >
            Добавить помещение
          </button> &nbsp;
        </div>
      </div>
    </div>

    <!-- Модальное окно -->

    <div  id="modal_edit_room"
          class="modal"
          tabindex="-1"
          aria-hidden="true"
          v-show="showModal"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button
                type="button"
                class="btn-close"
                aria-label="Close"
                @click="showModal = false"
                data-dismiss="modal" data-target="#modal_edit_room"
            ></button>
          </div>
          <div class="modal-body"  v-if="!loadingModal">

            <input v-model="currentItem.uid" type="hidden" />
            <div class="mb-3">
              <label for="BuildingName" class="form-label">Название: *</label>
              <textarea
                  id="BuildingName"
                  v-model="currentItem.name"
                  rows="1"
                  class="form-control">
              </textarea>
              <small v-if="errors.name" class="text-danger">{{ errors.name }}</small>

            </div>
            <div class="mb-3">
              <label for="parentGuid" class="form-label" >GUID Корпуса: *</label>
              <input  id="parentGuid"
                      v-model="parentItem.uid"
                      type="text"
                      class="form-control"
                      :disabled="mode === 'edit'"
              />
              <small v-if="errors.parent" class="text-danger">{{ errors.parent }}</small>
            </div>
            <div class="mb-3">
              <label for="room_capacity" class="form-label" >{{translations.room_capacity}}</label>
              <input  id="room_capacity"
                      v-model="roomCharacteristics.room_capacity"
                      type="text"
                      class="form-control"
              />
            </div>
            <div class="mb-3">
              <label for="room_area" class="form-label" >{{translations.room_area}}</label>
              <input  id="room_area"
                      v-model="roomCharacteristics.room_area"
                      type="text"
                      class="form-control"
              />
            </div>
            <div class="mb-3">
              <label for="room_number" class="form-label" >{{translations.room_number}}</label>
              <input  id="room_number"
                      v-model="roomCharacteristics.room_number"
                      type="text"
                      class="form-control"
              />
            </div>
            <div class="mb-3">
              <label for="room_technumber" class="form-label" >{{translations.room_technumber}}</label>
              <input  id="room_technumber"
                      v-model="roomCharacteristics.room_technumber"
                      type="text"
                      class="form-control"
              />
            </div>
            <div class="mb-3">
              <label for="room_description" class="form-label" >{{translations.room_description}}</label>
              <textarea
                  id="room_description"
                  v-model="roomCharacteristics.room_description"
                  rows="3"
                  class="form-control">
              </textarea>
            </div>
          </div>
          <div class="modal-body text-center" v-else >
            <div v-if="!editing.message" class="spinner-border text-primary" role="status"></div>
            <div v-else :class="editing.styles">{{editing.message}}</div>
          </div>
          <div class="modal-footer">
            <div><small v-if="errors.uid" class="text-danger">{{ errors.uid }}</small></div>
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
