import { defineStore } from 'pinia';

export const useAppStore = defineStore('app', {
    state: () => ({
        userId: null,
        capabilities: {
            isAdmin: false,
            isStudent: false,
        },
        disciplineId: null,
        disciplineName: null,
    }),

    getters: {
        isAdmin: (state) => state.capabilities?.isAdmin === true,
        isStudent: (state) => state.capabilities?.isStudent === true,
    },

    actions: {
        initializeApp(userId, capabilities, disciplineId, disciplineName) {
            this.userId = userId ? Number(userId) : null;
            this.capabilities = capabilities || {
                isAdmin: false,
                isStudent: false,
            };
            this.disciplineId = disciplineId || null;
            this.disciplineName = disciplineName || null;
        },
    },
});

