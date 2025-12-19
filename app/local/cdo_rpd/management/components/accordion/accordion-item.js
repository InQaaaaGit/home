Vue.component('accordion-item', {
    multiple: {
        type: Boolean,
        required: false,
        default: false
    },
    inject: ["Accordion"],
    data() {
        return {
            index: null,
            isShow: false
        };
    },
    computed: {
        visible() {
            if (this.Accordion.multiple) return this.isShow;
            else return this.index === this.Accordion.active;
        },
    },
    methods: {
        open() {
            if (this.Accordion.multiple) {
                this.isShow = !this.isShow;
            } else {
                if (this.visible) {
                    this.Accordion.active = null;
                } else {
                    this.Accordion.active = this.index;
                }
            }
        },
        start(el) {
            el.style.height = el.scrollHeight + "px";
        },
        end(el) {
            el.style.height = "";
        },
    },
    created() {
        this.index = this.Accordion.count++;
    },
    template: `
    <li class="accordion__item">
    <div
      class="accordion__trigger"
      :class="{ accordion__trigger_active: visible }"
      @click="open"
    >
      <div class="accordion__header">
        <svg
          width="12"
          height="8"
          viewBox="0 0 12 8"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
          class="arrow"
          :class="{ 'arrow-rotate': visible }"
        >
          <path
            d="M6 8L12 2L10.59 0.59L6 5.17L1.41 0.589999L5.24537e-07 2L6 8Z"
            fill="black"
            fill-opacity="0.54"
          />
        </svg>
        <slot name="accordion-trigger"></slot>
      </div>
    </div>
    <transition
      name="accordion"
      @enter="start"
      @after-enter="end"
      @before-leave="start"
      @after-leave="end"
    >
      <div class="accordion__content" v-show="visible">
        <ul>
          <slot name="accordion-content"></slot>
        </ul>
      </div>
    </transition>
  </li>
  `
})