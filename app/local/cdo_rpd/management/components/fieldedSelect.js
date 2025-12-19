Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
});

Vue.component('fielded-select', {
    props: {
        items: {
            type: Array,
            required: true,
            default: () => ([])
        },
        value: {},
        label: {
            type: String,
            required: false,
            default: ''
        },
        itemName: {
            type: String,
            required: false,
            default: 'value'
        },
        clearable: {
            type: Boolean,
            required: false,
            default: false
        },
        placeholder: {
            type: String,
            required: false,
            default: 'Не выбрано'
        }
    },
    data: () => ({
        showOptions: false,
        legendWidth: null
    }),
    computed: {
        showClearable() {
            return (this.value || this.value?.[this.itemName]) && this.clearable
        }
    },
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
    watch: {
        value(val, old) {
            if (!_.isEqual(val, old)) this.$emit('change', {current: val, old})
        }
    },
    template: `
      <fieldset 
        class="component-container" 
        tabindex="0"
        @blur="showOptions = false" 
        @click="showOptions = !showOptions"
        ref="rootWrapper" 
      >
      <legend 
        v-if="label.trim()" 
        v-html="label" 
        :title="label"
        :style="{maxWidth: legendWidth + 'px'}"
        >
      </legend>
        <div class="component__select">
            <span class="component__select--name" v-if="!items.length">
              Элементов нет
            </span>
            <span class="component__select--name" v-else>{{value ? value[itemName] || value : placeholder}}</span>
            <span 
              v-if="showClearable"
              @click.stop="$emit('input', '')"
              class="c-close">✖</span>
            <span class="c-arrow-down"
                  :class="{'c-arrow-transform' : showOptions}"
            ></span>
        </div>
        <transition name="bounce">
           <ul class="component__select-options" v-if="showOptions" >
              <li class="select--option no-data-option" v-if="!items.length">
               <span>Элементов нет</span>
              </li>
              <li class="select--option" v-for="item in items" @click="$emit('input', item)">
                  <span>{{item[itemName] ?? item ?? 'Элементов нет'}}</span>
              </li>
          </ul>
        </transition>
    </fieldset>
  `
});
