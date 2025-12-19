<template>
    <div>
        <v-subheader class="ml-2" inset>{{ strings.labels.nameVKR }}</v-subheader>
        <v-list-item>
            <v-list-item-avatar>
                <v-icon
                        class="black lighten-1"
                        dark
                >
                    mdi-book-alphabet
                </v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
                <v-list-item-title v-text="itemVKR.name_of_vkr"></v-list-item-title>
                <v-list-item-subtitle
                        v-text="itemVKR.status.name"
                        :class="itemVKR.status.id===2 ? 'text-red' : 'text-black'"
                ></v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
    </div>
</template>

<script>
export default {
    props: ['itemVKR'],
    name: "ListItemVKRName",
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