Vue.component('fieldedTextarea',{
  props: {
    value:{},
    label: {
      type: String,
      required: false,
      default: ''
    },
    placeholder: {
      type: String,
      required: false,
      default: ''
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data: () => ({
    legendWidth: null
  }),
  methods: {
    calculateLegendWidth() {
      this.legendWidth = this.$refs.rootWrapper.clientWidth - 24;
    },
  },
  created() {
    window.addEventListener("resize", this.calculateLegendWidth);
  },
  mounted() {
    this.$nextTick(() => {
      this.calculateLegendWidth();
    })
  },
  destroyed() {
    window.removeEventListener("resize", this.calculateLegendWidth);
  },
  template: `
    <fieldset 
        ref="rootWrapper" 
        class="fielded-textarea">
      <legend 
        v-if="label.trim() && !disabled" 
        v-html="label"
        :style="{maxWidth: legendWidth + 'px'}"
        >
      </legend>
        <textarea
          :placeholder="placeholder"
          :value="value"
          @input="$emit('input', $event.target.value)"
          :disabled="disabled"
        ></textarea>
    </fieldset>
  `
})