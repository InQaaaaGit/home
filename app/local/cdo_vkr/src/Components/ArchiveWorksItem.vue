<template>
    <div>
        <v-dialog
                v-model="modalArchiveWorks"
                width="1000"
                scrollable

        >
            <template v-slot:activator="{ on, attrs }">
                <div class="d-flex align-center">
                    <v-icon
                            class="blue rounded-2"
                            v-bind="attrs"
                            v-on="on"
                            large
                    >
                        mdi-archive
                    </v-icon>
                    <span
                            class="blue--text text-md-h6"
                            v-bind="attrs"
                            v-on="on">
                    &nbsp;  {{ strings.labels.archive_files }}
                    </span>
                </div>
            </template>
            <v-card color="bg-grey-lighten-3">
                <v-card-title class="text-h5 blue darken-2 text--white">
                    <div class="text--white">{{ strings.labels.archive_files }}</div>
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
                <v-card-text max-height="300">

<!--                    <v-data-table
                            hide-default-header
                            :headers="headers"
                            :items="vkrFiles.work_archive"
                    >
                        <template v-slot:item.user_status.date="{item}">
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
                            <div class="mt-1">{{ item.user_status.date }}</div>
                        </template>
                    </v-data-table>-->
                    <div v-if="vkrFiles.work_archive.length" v-for="archive in vkrFiles.work_archive" :key="archive.id">
                        <v-row>
                            <v-col md="8">
                                <div
                                    class="d-flex flex-sm-column"
                                >
                                    <p>{{archive.user_status.date}} </p>
                                <p class="text-sm-justify">{{ archive.reason}}</p>
                                </div>
                            </v-col>

                            <v-col md="4" class="d-flex align-center">
                                <p><br></p>
                                <p><v-chip
                                    color="indigo darken-3"
                                    outlined
                                    large
                                    label
                                >
                                    <v-icon start>mdi-file-outline</v-icon>
                                    <a :href="archive.url" class="wo-underline text--file-link w-220px">
                                        {{ showPartOfString(archive.name) }}
                                    </a>
                                </v-chip></p>
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                    </div>
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

    </div>
</template>

<script>
import utility from "@/utility";

export default {
    name: "ListItemArchiveWorksClear",
    props: ['vkrFiles'],
    data: () => ({
        strings: {},
        headers: [
            {
                width: '10%',
                align: 'center',
                value: 'user_status.date',
            },
            {
                width: '90%',
                align: 'justify',
                value: 'reason',
            }
        ],
        modalArchiveWorks: false,

    }),
    methods: {
        showPartOfString(string) {
            return utility.showPartOfString(string);
        },
    },
    created() {
        this.strings = this.$store.state.strings;
    },
}
</script>

<style scoped>

</style>