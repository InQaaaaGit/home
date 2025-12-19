<template>
    <div>
        <v-subheader class="ml-2" inset>
            {{ strings.labels.archive_files }}
        </v-subheader>
        <v-list-item
        >
            <v-list-item-avatar>
                <v-dialog
                        v-model="modalArchiveWorks"
                        width="1000"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                                class="blue"
                                dark
                                v-bind="attrs"
                                v-on="on"
                        >
                            mdi-archive
                        </v-icon>
                    </template>
                    <v-card color="bg-grey-lighten-3">
                        <v-card-title class="text-h5 blue darken-2">
                            <div class="text--white">{{ strings.labels.archive_files }} </div>
                            <v-spacer></v-spacer>
                            <v-btn
                                    icon
                                    dark
                                    color="white"
                                    @click="modalArchiveWorks = false"
                            >
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text max-height="100">
                            <v-data-table
                                    hide-default-header
                                    :headers="headers"
                                    :items="vkrFiles.work_archive"
                            >
                                <template  v-slot:item.user_status.date="{item}">
                                    <v-chip
                                        class="mt-2"
                                        color="red lighten-2"
                                        outlined
                                    >
                                        <v-icon start>mdi-file-outline</v-icon>
                                        <a :href="item.url" class="wo-underline">
                                            {{ item.name }}
                                        </a>
                                    </v-chip>
                                    <div class="mt-1">{{item.user_status.date}}</div>
                                </template>
                            </v-data-table>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                    color="primary"
                                    text
                                    @click="modalArchiveWorks = false"
                            >
                                {{ strings.buttons.close }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-list-item-avatar>
            <v-list-item-content>
                <v-list-item-title>
                    {{ strings.labels.archive_files}}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>


    </div>
</template>

<script>
export default {
    props: ['vkrFiles'],
    name: "ListItemArchiveWorks",
    data: () => ({
        strings: {},
        headers: [
            {
                width:'10%',
                align:'center',
                value: 'user_status.date',
            },
            {
                width:'90%',
                align:'justify',
                value: 'reason',
            }
        ],
        modalArchiveWorks: false,

    }),
    created() {
        this.strings = this.$store.state.strings;
    },

}
</script>

<style scoped>

</style>