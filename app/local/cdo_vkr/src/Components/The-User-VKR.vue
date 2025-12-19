<template>
    <div>
        <v-container>
            <v-row>
                <v-col md="8" class="b" elevation="1">
                    <v-card outlined elevation="5">
                        <v-card-text class="ml-4">
                            <v-row>
                                <v-col md="12">
                                    <h3 class="font-weight-bold">{{ strings.headers.VKR }}</h3>
                                </v-col>
                            </v-row>
                            <v-row v-if="isGEK">
                                <v-col md="4" class="text-md-h6 font-weight-bold">
                                    {{ strings.labels.manager_vkr }}
                                </v-col>
                                <v-col md="8" class="text-md-h6 text-justify ">
                                    <div class="pr-5">
                                        {{ itemVKR.manager.name + ', ' + itemVKR.manager.info }}
                                    </div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col md="4" class="text-md-h6 font-weight-bold">
                                    {{ strings.labels.nameVKR }}
                                </v-col>
                                <v-col md="8" class="text-md-h6 text-justify ">
                                    <div class="pr-5">
                                        {{ itemVKR.name_of_vkr }}
                                    </div>
                                </v-col>
                            </v-row>
                            <v-row class="d-flex align-center">
                                <v-col md="4" class="text-md-h6 font-weight-bold ">
                                    {{ strings.labels.vkr_files }}
                                </v-col>
                                <v-col md="8" class="text-md-h6">
                                    <template v-if="isWorkIsUploaded">
                                        <v-chip
                                                large
                                                color="teal lighten-2"
                                                outlined
                                                label
                                                @click=""
                                        >
                                            <v-icon start>mdi-file-outline</v-icon>
                                            <a :href="vkrFiles.work.url" class="wo-underline text--file-link w-220px">
                                                {{ showPartOfString(vkrFiles.work.name) }}
                                            </a>
                                        </v-chip>
                                        <span class="font-weight-light text-md-body-1">
                                            {{ vkrFiles.work.user_status.date }}
                                        </span>
                                    </template>
                                    <template v-else>
                                        <v-icon
                                                class="red lighten-1"
                                                dark
                                        >
                                            mdi-upload-off
                                        </v-icon>
                                        {{ strings.phrases.notUploaded }}
                                    </template>
                                </v-col>
                            </v-row>
                            <row-status :item-v-k-r="itemVKR" v-if="!isGEK"></row-status>
                            <template v-if="hideReviewAndComment">
                                <v-row class="d-flex align-center">
                                    <v-col md="4" class="text-md-h6 font-weight-bold ">
                                        {{ strings.inputs.comment }}
                                    </v-col>
                                    <v-col md="8">
                                        <template v-if="!isFileCommentUploaded">
                                            <v-file-input
                                                    class="w-75"
                                                    accept="application/pdf"
                                                    v-model="comment"
                                                    @change="uploadFile('comment', '/commentary/')"
                                                    :label="strings.inputs.file"
                                            >
                                            </v-file-input>
                                        </template>
                                        <template v-else>
                                            <v-chip
                                                    :close="isWorkCanBeDeleted"
                                                    color="indigo darken-3"
                                                    outlined
                                                    large
                                                    label
                                                    @click:close="deleteFile(vkrFiles.comment, 'comment')"
                                                    v-if="checkExistCommentaryFile"
                                            >
                                                <v-icon start>mdi-file-outline</v-icon>
                                                <a :href="vkrFiles.comment.url"
                                                   class="wo-underline text--file-link"
                                                   :class="!isWorkCanBeDeleted ? 'w-220px' : ''"
                                                >
                                                    {{ showPartOfString(vkrFiles.comment.name) }}
                                                </a>
                                            </v-chip>
                                            <template v-if="checkExistCommentaryFile">
                                                        <span
                                                                :class="vkrFiles.comment.user_status.id === 0 ? 'red--text' : 'green--text'"
                                                        >
                                                            {{ writeStatus }}
                                                        </span>
                                            </template>
                                        </template>
                                    </v-col>
                                </v-row>
                                <v-row
                                        class="d-flex align-center"
                                        v-if="hideIfVKRIsNotInEduLevelArraySettings">
                                    <v-col md="4" class="text-md-h6 font-weight-bold ">
                                        {{ strings.inputs.review }}
                                    </v-col>
                                    <v-col md="8">
                                        <template v-if="isFileReviewUploaded">
                                            <v-chip
                                                    :close="isWorkCanBeDeleted"
                                                    large
                                                    label
                                                    color="indigo darken-3"
                                                    outlined
                                                    @click:close="deleteFile(vkrFiles.review, 'review')"

                                            >
                                                <v-icon start>mdi-file-outline</v-icon>
                                                <a :href="vkrFiles.review.url"
                                                   class="wo-underline text--file-link"
                                                   :class="!isWorkCanBeDeleted ? 'w-220px' : ''"
                                                >
                                                    {{ showPartOfString(vkrFiles.review.name) }}
                                                </a>
                                            </v-chip>
                                            <template v-if="isFileReviewUploaded">
                                            <span
                                                    :class="vkrFiles.review.user_status.id === 0 ? 'red--text' : 'green--text'"
                                            >
                                                {{ writeStatusReview }}
                                            </span>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <v-file-input
                                                    class="w-75"
                                                    v-model="review"
                                                    accept="application/pdf"
                                                    @change="uploadFile('review', '/review/')"
                                                    :label="strings.inputs.file">
                                            </v-file-input>
                                        </template>
                                    </v-col>
                                </v-row>
                            </template>
                            <v-row
                                    class="d-flex align-center"
                                    v-if="checkVKRHaveArchive && !isGEK">
                                <v-col md="12">
                                    <archive-works-item
                                            :vkr-files="vkrFiles">
                                    </archive-works-item>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col md="4">
                    <div>
                        <the-student-info :item-v-k-r="itemVKR">

                        </the-student-info>
                    </div>
                    <div class="pt-4">
                        <v-btn

                                @click="changeStatusApplication()"
                                block
                                color="primary"
                                rounded
                        >
                            {{ this.$store.state.strings.buttons.back }}
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
            <v-row class="d-flex align-end flex-column flex-nowrap" v-if="!isGEK && !itemVKR.admitted">
                <v-col md="12">
                    <v-btn
                            @click="agreedVRK"
                            color="primary"
                            rounded
                            :disabled="isAgreedDisabled">
                        {{ strings.buttons.agreed }}
                    </v-btn>
                    <v-dialog
                            v-model="modalSendToRework"
                            width="500"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn @click="1"
                                   class="ml-2"
                                   :disabled="!isWorkIsUploaded || isDisabledWorkButtons"
                                   color="warning"
                                   rounded
                                   v-bind="attrs"
                                   v-on="on">
                                {{ strings.buttons.rework }}
                            </v-btn>
                        </template>

                        <v-card color="bg-grey-lighten-3" elevation="5">
                            <v-card-title class="text-h5 primary">
                                <span>{{ strings.headers.reasonForRework }}</span>
                                <v-spacer></v-spacer>
                                <v-btn
                                        icon
                                        dark
                                        color="black"
                                        @click="modalSendToRework = false"
                                >
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-card-text>
                                <v-textarea
                                        outlined
                                        :label="strings.headers.reasonForRework"
                                        v-model="reason"
                                >
                                </v-textarea>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                        color="primary"
                                        text
                                        :disabled="!reason.length"
                                        @click="sendToReworkVRK"
                                >
                                    {{ strings.buttons.ok }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-col>
            </v-row>
        </v-container>
        <!--        <v-container>
                    <v-row>
                        <v-col md="8">

                            <v-container>
                                <v-row>
                                    <v-col md="12">
                                        <v-card
                                                max-width="1000"
                                                class="mx-auto"
                                        >
                                            <v-toolbar
                                                    color="blue darken-2"
                                                    elevation="4"
                                            >
                                                <v-btn
                                                        icon
                                                        large
                                                        @click="getFilesOfVKR"
                                                        color="amber">
                                                    <v-icon>mdi-cached</v-icon>
                                                </v-btn>
                                                <v-spacer></v-spacer>
                                                <span class="white&#45;&#45;text mr-2"> {{ strings.labels.grade }} </span>
                                                <v-chip
                                                        color="amber"
                                                        large
                                                        label
                                                        v-text="itemVKR.grade"
                                                ></v-chip>
                                            </v-toolbar>
                                            <v-list
                                                    subheader
                                                    two-line>
                                                <template
                                                        v-if="checkVKRHaveArchive && !isGEK">
                                                    <list-item-archive-works
                                                            :vkr-files="vkrFiles"></list-item-archive-works>
                                                </template>
                                                <list-item-v-k-r-name :item-v-k-r="itemVKR"></list-item-v-k-r-name>
                                                <list-item-status :item-v-k-r="itemVKR"></list-item-status>
                                                <v-subheader class="ml-2" inset>{{ strings.labels.vkr_files }}</v-subheader>
                                                <v-list-item
                                                        v-if="isWorkIsUploaded"
                                                >

                                                    <v-chip
                                                            class="ma-4"
                                                            large
                                                            color="teal lighten-2"
                                                            outlined

                                                            @click=""
                                                    >
                                                        <v-icon start>mdi-file-outline</v-icon>
                                                        <a :href="vkrFiles.work.url" class="wo-underline">
                                                            {{ vkrFiles.work.name }}
                                                        </a>
                                                    </v-chip>
                                                </v-list-item>
                                                <v-list-item v-else>
                                                    <v-list-item-avatar>
                                                        <v-icon
                                                                class="red lighten-1"
                                                                dark
                                                        >
                                                            mdi-upload-off
                                                        </v-icon>
                                                    </v-list-item-avatar>
                                                    <v-list-item-content>
                                                        <v-list-item-title
                                                                v-text="strings.phrases.notUploaded"></v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <template v-if="hideReviewAndComment">
                                                    <v-subheader
                                                            class="ml-2"
                                                            inset>
                                                        {{ strings.inputs.comment }}
                                                    </v-subheader>
                                                    <v-list-item
                                                    >
                                                        <v-list-item-content>
                                                            <v-row class="d-flex align-center row&#45;&#45;flex ">
                                                                <v-col md="8">
                                                                    <template v-if="!isFileCommentUploaded">
                                                                        <v-file-input
                                                                                accept="application/pdf"
                                                                                v-model="comment"
                                                                                @change="uploadFile('comment', '/commentary/')"
                                                                                :label="strings.inputs.file"

                                                                        >
                                                                        </v-file-input>
                                                                    </template>
                                                                    <template v-else>
                                                                        <v-chip
                                                                                class="ma-4"
                                                                                close
                                                                                large
                                                                                color="indigo darken-3"
                                                                                outlined
                                                                                @click:close="deleteFile(vkrFiles.comment, 'comment')"
                                                                                v-if="checkExistCommentaryFile"
                                                                        >
                                                                            <v-icon start>mdi-file-outline</v-icon>
                                                                            <a :href="vkrFiles.comment.url"
                                                                               class="wo-underline">
                                                                                {{ vkrFiles.comment.name }}
                                                                            </a>
                                                                        </v-chip>
                                                                    </template>
                                                                </v-col>
                                                                <v-col md="4">
                                                                    <template v-if="checkExistCommentaryFile">
                                                                <span
                                                                        :class="vkrFiles.comment.user_status.id === 0 ? 'text-danger' : 'text-success'"
                                                                >
                                                                    {{ writeStatus }}
                                                                </span>
                                                                    </template>
                                                                </v-col>
                                                            </v-row>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                    <template v-if="hideIfVKRIsNotInEduLevelArraySettings">
                                                        <v-subheader class="ml-2" inset>{{
                                                            strings.inputs.review
                                                            }}
                                                        </v-subheader>
                                                        <v-list-item>
                                                            <v-list-item-content>
                                                                <v-row class="d-flex align-center row&#45;&#45;flex">
                                                                    <v-col md="8">
                                                                        <template v-if="isFileReviewUploaded">
                                                                            <v-chip
                                                                                    class="ma-4"
                                                                                    close
                                                                                    large
                                                                                    color="deep-purple lighten-3"
                                                                                    outlined
                                                                                    @click:close="deleteFile(vkrFiles.review, 'review')"
                                                                            >
                                                                                <v-icon start>mdi-file-outline</v-icon>
                                                                                <a :href="vkrFiles.review.url"
                                                                                   class="wo-underline">
                                                                                    {{ vkrFiles.review.name }}
                                                                                </a>
                                                                            </v-chip>
                                                                        </template>
                                                                        <template v-else>
                                                                            <v-file-input
                                                                                    v-model="review"
                                                                                    accept="application/pdf"
                                                                                    @change="uploadFile('review', '/review/')"
                                                                                    :label="strings.inputs.file">
                                                                            </v-file-input>
                                                                        </template>
                                                                    </v-col>
                                                                    <v-col md="4"
                                                                           class="text-md-h6"
                                                                    >
                                                                        <template v-if="isFileReviewUploaded">
                                                                        <span
                                                                            class="pl-2"
                                                                                :class="vkrFiles.review.user_status.id === 0 ? 'text-danger' : 'text-success'"
                                                                        >
                                                                            {{ writeStatusReview }}
                                                                        </span>
                                                                        </template>
                                                                    </v-col>
                                                                </v-row>
                                                            </v-list-item-content>
                                                        </v-list-item>
                                                    </template>
                                                </template>

                                            </v-list>
                                        </v-card>
                                    </v-col>
                                    &lt;!&ndash;                                <v-col md="2 d-flex align-self-sm-start align-center ">
                                                                        <v-text-field
                                                                                class="mt-2 w-50"
                                                                                outlined

                                                                                readonly
                                                                                :value="itemVKR.grade"

                                                                        >

                                                                        </v-text-field>
                                                                    </v-col>&ndash;&gt;
                                </v-row>
                            </v-container>
                        </v-col>
                        <v-col md="4">
                            <v-row>
                                <v-col>
                                    <the-student-info :item-v-k-r="itemVKR">

                                    </the-student-info>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="d-flex align-end flex-column justify-center">
                                    <v-btn
                                            @click="changeStatusApplication()"
                                            block
                                            color="primary"
                                            rounded
                                    >
                                        {{ this.$store.state.strings.buttons.back }}
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                    <v-row class="d-flex align-end flex-column flex-nowrap" v-if="!isGEK">
                        <v-col md="12">
                            <v-btn
                                    @click="agreedVRK"
                                    color="primary"
                                    rounded
                                    :disabled="isAgreedDisabled">
                                {{ strings.buttons.agreed }}
                            </v-btn>
                            <v-dialog
                                    v-model="modalSendToRework"
                                    width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn @click="1"
                                           class="ml-2"
                                           :disabled="!isWorkIsUploaded"
                                           color="warning"
                                           rounded
                                           v-bind="attrs"
                                           v-on="on">
                                        {{ strings.buttons.rework }}
                                    </v-btn>
                                </template>

                                <v-card color="bg-grey-lighten-3" elevation="5">
                                    <v-card-title class="text-h5 primary">
                                        <span>{{ strings.headers.reasonForRework }}</span>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                                icon
                                                dark
                                                color="black"
                                                @click="modalSendToRework = false"
                                        >
                                            <v-icon>mdi-close</v-icon>
                                        </v-btn>
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-card-text>
                                        <v-textarea
                                                outlined
                                                :label="strings.headers.reasonForRework"
                                                v-model="reason"
                                        >
                                        </v-textarea>
                                    </v-card-text>
                                    <v-divider></v-divider>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                                color="primary"
                                                text
                                                :disabled="!reason.length"
                                                @click="sendToReworkVRK"
                                        >
                                            {{ strings.buttons.ok }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-col>
                    </v-row>
                </v-container>-->
    </div>
</template>

<script>
import Loader from "@/Components/Loader.vue";
import TheTitle from "@/Components/The-title.vue";
import moodleAjax from 'core/ajax';
import notification from 'core/notification';
import TheStudentInfo from "@/Components/The-Student-Info.vue";
import utility from "@/utility";
import ListItemStatus from "@/Components/ListItemStatus.vue";
import ListItemVKRName from "@/Components/ListItemVKRName.vue";
import ListItemArchiveWorks from "@/Components/ListItemArchiveWorks.vue";
import ArchiveWorksItem from "@/Components/ArchiveWorksItem.vue";
import RowStatus from "@/Components/RowStatus.vue";

export default {
    components: {
        RowStatus,
        ArchiveWorksItem,
        ListItemArchiveWorks, ListItemVKRName, ListItemStatus, TheStudentInfo, TheTitle, Loader
    },
    props: ['itemVKR', 'isGEK'],
    data: () => ({
        reason: '',
        commentExist: false,
        modalArchiveWorks: false,
        comment: {},
        review: {},
        statusesForHideCommentAndReview: [2, 5, 4, 6, 7],
        statusesForHideClose: [7,6],
        overlay: true,
        modalSendToRework: false,
        vkrFiles: {
            work: {
                user_status: {
                    date: '',
                    id: 0
                }
            },
            comment: {},
            review: {},
            work_archive: []
        },
    }),
    methods: {
        async realoadData() {
            await this.getFilesOfVKR();
        },
        changeStatusApplication() {
            this.$store.commit('changeStatusApplication', !this.$store.state.statusApplication)
        },
        async deleteFile(file, area) {
            let isDeleted = await utility.ajaxMoodleCall('local_cdo_vkr_delete_file',
                {
                    file_id: file.id
                }
            );

            if (isDeleted) {
                this[area] = {};
                this.vkrFiles[area] = {};
                //if (this.itemVKR.status.id === this.$store.state.status.VKRHaveReviewAndComment.id) {
                let result = await this.changeVKRStatus(this.$store.state.status.VKRAgreed.id, false);
                if (result) {
                    this.itemVKR.status = this.$store.state.status.VKRAgreed;
                    this.$store.commit('changeItemVKR', this.itemVKR);
                }
                //}
            }
            return false;
        },
        async uploadFile(area, filepath = '/') {
            if (!!this[area]) {
                if (this[area].name.substring(this[area].name.lastIndexOf('.') + 1) !== 'pdf') {
                  alert('Поддерживается загрузка только PDF');
                  this[area] = null;
                  this.loadingFile = false;
                  return;

                }
                this.loadingFile = true;
                this.vkrFiles[area] = await utility.uploadFile(this[area], filepath, this.itemVKR.id);
                this.loadingFile = false;
            }
        },
        async agreedVRK() {
            let result = await this.changeVKRStatus(this.$store.state.status.VKRAgreed.id);
            this.itemVKR.status.id = this.$store.state.status.VKRAgreed.id;
            this.itemVKR.status.name = this.$store.state.status.VKRAgreed.name;
            this.$store.commit('changeItemVKR', this.itemVKR);
        },
        async sendToReworkVRK() {
            this.modalSendToRework = false;
            this.$store.state.loaderOn = true;
            if (this.vkrFiles.comment.hasOwnProperty('id')) {
                await this.deleteFile(this.vkrFiles.comment, 'comment');
            }

            if (this.vkrFiles.review.hasOwnProperty('id')) {
                await this.deleteFile(this.vkrFiles.review, 'review');
            }

            let result = await utility.ajaxMoodleCall('local_cdo_vkr_push_work_to_archive',
                {
                    id_vkr: this.itemVKR.id,
                    file_id: this.vkrFiles.work.id,
                    reason: this.reason
                }
            );
            if (result) {
                let copyWork = this.vkrFiles.work;
                copyWork.reason = this.reason;
                this.vkrFiles.work_archive.push(copyWork);
                this.vkrFiles.work = {};
                await this.changeVKRStatus(this.$store.state.status.VKROnRework.id);
                this.itemVKR.status = this.$store.state.status.VKROnRework;
                this.$store.commit('changeItemVKR', this.itemVKR);
            }
            this.$store.state.loaderOn = false;
        },
        async getFilesOfVKR() {
            this.$store.state.loaderOn = true;
            let response = await utility.ajaxMoodleCall('local_cdo_vkr_get_files', {
                id_vkr: this.itemVKR.id
            });
            if (Array.isArray(response)) {
                response = {
                    comment: {},
                    review: {},
                    work: {},
                    work_archive: []
                };
            }
            this.vkrFiles.comment = response.comment ?? {};
            this.vkrFiles.work = response.work ?? {};
            this.vkrFiles.work_archive = response.work_archive ?? [];
            this.vkrFiles.review = response.review ?? {};
            this.$store.state.loaderOn = false;
        },
        isAreaFileUploaded(area) {
            if (this.vkrFiles.hasOwnProperty(area)) {
                return this.vkrFiles[area].hasOwnProperty('id');
            }
            return false;
        },
        async changeVKRStatus(newStatus, acquainted) {
            return await utility.changeVKRStatus(this.itemVKR.id, newStatus, acquainted);
        },
        showPartOfString(string) {
            return utility.showPartOfString(string);
        },
    },
    computed: {
        isWorkCanBeDeleted() {
            return !(this.statusesForHideClose.includes(this.itemVKR.status.id) || this.isGEK || this.itemVKR.admitted);
        },
        isDisabledWorkButtons() {
            return this.itemVKR.status.id === this.$store.state.status.VKRStudentSuccessfulEndEducation.id
                || this.itemVKR.status.id === this.$store.state.status.VKRStudentExpelled.id;

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
                        return 'black';
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
                        return 'mdi-comment-eye';
                    default:
                        return 'mdi-list-status';
                }
            }
            return 'mdi-list-status';
        },
        writeVKRStatusName() {
            if (this.itemVKR.hasOwnProperty('status'))
                return utility.writeVKRStatusName(this.itemVKR.status.id, this.$store.state.status);
            return '';
        },
        writeStatus() {
            if (this.vkrFiles.comment.user_status.id === 0) {
                return this.$store.state.strings.statuses.notAcquainted;
            } else {
                return this.$store.state.strings.statuses.acquainted + ' ' + this.vkrFiles.comment.user_status.date;
            }
            //return this.vkrFiles.comment.user_status.id === 0 ? this.$store.state.strings.statuses.notAcquainted : this.$store.state.strings.statuses.acquainted
        },
        writeStatusReview() {
            if (this.vkrFiles.review.user_status.id === 0) {
                return this.$store.state.strings.statuses.notAcquainted;
            } else {
                return this.$store.state.strings.statuses.acquainted + ' ' + this.vkrFiles.review.user_status.date;
            }
        },
        isFileReviewUploaded() {
            if (this.vkrFiles.hasOwnProperty('review')) {
                return this.vkrFiles.review.hasOwnProperty('id');
            }
            return false;
        },
        isFileCommentUploaded() {
            if (this.vkrFiles.hasOwnProperty('comment')) {
                return this.vkrFiles.comment.hasOwnProperty('id');
            }
            return false;
        },
        checkVKRHaveArchive() {
            if (this.vkrFiles.hasOwnProperty('work_archive')) {
                return !!this.vkrFiles.work_archive.length;

            }
            return false;
        },
        isWorkIsUploaded() {
            if (this.vkrFiles.hasOwnProperty('work')) {
                return this.vkrFiles.work.hasOwnProperty('id'); //true
            }
            return false;
        },
        checkExistCommentaryFile() {
            return this.vkrFiles.hasOwnProperty('comment') && !_.isEmpty(this.vkrFiles.comment);
        },
        hideIfVKRIsNotInEduLevelArraySettings() {
            return !!this.$store.state.levelEduCantBeReviewed.includes(this.itemVKR.edu_level);

        },
        isAgreedDisabled() {
            return !(this.itemVKR.status.id === 1 && this.vkrFiles.work.hasOwnProperty('id'));

        },
        hideReviewAndComment() {
            return this.statusesForHideCommentAndReview.includes(this.itemVKR.status.id);
        }
    },
    created() {
        this.getFilesOfVKR();
        this.strings = this.$store.state.strings;
    },
    watch: {
        vkrFiles: {
            handler(newData, oldData) {

                if (this.$store.state.levelEduCantBeReviewed.includes(this.itemVKR.edu_level)) {
                    if (newData.comment.hasOwnProperty('id') && newData.review.hasOwnProperty('id') && this.itemVKR.status.id === 2) {
                        this.changeVKRStatus(this.$store.state.status.VKRHaveReviewAndComment.id, false);
                        this.itemVKR.status = this.$store.state.status.VKRHaveReviewAndComment;
                        this.$store.commit('changeItemVKR', this.itemVKR);
                    }
                } else {
                    if (newData.comment.hasOwnProperty('id')) {
                        if (this.itemVKR.status.id === 2) {
                            this.changeVKRStatus(this.$store.state.status.VKRHaveReviewAndComment.id, false);
                            this.itemVKR.status = this.$store.state.status.VKRHaveReviewAndComment;
                            this.$store.commit('changeItemVKR', this.itemVKR);
                        }
                    }
                }

            },
            deep: true
        },
        immediate: false
    }
}
</script>

<style scoped>

</style>