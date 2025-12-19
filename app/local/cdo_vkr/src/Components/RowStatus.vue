<template>
    <v-row class="d-flex align-center">
        <v-col md="4" class="text-md-h6 font-weight-bold ">
            {{ strings.labels.status }}
        </v-col>
        <v-col md="8" class="text-md-h6">
            {{ writeVKRStatusName }}
            <v-icon
                large
            >
                {{ stylesStatus }}
            </v-icon>

        </v-col>
    </v-row>
</template>

<script>
import utility from "@/utility";

export default {
    name: "RowStatus",
    props: ['itemVKR'],
    data: () => ({
        strings: {},
    }),
    created() {
        this.strings = this.$store.state.strings;
    },
    computed: {
        stylesStatus() {
            if (!this.itemVKR) {
                return ''
            }
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
                        return 'mdi-comment-eye';
                    default:
                        return 'mdi-list-status';
                }
            }
            return 'mdi-list-status';
        },
        colorStatus() {
            if (!this.itemVKR) {
                return ''
            }
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
                        return 'black';
                }
            }
            return 'black';
        },
        writeVKRStatusName() {
            if (!this.itemVKR) {
                return ''
            }
            if (this.itemVKR.hasOwnProperty('status'))
                return utility.writeVKRStatusName(this.itemVKR.status.id, this.$store.state.status);
            return '';
        },
    }

}
</script>

<style scoped>

</style>