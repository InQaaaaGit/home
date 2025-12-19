<template>
  <div ref="SchedulerComponent" class="scheduler-container"></div>
</template>

<script>
import { scheduler } from "dhtmlx-scheduler";


export default {
  props: {
    events: {
      type: Array,
      default() {
        return []
      },
    },
  },

  methods: {
    $_initDataProcessor: function() {
      if (!scheduler.$_dataProcessorInitialized) {
        scheduler.createDataProcessor((entity, action, data, id) => {
          this.$emit(`${entity}-updated`, id, action, data);
        });
        scheduler.$_dataProcessorInitialized = true;
      }
    },

  },
  mounted: function () {
    scheduler.skin = "material";
    scheduler.config.header = [
      "day",
      "week",
      "month",
      "date",
      "prev",
      "today",
      "next",
    ];
    this.$_initDataProcessor();
    scheduler.i18n.setLocale("ru");
    scheduler.config.hour_size_px = 108;
    scheduler.config.first_hour = 7;
    scheduler.config.last_hour = 22;
    scheduler.config.readonly = true;
    scheduler.init(
        this.$refs.SchedulerComponent,
        new Date(),
        "week"
    );
    scheduler.parse(this.$props.events);

  },
};
</script>

<style>
@import "~dhtmlx-scheduler/codebase/dhtmlxscheduler.css";

.scheduler-container {
  width: 100%;
  height: 100%;
}
</style>