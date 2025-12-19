Vue.component('fieldedInput',{
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
    searchIcon: {
      type: Boolean,
      required: false,
      default: false
    },
    dashed: {
      type: Boolean,
      required: false,
      default: false
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
      this.legendWidth = this.$refs.rootWrapper.clientWidth - 24
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
        class="fielded-input"
        :class="{'dashed': dashed, 'dashed--pt20': dashed && label}"
        >
      <legend 
        v-if="label.trim() && !disabled" 
        v-html="label"
        :style="{maxWidth: legendWidth + 'px'}"
        :class="{'label__ifDashed' : dashed}"
        >
      </legend>
      <div class="fielded-input__container">
        <input type="text"
          :placeholder="placeholder"
          :value="value"
          @input="$emit('input', $event.target.value)"
          :disabled="disabled"
        >
        <svg 
          class="mr-10"
          v-if="searchIcon"
          width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12.5 11H11.71L11.43 10.73C12.41 9.59 13 8.11 13 6.5C13 2.91 10.09 0 6.5 0C2.91 0 0 2.91 0 6.5C0 10.09 2.91 13 6.5 13C8.11 13 9.59 12.41 10.73 11.43L11 11.71V12.5L16 17.49L17.49 16L12.5 11ZM6.5 11C4.01 11 2 8.99 2 6.5C2 4.01 4.01 2 6.5 2C8.99 2 11 4.01 11 6.5C11 8.99 8.99 11 6.5 11Z" fill="black" fill-opacity="0.54"/>
        </svg>
      </div>
    </fieldset>
  `
})