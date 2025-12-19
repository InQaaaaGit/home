<script setup>
import {mask} from 'vue-the-mask';
import AddressInput from "./AddressInput.vue";

Object.defineProperty(mask, 'name', {value: 'mask'});
const directives = {mask};

const props = defineProps({
      'strings': {type: Object, required: false},
      'formData': {type: Object, required: false},
      'errors': {
        type: Object,
        required: true,
        default: () => ({})
      },
      'daDataApiKey': {type: String, required: false}
    }
);

</script>

<template>
  <div>
    <!-- Полное наименование -->
    <div class="form-group row">
      <label for="fullnameLE" class="col-sm-2 col-form-label">{{ strings.fullnameLE }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="fullnameLE"
            :placeholder="strings.placeholderFullnameLE"
            v-model="formData.fullnameLE"
        >
        <div v-if="errors.fullnameLE" class="invalid-feedback d-block">
          {{ errors.fullnameLE }}
        </div>
      </div>
    </div>

    <!-- Сокращенное наименование -->
    <div class="form-group row">
      <label for="shortnameLE" class="col-sm-2 col-form-label">{{ strings.shortnameLE }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="shortnameLE"
            :placeholder="strings.placeholderShortnameLE"
            v-model="formData.shortnameLE"
        >
        <div v-if="errors.shortnameLE" class="invalid-feedback d-block">
          {{ errors.shortnameLE }}
        </div>
      </div>
    </div>

    <!-- Юридический адрес -->
    <div class="form-group row">
      <label for="legalAddress" class="col-sm-2 col-form-label">{{ strings.legalAddress }}</label>
      <div class="col-sm-10">
        <address-input
            :model-value="formData.legalAddress"
            :label="strings.legalAddress"
            :api-key="daDataApiKey"
            :class="{'is-invalid': errors && errors.legalAddress}"
            @update:model-value="(value) => formData.legalAddress = value"
            @update:suggestion-data="(data) => formData.legalAddressData = data"
        />
        <div v-if="errors.legalAddress" class="invalid-feedback d-block">
          {{ errors.legalAddress }}
        </div>
      </div>
    </div>

    <!-- Почтовый адрес -->
    <div class="form-group row">
      <label for="postalAddress" class="col-sm-2 col-form-label">{{ strings.postalAddress }}</label>
      <div class="col-sm-10">
        <address-input
            :model-value="formData.postalAddress"
            :label="strings.postalAddress"
            :api-key="daDataApiKey"
            :class="{'is-invalid': errors && errors.postalAddress}"
            @update:model-value="(value) => formData.postalAddress = value"
            @update:suggestion-data="(data) => formData.postalAddressData = data"
        />
        <div v-if="errors.postalAddress" class="invalid-feedback d-block">
          {{ errors.postalAddress }}
        </div>
      </div>
    </div>

    <!-- Телефон -->
    <div class="form-group row">
      <label for="telephoneLE" class="col-sm-2 col-form-label">{{ strings.telephoneLE }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="telephoneLE"
            :placeholder="strings.placeholderTelephoneLE"
            v-model="formData.telephoneLE"
            v-mask="'+7 (###) ###-##-##'"
        >
        <div v-if="errors.telephoneLE" class="invalid-feedback d-block">
          {{ errors.telephoneLE }}
        </div>
      </div>
    </div>

    <!-- ИНН -->
    <div class="form-group row">
      <label for="innLE" class="col-sm-2 col-form-label">{{ strings.inn }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="innLE"
            :placeholder="strings.placeholderInn"
            v-model="formData.innLE"
            v-mask="'############'"
        >
        <div v-if="errors.innLE" class="invalid-feedback d-block">
          {{ errors.innLE }}
        </div>
      </div>
    </div>

    <!-- КПП -->
    <div class="form-group row">
      <label for="kpp" class="col-sm-2 col-form-label">{{ strings.kpp }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="kpp"
            :placeholder="strings.placeholderKpp"
            v-model="formData.kpp"
            v-mask="'#########'"
        >
        <div v-if="errors.kpp" class="invalid-feedback d-block">
          {{ errors.kpp }}
        </div>
      </div>
    </div>

    <!-- Банковские реквизиты -->
<!--
    <div class="form-group row">
      <label for="bankDetails" class="col-sm-2 col-form-label">{{ strings.bankDetails }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="bankDetails"
            :placeholder="strings.placeholderBankDetails"
            v-model="formData.bankDetails"
        >
        <div v-if="errors.bankDetails" class="invalid-feedback d-block">
          {{ errors.bankDetails }}
        </div>
      </div>
    </div>
-->

    <!-- Номер счета -->
    <div class="form-group row">
      <label for="accountNumber" class="col-sm-2 col-form-label">{{ strings.accountNumber }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="accountNumber"
            :placeholder="strings.placeholderAccountNumber"
            v-model="formData.accountNumber"
            v-mask="'####################'"
        >
        <div v-if="errors.accountNumber" class="invalid-feedback d-block">
          {{ errors.accountNumber }}
        </div>
      </div>
    </div>

    <!-- БИК -->
<!--    <div class="form-group row">
      <label for="bik" class="col-sm-2 col-form-label">{{ strings.bik }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            id="bik"
            :placeholder="strings.placeholderBik"
            v-model="formData.bik"
            v-mask="'#########'"
        >
        <div v-if="errors.bik" class="invalid-feedback d-block">
          {{ errors.bik }}
        </div>
      </div>
    </div>-->
  </div>
</template>


<style scoped>

</style>