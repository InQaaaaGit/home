<template>
    <v-card outlined elevation="5">
        <v-card-text class="blue-grey lighten-4">
            <div class="pb-2">
                <h2 class="font-weight-bolder">{{ strings.labels.student }}</h2>
            </div>
            <div class="pb-2 text-primary">
                <h3>{{ itemVKR.FIO }}</h3>
            </div>
            <div class="pb-2">
                <span class="font-weight-bold pb-1">{{ strings.labels.edu_level }}:</span>
                {{ itemVKR.edu_level }}
            </div>
            <div class="pb-2">
                <span class="font-weight-bold pb-1">{{ strings.labels.edu_speciality }}:</span>
                {{ itemVKR.edu_speciality }}
            </div>
            <div class="pb-2">
                <span class="font-weight-bold pb-1">{{ strings.labels.edu_form }}:</span>
                {{ itemVKR.edu_form }}
            </div>
            <div class="pb-2">
                <span class="font-weight-bold pb-1">{{ strings.labels.edu_group }}:</span>
                {{ itemVKR.edu_group }}
            </div>
            <div class="pb-2">
                <span class="font-weight-bold pb-1">{{ strings.labels.edu_division }}:</span>
                {{ itemVKR.edu_division }}
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: ['itemVKR'],
    name: "The-Student-Info",
    data: () => ({
        strings: {}
    }),
    created() {
        this.strings = this.$store.state.strings;
    }

}
</script>

<style scoped>

</style>