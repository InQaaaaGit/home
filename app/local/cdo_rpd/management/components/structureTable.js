Vue.component('structure-table', {
    props: {
        tableColumns: {
            type: Array,
            required: true
        },
        tableData: {
            type: Array,
            required: true
        },
        search: {
            type: String,
            required: false,
            default: ''
        },

    },
    data: function () {
        const sortOrders = {};
        this.tableColumns.forEach(function (item) {
            sortOrders[item.value] = 1;
        })
        return {
            isActivePaginationSelect: false,
            currentPage: 0,
            itemsPerPage: null,
            listPerPage: [10, 20, 50, 'Все'],
            sortKey: '',
            sortOrders: sortOrders,
            sortedColumn: null
        }
    },
    created() {
        this.itemsPerPage = this.listPerPage[0];
    },
    computed: {
        sortedTableByColumn: function () {

            if (this.sortKey) {
                const currentColumn = this.tableColumns.filter(item=>item.value===this.sortedColumn);
                if(currentColumn[0].hasOwnProperty('sortFun')) {
                    return currentColumn[0].sortFun(this.paginatedData, this.sortOrders);
                } else {
                    const order = this.sortOrders[this.sortKey] || 1;
                    return _.cloneDeep(this.paginatedData).sort((a, b) => {
                        a = a[this.sortKey];
                        b = b[this.sortKey];
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    });
                }
            } else {
                return this.paginatedData
            }
        },
        searchTable() {
            if (this.search)
                return this.tableData.filter(item => item.discipline.toLowerCase().includes(this.search.toLowerCase()));
            else return this.tableData;
        },
        getTableColumnValues() {
            return this.tableColumns.map(item => item.value);
        },
        getSelectableColumns() {
            return this.tableColumns.map(item => item.sortable);
        },
        getCenteredTD() {
            return this.tableColumns.map(item => item.align);
        },
        pageCount() {
            const itemsPerPage = isNaN(this.itemsPerPage) ? this.searchTable.length : this.itemsPerPage;
            let l = this.searchTable.length,
                s = itemsPerPage;
            return Math.ceil(l / s);
        },
        paginatedData() {
            const itemsPerPage = isNaN(this.itemsPerPage) ? this.searchTable.length : this.itemsPerPage;
            const start = this.currentPage * itemsPerPage,
                end = start + itemsPerPage;
            return this.searchTable.slice(start, end);
        },
        fromPage() {
            return this.currentPage * this.itemsPerPage || 1;
        },
        toPage() {
            const itemsPerPage = isNaN(this.itemsPerPage) ? this.searchTable.length : this.itemsPerPage;
            return this.currentPage * itemsPerPage + itemsPerPage
        }
    },
    methods: {
        sortByColumn(columnKey) {
            for (let prop in this.sortOrders) {
                if (prop !== columnKey) this.sortOrders[prop] = 1;
            }
            this.sortKey = columnKey;
            this.sortedColumn = columnKey;
            this.sortOrders[columnKey] = this.sortOrders[columnKey] * -1;
        },
        filterRowsByHeader(tableRowObject) {
            return Object.keys(_.cloneDeep(tableRowObject))
                .filter(key => this.getTableColumnValues.includes(key))
                .reduce((obj, key) => {
                    obj[key] = tableRowObject[key];
                    return obj;
                }, {});
        },
        selectPage(page) {
            this.currentPage = 0;
            this.itemsPerPage = page;
            this.isActivePaginationSelect = false;
        },
    },
    watch: {
        tableData: function (cur, old) {
            this.currentPage = 0;
        }
    },
    template: `
    <div>
      <table class="structure-table">
      <thead>
        <tr>
          <th 
            v-for="column in tableColumns"
            :style="{textAlign: column.align}"
            >
            <div 
              @click="column.sortable ? sortByColumn(column.value): ''"
              :class="{'cursor-default': !column.sortable}"
              class="structure-table_header-item">
              {{column.text}}
               <span 
                 :class="{'c-arrow-transform c-arrow--blue': sortOrders[column.value] < 0}"
                 class="c-arrow-down c-arrow-down--margin" 
                 v-if="column.sortable">
                </span>
             </div>
          </th>
        </tr>
      </thead>
      <tbody>

        <tr v-for="(row,idx) in sortedTableByColumn" :key="row.id">
          <slot name="td" :item="row">
            <td 
              :style="{textAlign: getCenteredTD[i]}"
              v-for="(item, name, i) in filterRowsByHeader(row)"
              :key="item.id">
             
              <slot :name="getTableColumnValues[i]" :item="row">
                {{row[getTableColumnValues[i]]}}
              </slot>
            </td>
          </slot>
        </tr>
      </tbody>
    </table>
      <div class="structure-table__pagination">
        <div class="pagination-per-page">
          <span class="pagination-per-page_title">Записей на странице: </span> 
          <div 
            @click="isActivePaginationSelect = true"
            @blur="isActivePaginationSelect = false"
            class="pagination-per-page_select" 
            tabindex="0"
            >
            <span class="pagination-per-page_counter">{{itemsPerPage}}</span>
            <span class="c-arrow-down c-arrow-down--reduce-margin"></span>
            <transition name="bounce">
              <ul class="pagination-dropdown" v-show="isActivePaginationSelect">
                <li 
                  v-for="page in listPerPage"
                  :key="page"
                  class="pagination-dropdown__item" 
                  @click.stop="selectPage(page)">{{page}}</li>
              </ul>
            </transition>
          </div>
        </div>
        <span class="pagination__counter">
          {{fromPage}}-{{toPage}} из {{searchTable.length}}
        </span>
        <button 
          @click="currentPage--"
          :disabled="currentPage == 0"
          class="pagination_button">
          <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z" fill="black" fill-opacity="0.87"/>
          </svg>
        </button>
        <button 
          @click="currentPage++"
          :disabled="currentPage >= pageCount - 1"
          class="pagination_button pagination_button--margin-left">
          <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z" fill="black" fill-opacity="0.87"/>
          </svg>
        </button>
      </div>
    </div>
  `,
});