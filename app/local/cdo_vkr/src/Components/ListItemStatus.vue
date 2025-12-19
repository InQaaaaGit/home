<template>
    <div>
        <v-subheader class="ml-2" inset>
            {{ strings.labels.status }}
        </v-subheader>
        <v-list-item class="">
            <v-list-item-avatar>
                <v-icon
                        :class="colorStatus"
                        dark
                >
                    {{ stylesStatus }}
                </v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
                <v-list-item-title>
                    {{ writeVKRStatusName }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </div>
</template>

<script>
import utility from "@/utility";

export default {
    props: ['itemVKR'],
    name: "ListItemStatus",
    data: () => ({
        strings: {},
    }),
    computed: {
        writeVKRStatusName() {
            if (this.itemVKR.hasOwnProperty('status'))
                return utility.writeVKRStatusName(this.itemVKR.status.id, this.$store.state.status);
            return '';
        },
        colorStatus() {
            if (this.itemVKR.hasOwnProperty('status')) {
                switch (this.itemVKR.status.id) {
                    case this.$store.state.status.VKRUpload.id:
                        return 'amber lighten-3';
                    case this.$store.state.status.VKRAgreed.id:
                        return 'green darken-2';
                    case this.$store.state.status.VKROnRework.id:
                        return 'deep-orange lighten-2';
                    case this.$store.state.status.VKRNotUpload.id:
                        return 'light-blue lighten-4';
                    case this.$store.state.status.VKRHaveReviewAndComment.id:
                        return 'teal darken-1';
                    default:
                        return 'mdi-list-status';
                }
            }
            return 'black';
        },
        stylesStatus() {
            if (this.itemVKR.hasOwnProperty('status')) {
                switch (this.itemVKR.status.id) {
                    case this.$store.state.status.VKRUpload.id:
                        return 'mdi-upload';
                    case this.$store.state.status.VKRAgreed.id:
                        return 'mdi-check-bold';
                    case this.$store.state.status.VKROnRework.id:
                        return 'mdi-close-thick';
                    case this.$store.state.status.VKRNotUpload.id:
                        return 'mdi-upload-off';
                    case this.$store.state.status.VKRHaveReviewAndComment.id:
                        return 'mdi-comment-multiple';
                    default:
                        return 'mdi-list-status';
                }

            }
            return 'mdi-list-status';
        },
    },
    created() {
        this.strings = this.$store.state.strings;
    }
}
</script>

<style scoped>

</style>