<template>
    <div id="app">
        <v-app>
            <Loader></Loader>
            <TableVKRsForTeachers></TableVKRsForTeachers>
        </v-app>
    </div>
</template>

<script>
import TableVKRsForTeachers from "@/Components/Table-vkrs-for-teachers.vue";
import Loader from "@/Components/Loader.vue";
import TheTitle from "@/Components/The-title.vue";

export default {
    name: "App",
    components: {
        TheTitle,
        Loader,
        TableVKRsForTeachers: TableVKRsForTeachers
    },
    mounted() {
        this.$store.state.loaderOn = false;
    }
}

</script>

