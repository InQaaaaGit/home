<template>
  <b-card border-variant="primary" class="h-100">
    <b-card-text>
      {{ file.comment }}&nbsp;
      <b-link @click="$emit('show-modal-file', file.id)" v-if="!getSettings.isAuditor">
        <b-icon icon="pencil-square"></b-icon>
      </b-link>
      &nbsp;
      <b-link @click="$emit('show-confirm-delete', file.id)" v-if="!getSettings.isAuditor">
        <b-icon icon="trash-fill"></b-icon>
      </b-link>
    </b-card-text>
    <b-card-text class="text-muted" v-if="file.edu_plan">
      Учебный план: {{ file.edu_plan }}
    </b-card-text>
    <b-card-text class="small text-muted">
      <b-link :href="file.path" target="_blank">
        {{ file.name }}
      </b-link>
    </b-card-text>
    <template v-if="file.description || getSettings.isAuditor">
      <b-card-text class="small">
        Комментарий:
        <b-link
            v-if="getSettings.isAuditor"
            @click="$emit('show-modal-notes', file.id)"
        >
          <b-icon icon="pencil-square"></b-icon>
        </b-link>
      </b-card-text>
      <b-card-text class="small text-muted">
        {{ file.description }}
      </b-card-text>
    </template>
  </b-card>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "FileCard",
  props: {
    file: Object
  },
  data() {
    return {}
  },
  computed: {
    ...mapGetters('APP', [
      'getSettings'
    ]),
  }
}
</script>
