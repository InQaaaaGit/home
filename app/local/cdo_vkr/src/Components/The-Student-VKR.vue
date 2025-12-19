<template>
  <v-container class=''>
    <v-row v-if="!!itemVKR">
      <v-col md="8">
        <v-card outlined elevation="5">
          <v-card-text class="ml-4">
            <v-row>
              <v-col md="12">
                <v-row class="d-flex align-center">
                  <v-col md="7" class="font-weight-bold text-md-h5">
                    {{ strings.headers.VKR }}
                  </v-col>
                  <v-col md="3" class="text-md-h6 font-weight-bold green--text">
                    {{ itemVKR.admitted ? strings.phrases.success_admitted : '' }}
                  </v-col>
                  <v-col
                      md="2" class="d-flex justify-end pr-15">
                    <div class="d-flex flex-sm-column align-center">
                      <v-avatar
                          color="grey lighten-3"
                          size="56"
                          tile
                          rounded
                      >
                                            <span class="text-h5 green--text"
                                                  v-if="itemVKR.grade!=='0' && itemVKR.grade!=='' && itemVKR.grade!==0"
                                            >
                                                {{ itemVKR.grade }}
                                            </span>
                      </v-avatar>
                      {{ strings.labels.grade }}
                    </div>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
            <v-row>
              <v-col md="4" class="text-md-h6 font-weight-bold">
                {{ strings.labels.nameVKR }}
              </v-col>
              <v-col md="8" class="text-md-h6 text-justify">
                <div class="pr-5">
                  {{ itemVKR.name_of_vkr }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col md="4" class="text-md-h6 font-weight-bold">
                {{ strings.labels.manager_vkr }}
              </v-col>
              <v-col md="8" class="text-md-h6 text-justify ">
                <div class="pr-5">
                  {{ itemVKR.manager.name + ', ' + itemVKR.manager.info }}
                </div>
              </v-col>
            </v-row>
            <v-row class="d-flex align-center">
              <v-col md="4" class="text-md-h6 font-weight-bold ">
                {{ strings.labels.vkr_files }}
              </v-col>
              <v-col md="8" class="text-md-h6">
                <template v-if="itemVKR.agreedEBS">
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
                    <v-file-input
                        class="w-75 "
                        v-if="[0,3].includes(this.itemVKR.status.id)"
                        accept="application/pdf"
                        :loading="this.loadingFile"
                        v-model="work"
                        @change="uploadFile"
                        :label="strings.inputs.file"
                        dense
                    >
                    </v-file-input>
                  </template>
                </template>
                <template v-else>
                  <v-checkbox
                      class="ml-3"
                      color="green"
                      :label="strings.phrases.agreedForEBS"
                      v-model="itemVKR.agreedEBS"
                      @change="setAgreedEBS"
                  >
                  </v-checkbox>
                </template>
              </v-col>
            </v-row>
            <row-status :item-v-k-r="itemVKR"></row-status>
            <v-row
                class="d-flex align-center"
                v-if="isCommentUpload"
            >
              <v-col md="4" class="text-md-h6 font-weight-bold">
                {{ strings.inputs.comment }}
              </v-col>
              <v-col md="8" class="text-md-h6 text-justify">
                <v-chip
                    color="indigo darken-3"
                    outlined
                    large
                    label
                >
                  <v-icon start>mdi-file-outline</v-icon>
                  <a :href="vkrFiles.comment.url"
                     class="wo-underline text--file-link w-220px">
                    {{ showPartOfString(vkrFiles.comment.name) }}
                  </a>
                </v-chip>
                <v-btn
                    large
                    :loading="loadingAcquired"
                    :disabled="!!vkrFiles.comment.user_status.id"
                    class="blue text--white"
                    rounded
                    @click="setAcquaintedStatus(vkrFiles.comment)"
                    v-text="this.$store.state.strings.statuses.acquainted">
                </v-btn>
                <span
                    v-show="!!vkrFiles.comment.user_status.id"
                    class="ml-2 green&#45;&#45;text">
                                                        {{ vkrFiles.comment.user_status.date }}
                                                    </span>
              </v-col>
            </v-row>
            <v-row
                class="d-flex align-center"
                v-if="isReviewUpload"
            >
              <v-col md="4" class="text-md-h6 font-weight-bold">
                {{ strings.inputs.review }}
              </v-col>
              <v-col md="8" class="text-md-h6 text-justify">
                <v-chip
                    color="indigo darken-3"
                    outlined
                    large
                    label
                >
                  <v-icon start>mdi-file-outline</v-icon>
                  <a :href="vkrFiles.review.url"
                     class="wo-underline text--file-link w-220px">
                    {{ showPartOfString(vkrFiles.review.name) }}
                  </a>
                </v-chip>
                <v-btn
                    id="review_btn"
                    large
                    :loading="loadingAcquired"
                    :disabled="!!vkrFiles.review.user_status.id"
                    class="blue text--white"
                    rounded
                    @click="setAcquaintedStatus(vkrFiles.review)"
                    v-text="this.$store.state.strings.statuses.acquainted">
                </v-btn>
                <span
                    v-show="!!vkrFiles.review.user_status.id"
                    class="ml-2">
                                    {{ vkrFiles.review.user_status.date }}
                                </span>
              </v-col>
            </v-row>
            <template v-if="isVKRHaveArchive">
              <v-row
                  class="d-flex align-center"
                  v-if="itemVKR.status.id === this.$store.state.status.VKROnRework.id">
                <v-col md="4" class="text-md-h6 font-weight-bold">
                  {{ strings.labels.reason }}
                </v-col>
                <v-col md="8" class="text-md-h7 text-justify">
                  <div class="pr-5">
                    {{ vkrFiles.work_archive[0].reason }}
                  </div>
                </v-col>
              </v-row>
              <v-row
                  class="d-flex align-center"
                  v-if="isVKRHaveArchive">
                <v-col md="12">
                  <archive-works-item
                      :vkr-files="vkrFiles">
                  </archive-works-item>
                </v-col>
              </v-row>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="4">
        <the-student-info :item-v-k-r="itemVKR">
        </the-student-info>
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col md="12" class="text-md-h4">
        {{ strings.phrases.VKRDoesntExist }}
      </v-col>
    </v-row>
    <!--        <v-row v-if="!!itemVKR">
                <v-col md="8">
                    <v-card
                            max-width="1000"
                            class="mx-auto"
                    >
                        <v-toolbar
                                color="blue darken-2"
                                elevation="4"
                        >
                            <v-spacer></v-spacer>
                            <span class="white&#45;&#45;text mr-2"> {{ strings.labels.grade }} </span>
                            <v-chip
                                    color="grey"
                                    large
                                    label
                                    v-text="itemVKR.grade"
                            ></v-chip>
                        </v-toolbar>
                        <v-list>
                            <v-subheader class="ml-2" inset>
                                {{ strings.labels.nameVKR }}
                            </v-subheader>
                            <v-list-item class="">
                                <v-list-item-avatar>
                                    <v-icon
                                            class="black lighten-1"
                                            dark
                                    >
                                        mdi-book-alphabet
                                    </v-icon>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ itemVKR.name_of_vkr }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-subheader class="ml-2" inset>
                                {{ strings.labels.manager_vkr }}
                            </v-subheader>
                            <v-list-item class="">
                                <v-list-item-avatar>
                                    <v-icon
                                            class="green darken-4"
                                            dark
                                    >
                                        mdi-human-male-board
                                    </v-icon>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ itemVKR.manager.name + ', ' + itemVKR.manager.info }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <list-item-status :item-v-k-r="itemVKR"></list-item-status>


                            <v-subheader class="ml-2" inset>
                                {{ strings.labels.vkr_files }}
                            </v-subheader>
                            <template v-if="itemVKR.agreedEBS">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-row class="row&#45;&#45;flex">
                                            <v-col>
                                                <template v-if="isWorkIsUploaded">

                                                    <v-chip
                                                            class="ma-4"
                                                            large
                                                            color="deep-purple lighten-3"
                                                            outlined
                                                    >
                                                        <v-icon start>mdi-file-outline</v-icon>
                                                        <a :href="vkrFiles.work.url"
                                                           class="wo-underline">
                                                            {{ vkrFiles.work.name }}
                                                        </a>
                                                    </v-chip>
                                                </template>
                                                <template v-else>
                                                    <v-file-input
                                                            v-if="[0,3].includes(this.itemVKR.status.id)"
                                                            accept="application/pdf"
                                                            :loading="this.loadingFile"
                                                            v-model="work"
                                                            @change="uploadFile"
                                                            :label="strings.inputs.file"
                                                            dense
                                                    >
                                                    </v-file-input>
                                                </template>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-content>
                                </v-list-item>
                            </template>
                            <template v-else>
                                <v-list-item
                                >
                                    <v-list-item-content>
                                        <v-checkbox
                                                class="ml-3"
                                                color="green"
                                                :label="strings.phrases.agreedForEBS"
                                                v-model="itemVKR.agreedEBS"
                                                @change="setAgreedEBS"
                                        >

                                        </v-checkbox>
                                    </v-list-item-content>
                                </v-list-item>
                            </template>
                            <template v-if="isCommentUpload">
                                <v-subheader
                                        class="ml-2"
                                        inset>
                                    {{ strings.inputs.comment }}
                                </v-subheader>
                                <v-list-item
                                >
                                    <v-list-item-content>
                                        <v-row class="row&#45;&#45;flex">
                                            <v-col>
                                                <v-chip
                                                        class="ma-4"
                                                        large
                                                        color="indigo darken-3"
                                                        outlined
                                                >
                                                    <v-icon start>mdi-file-outline</v-icon>
                                                    <a :href="vkrFiles.comment.url"
                                                       class="wo-underline">
                                                        {{ vkrFiles.comment.name }}
                                                    </a>
                                                </v-chip>
                                                <v-btn
                                                        large
                                                        :loading="loadingAcquired"
                                                        :disabled="!!vkrFiles.comment.user_status.id"
                                                        class="blue"
                                                        rounded
                                                        @click="setAcquaintedStatus(vkrFiles.comment)"
                                                        v-text="this.$store.state.strings.statuses.acquainted">
                                                </v-btn>
                                                <span
                                                        v-show="!!vkrFiles.comment.user_status.id"
                                                        class="ml-2 green&#45;&#45;text">
                                                    {{ vkrFiles.comment.user_status.date }}
                                                </span>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-content>
                                </v-list-item>
                            </template>
                            <template v-if="isReviewUpload">
                                <v-subheader
                                        class="ml-2"
                                        inset>
                                    {{ strings.inputs.review }}
                                </v-subheader>
                                <v-list-item
                                >
                                    <v-list-item-content>
                                        <v-row class="row&#45;&#45;flex">
                                            <v-col>
                                                <v-chip
                                                        class="ma-4"
                                                        large
                                                        color="indigo darken-3"
                                                        outlined
                                                >
                                                    <v-icon start>mdi-file-outline</v-icon>
                                                    <a :href="vkrFiles.review.url"
                                                       class="wo-underline">
                                                        {{ vkrFiles.review.name }}
                                                    </a>
                                                </v-chip>
                                                <v-btn large
                                                       :disabled="!!vkrFiles.review.user_status.id"
                                                       class="blue"
                                                       rounded
                                                       @click="setAcquaintedStatus(vkrFiles.review)"
                                                       v-text="this.$store.state.strings.statuses.acquainted">
                                                </v-btn>
                                                <span
                                                        v-show="!!vkrFiles.review.user_status.id"
                                                        class="ml-2 green&#45;&#45;text">
                                                    {{ vkrFiles.review.user_status.date }}
                                                </span>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-content>
                                </v-list-item>
                            </template>
                            <list-item-archive-works v-if="isVKRHaveArchive"
                                                     :vkr-files="vkrFiles">

                            </list-item-archive-works>
                        </v-list>

                    </v-card>
                </v-col>
                <v-col md="4">
                    <the-student-info :item-v-k-r="itemVKR">

                    </the-student-info>
                </v-col>
            </v-row>
            <v-row v-else>
                <v-col md="12">
                    {{ strings.phrases.VKRDoesntExist }}
                </v-col>
            </v-row>-->
  </v-container>
</template>

<script>
import TheStudentInfo from "@/Components/The-Student-Info.vue";
import utility from "@/utility";
import ListItemArchiveWorks from "@/Components/ListItemArchiveWorks.vue";
import ListItemStatus from "@/Components/ListItemStatus.vue";
import Loader from "@/Components/Loader.vue";
import RowStatus from "@/Components/RowStatus.vue";
import ArchiveWorksItem from "@/Components/ArchiveWorksItem.vue";

export default {
  name: "The-Student-VKR",
  components: {
    RowStatus, Loader,
    ListItemStatus, ListItemArchiveWorks,
    TheStudentInfo, ArchiveWorksItem
  },
  data: () => ({
    itemVKR: {
      id: 0,
      name_of_vkr: '',
      manager: {
        name: ''
      }
    },
    strings: {},
    work: {},
    vkrFiles: {
      work: {},
      comment: {},
      review: {},
      work_archive: []
    },
    loadingFile: false,
    loadingAcquired: false,
  }),
  methods: {
    showPartOfString(string) {
      return utility.showPartOfString(string);
    },
    async uploadFile() {
      if (!!this.work) {

        if (this.work.name.substring(this.work.name.lastIndexOf('.') + 1) !== 'pdf') {
          alert('Поддерживается загрузка только PDF');
          this.work = null;
          this.loadingFile = false;
          return;

        }
        this.loadingFile = true;
        this.vkrFiles.work = await utility.uploadFile(this.work, '/work/', this.itemVKR.id);

        let change_result = await this.changeVKRStatus();
        if (change_result) {
          this.itemVKR.status = this.$store.state.status.VKRUpload;
          this.$store.commit('changeItemVKR', this.itemVKR);
        }
        await this.changeVKRStatusManager();
        this.loadingFile = false;
      }
    },
    async getFilesVKR() {
      this.vkrFiles = await utility.getFilesVKR(this.itemVKR.id);
    },
    async getVKRInfo() {
      let result = await utility.ajaxMoodleCall('local_cdo_vkr_get_vkr_info', {});
      return result.pop();
    },
    async changeVKRStatus() {
      return await utility.changeVKRStatus(this.itemVKR.id, this.$store.state.status.VKRUpload.id, false);
    },
    async changeVKRStatusManager() {
      return await utility.ajaxMoodleCall(
          'local_cdo_vkr_change_manager_status_of_vkr',
          {
            id: this.itemVKR.id
          }
      );
    },
    async setAgreedEBS() {
      let status = await utility.ajaxMoodleCall('local_cdo_vkr_accept_ebs_agreed', {
        id: this.itemVKR.id
      }); //TODO make true/false refactor
    },
    async setAcquaintedStatus(areaFile) {
      this.loadingAcquired = true;

      let result = await utility.ajaxMoodleCall('local_cdo_vkr_set_acquainted_status',
          {
            file_id: areaFile.id,
          }
      );
      if (result) {
        areaFile.user_status.id = 1;
        await this.changeVKRStatusManager();
      }
      this.loadingAcquired = false;
    }

  },
  async created() {
    this.strings = this.$store.state.strings;
    this.itemVKR = await this.getVKRInfo();
    if (!!this.itemVKR)
      this.getFilesVKR();
    this.$store.state.loaderOn = false;
  },
  computed: {
    writeVKRStatusName() {
      if (this.itemVKR.hasOwnProperty('status'))
        return utility.writeVKRStatusName(this.itemVKR.status.id, this.$store.state.status);
      return '';
    },
    isWorkIsUploaded() {
      if (this.vkrFiles.hasOwnProperty('work')) {
        return this.vkrFiles.work.hasOwnProperty('id'); //true
      }
      return false;
    },
    isCommentUpload() {
      if (this.vkrFiles.hasOwnProperty('comment')) {
        return this.vkrFiles.comment.hasOwnProperty('id'); //true
      }
      return false;
    },
    isReviewUpload() {
      if (this.vkrFiles.hasOwnProperty('review')) {
        return this.vkrFiles.review.hasOwnProperty('id'); //true
      }
      return false;
    },
    isVKRHaveArchive() {
      if (this.vkrFiles.hasOwnProperty('work_archive')) {
        return !!this.vkrFiles.work_archive.length; //true
      }
      return false;
    }
  },
  watch: {
    vkrFiles: {
      handler(newData, oldData) {
        console.log(newData);
        if (!this.itemVKR.acquainted) {
          if (this.$store.state.levelEduCantBeReviewed.includes(this.itemVKR.edu_level)) {
            console.log(newData);
            if (newData.comment.hasOwnProperty('id') && newData.review.hasOwnProperty('id')) {
              console.log(newData);
              if (newData.comment.user_status.id === 1
                  && newData.review.user_status.id === 1) {
                utility.ajaxMoodleCall('local_cdo_vkr_set_acquainted_to_vkr',
                    {
                      id: this.itemVKR.id,
                    }
                );
                this.itemVKR.status = this.$store.state.status.VKRStudentReadInfo;
                this.$store.commit('changeItemVKR', this.itemVKR);
              }
            }
          } else {
            if (newData.comment.hasOwnProperty('id')) {
              if (newData.comment.user_status.id === 1) {
                utility.ajaxMoodleCall('local_cdo_vkr_set_acquainted_to_vkr',
                    {
                      id: this.itemVKR.id,
                    }
                );
                /*this.itemVKR.status.id = this.$store.state.status.VKRAgreed.id;
                this.itemVKR.status.name = this.$store.state.status.VKRAgreed.name;*/
                this.itemVKR.status = this.$store.state.status.VKRStudentReadInfo;
                this.$store.commit('changeItemVKR', this.itemVKR);
              }
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