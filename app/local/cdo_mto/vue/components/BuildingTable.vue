<script setup>
import {ref, onMounted, computed} from "vue";
import {useMTOStore} from "../store/mto"; // хранилище для загрузки данных

const storeMTO = useMTOStore(); // Инициализируем хранилище

const buildings = ref([]);
const loading = ref(true);
const expandedDetails = ref([]);

const translations = {
  building_address:   "Адрес (местоположение) здания, строения, сооружения, помещения, территории: ",
  building_docsanit:  "Реквизиты санитарно-эпидемиологического заключения о соответствии санитарным правилам зданий, строений, сооружений, помещений, оборудования и иного имущества, необходимых для осуществления образовательной деятельности: ",
  building_docfire:   "Реквизиты заключения о соответствии объекта защиты обязательным требованиям пожарной безопасности при осуществлении образовательной деятельности (в случае если соискателем лицензии (лицензиатом) является образовательная организация): ",
  building_owner:     "Полное наименование собственника (арендодателя, ссудодателя) объекта недвижимого имущества: ",
  building_cadastre:  "Кадастровый (или условный) номер объекта недвижимости: ",
  building_usagedoc:  "Документ - основание возникновения права (указываются реквизиты и сроки действия): ",
  building_usagetype: "Собственность или оперативное управление, хозяйственное ведение, аренда (субаренда), безвозмездное пользование: ",
  building_registry:  "Номер записи регистрации в Едином государственном реестре недвижимости: ",
  building_purpose:   "Назначение зданий, строений, сооружений, помещений и территорий с указанием площади (кв. м): ",
  building_4disabled: "Условия для получения образования лицами с ОВЗ: ",
};

// Функция для загрузки данных
const loadData = async () => {
  try {
    loading.value = true;
    const response = await storeMTO.getStructureInfo("building");

    if (response.error) {
      buildings.value = [];
    } else {
      buildings.value = response.data.building || [];
      expandedDetails.value = Array(buildings.value.length).fill(false); // Инициализируем состояние
    }
  } catch (error) {
    console.error("Ошибка загрузки данных:", error);
    buildings.value = [];
  } finally {
    loading.value = false; // Завершаем загрузку
  }
};

// Метод для переключения отображения деталей
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
const loadingModal = ref(false);
const editing      = ref({});

const buildingCharacteristics = ref({
  building_address: '',
  building_docsanit: '',
  building_docfire: '',
  building_owner: '',
  building_usagedoc: '',
  building_cadastre: '',
  building_usagetype: '',
  building_registry: '',
  building_purpose: '',
  building_4disabled: '',
});

const openEditModal = (mod, item) => {
  mode.value = mod;
  loadingModal.value = false;
  errors.value = {};
  if (Object.keys(item).length === 0) {
    for (const key in buildingCharacteristics.value) {
      buildingCharacteristics.value[key] = '';
    }
    currentItem.value = {};
  } else {
    currentItem.value = { ...item.element };
    for (const key in buildingCharacteristics.value) {
      buildingCharacteristics.value[key] =
          item.element_characteristics[key]?.value || '';
    }
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
          methodname = 'local_cdo_mto_patch_building':
          methodname = 'local_cdo_mto_add_building';

      const patch = await storeMTO.postBuilding(methodname, currentItem.value, buildingCharacteristics.value);

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

const deleteItem = async (building) => {
  if (confirm(`Вы уверены, что хотите удалить здание "${building.element.name}"?`)) {
    const index = buildings.value.findIndex(
        (b) => b.element.uid === building.element.uid
    );

    if (index !== -1) {
      try {
        showToast(`Удаление здания "${building.element.name}"`, "info");
        const patch = await storeMTO.deleteBuilding(building.element);

        if (patch.error) {
          console.error("Ошибка удаления::: ", patch);
          showToast(`Ошибка удаления здания "${building.element.name}"!`, "danger");
        } else {
          buildings.value.splice(index, 1);
          showToast(`Здание "${building.element.name}" успешно удалено`, "success");
        }
      } catch (error) {
        console.error("Ошибка удаления: ", error);
        showToast("Произошла ошибка при удалении здания!", "danger");
      }
    } else {
      console.warn(`Здание с UID: ${building.element.uid} не найдено`);
      showToast(`Здание "${building.element.name}" не найдено!`, "warning");
    }
  }
};

const showToast = (message, type = "success") => {
  const toastId = `toast-${Date.now()}`;
  const toastHTML = `
    <div id="${toastId}" class="alert alert-block alert-${type} alert-dismissible show" role="alert" aria-live="assertive" aria-atomic="true">
          ${message}
          <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Отклонить это уведомление</span>
        </button>
    </div>
  `;

  const toastContainer = document.getElementById("toasts");
  toastContainer.insertAdjacentHTML("beforeend", toastHTML);

  setTimeout(() => {
    const toastElement = document.getElementById(toastId);
    if (toastElement) toastElement.remove();
  }, 9000);
};

onMounted(loadData);
</script>

<template>
  <div>
    <div class="toast-container"> <div id="toasts"></div> </div>
    <div v-if="loading" class="text-center my-3">
      <div class="spinner-border text-primary" role="status"></div>
    </div>
    <table v-else class="table border-bottom">
      <tbody>
      <tr v-for="(building, index) in buildings" :key="building.element.uid">
        <td style="width: 50px;"> <small>{{ index +1 }}</small> </td>
        <td  v-if="filteredCharacteristics(building.element_characteristics).length > 0">
          <span
              class="cursor-pointer text-primary"  style="cursor: pointer"
              @click="toggleDetails(index)"
          >
              <b>{{ building.element.name }}</b>
            </span>
          <div v-show="expandedDetails[index]" class="detail p-4">
            <ul class="list-group">
              <li
                  v-for="([key, value]) in filteredCharacteristics(building.element_characteristics)"
                  :key="key"
                  class="list-group-item"
              >
                <div><small><strong><u>{{ translateKey(key) }}</u></strong></small></div>
                <div>{{ value.value }}</div>
              </li>
            </ul>
          </div>
        </td>
        <td v-else><b>{{ building.element.name }}</b></td>

        <td class="text-right" style="min-width: 110px;">
          <button class="edit-room btn btn-link" data-toggle="modal" data-target="#modal_edit_building" @click="openEditModal('edit', building)">
            <i class="fas fa-pencil-alt"></i>
          </button>
          <button class="delete-building btn btn-link" @click="deleteItem(building)">
            <i class="fas fa-times"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>
    <div class="row">
      <div class="col-6">
      </div>
      <div class="col-6">
        <div class="text-right">
          <button  data-toggle="modal" data-target="#modal_edit_building"
                   class="btn btn-primary"
                   @click="openEditModal('add', {})"
          >
            Добавить корпус
          </button>
        </div>
      </div>
    </div>

    <!-- Модальное окно -->

    <div  id="modal_edit_building"
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
                data-dismiss="modal" data-target="#modal_edit_building"
            ></button>
          </div>
          <div class="modal-body" v-if="!loadingModal">

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
              <label for="buildingsAddress" class="form-label">{{ translations.building_address }}</label>
              <textarea
                  id="buildingsAddress"
                  v-model="buildingCharacteristics.building_address"
                  rows="3"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_docsanit" class="form-label" >{{translations.building_docsanit}}</label>
              <textarea
                  id="building_docsanit"
                  v-model="buildingCharacteristics.building_docsanit"
                  rows="3"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_docfire" class="form-label" >{{translations.building_docfire}}</label>
              <textarea
                  id="building_docfire"
                  v-model="buildingCharacteristics.building_docfire"
                  rows="3"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_owner" class="form-label" >{{translations.building_owner}}</label>
              <textarea
                  id="building_owner"
                  v-model="buildingCharacteristics.building_owner"
                  rows="1"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_cadastre" class="form-label" >{{translations.building_cadastre}}</label>
              <textarea
                  id="building_cadastre"
                  v-model="buildingCharacteristics.building_cadastre"
                  rows="1"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_usagedoc" class="form-label" >{{translations.building_usagedoc}}</label>
              <textarea
                  id="building_usagedoc"
                  v-model="buildingCharacteristics.building_usagedoc"
                  rows="3"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_usagetype" class="form-label" >{{translations.building_usagetype}}</label>
              <textarea
                  id="building_usagetype"
                  v-model="buildingCharacteristics.building_usagetype"
                  rows="1"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_registry" class="form-label" >{{translations.building_registry}}</label>
              <textarea
                  id="building_registry"
                  v-model="buildingCharacteristics.building_registry"
                  rows="1"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_purpose" class="form-label" >{{translations.building_purpose}}</label>
              <textarea
                  id="building_purpose"
                  v-model="buildingCharacteristics.building_purpose"
                  rows="3"
                  class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="building_4disabled" class="form-label" >{{translations.building_4disabled}}</label>
              <textarea
                  id="building_4disabled"
                  v-model="buildingCharacteristics.building_4disabled"
                  rows="1"
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
