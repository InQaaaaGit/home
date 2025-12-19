<template>
    <div id="app">
        <v-app>
            <v-main>
                <the-student-v-k-r>
                </the-student-v-k-r>
                <Loader>
                </Loader>
            </v-main>
        </v-app>
    </div>
</template>

<script>
import TheStudentVKR from "@/Components/The-Student-VKR.vue";
import TheTitle from "@/Components/The-title.vue";
import Loader from "@/Components/Loader.vue";

export default {
    name: "StudyApp",
    components: {Loader, TheTitle, TheStudentVKR}
}
</script>

<style scoped>

</style>