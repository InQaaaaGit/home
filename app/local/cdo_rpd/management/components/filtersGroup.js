Vue.component('filters-group', {
    props: {
        filters: {
            type: Array
        },
        selectedFilter: {
            type: Object
        },
        hideCodev: {
            type: Boolean,
            default: false
        },
    },
    template: `
      <ul class="root-filters">
      <li
          v-for="(filter, idx) in filters"
          :key="filter.id"
          class="root-filters_item"
          @click="selectFilter(filter)"
          :class="{'root-filters_item--active':selectedFilter.id === filter.id, 
                 'root-filters_item--disabled':filter.disabled}"

      >
        {{ filter.name }}
      </li>
      </ul>
    `,
    data: () => ({
        realFilter: [],
    }),
    methods: {
        selectFilter(filter) {
            if (filter.disabled) return
            else this.$emit('filter-by', filter)
        }
    }
});