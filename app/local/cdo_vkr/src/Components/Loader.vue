<template>
    <v-overlay
            :value="this.$store.state.loaderOn"
            class="align-center justify-center"
    >
        <v-progress-circular
                color="primary"
                indeterminate
                size="64"
        ></v-progress-circular>
    </v-overlay>
</template>

<script>
export default {
    name: "Loader"
}
</script>

<style scoped>

</style>