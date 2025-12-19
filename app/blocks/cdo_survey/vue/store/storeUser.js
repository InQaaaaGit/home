import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
  state: () => ({
    lastname: '',
    firstname: '',
    apiToken: '',
    guidPassportRF: ''
  }),
  
  actions: {
    setUserData(lastname, firstname, apiToken, guidPassportRF) {
      this.lastname = lastname;
      this.firstname = firstname;
      this.apiToken = apiToken;
      this.guidPassportRF = guidPassportRF;
    },
  },
  
  getters: {
    getUserData: (state) => ({
      lastname: state.lastname,
      firstname: state.firstname,
      apiToken: state.apiToken,
      guidPassportRF: state.guidPassportRF
    }),
  },
}); 