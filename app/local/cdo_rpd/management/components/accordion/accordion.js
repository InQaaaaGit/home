Vue.component('accordion', {
  props: {
    multiple: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data() {
    return {
      Accordion: {
        count: 0,
        active: null,
        multiple: this.multiple
      }
    };
  },
  provide() {
    return { Accordion: this.Accordion };
  },
  template: `
    <ul class="accordion">
      <slot></slot>
    </ul>
  `
})