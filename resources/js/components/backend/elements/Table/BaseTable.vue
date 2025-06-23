<template>
  <div class="table-responsive" id="printArea">
    <table
      id="pdf-table"
      class="table table-sm table-bordered table-striped mb-0"
    >
      <thead>
        <tr>
          <th class="sl text-center">#</th>
          <slot name="columns">
            <th
              v-for="(column, index) in columns"
              :key="'a' + index"
              @click="sort(column.field)"
              class="sort-th"
              :style="'text-align:' + column.align + '; width:' + column.width"
            >
              <span v-html="column.title"></span>
              <span v-if="coloumSort == column.field">
                <i
                  v-if="order == 'desc'"
                  class="bx bx-sort-down float-end sort"
                ></i>
                <i
                  v-if="order == 'asc'"
                  class="bx bx-sort-up float-end sort"
                ></i>
              </span>
              <i v-else class="bx bx-sort-up float-end sort"></i>
            </th>

            <th
              v-if="
                Object.keys(routes).length > 0 &&
                ($root.checkPermission(routes.view) ||
                  $root.checkPermission(routes.edit) ||
                  $root.checkPermission(routes.destroy))
              "
              :class="routes.array ? 'action' : 'action'"
            >
              Action
            </th>
          </slot>
        </tr>
      </thead>
      <slot v-if="!$root.tableSpinner">
        <tbody v-if="Object.keys(data).length > 0">
          <tr
            v-for="(item, index) in data"
            :key="index"
            class="change_sorting"
            :class="'change_sorting' + item.sorting + ' update_item' + item.id"
          >
            <th class="text-center" v-if="$parent.table.meta">
              {{ $parent.table.meta.from + index }}
            </th>
            <th v-else class="text-center">{{ index + 1 }}</th>
            <slot
              v-for="(column, index) in columns"
              :name="column.field"
              :item="item"
            >
              <td
                :key="'b' + index"
                :style="
                  'text-align:' +
                  column.align +
                  '; text-transform:' +
                  column.text
                "
                :v-if="hasValue(item, column.field)"
              >
                <span v-if="column.amount">{{
                  itemValue(item, column.field) | currency
                }}</span>
                <span v-else-if="column.date">{{
                  itemValue(item, column.field) | formatDate
                }}</span>
                <span v-else-if="column.dateTime">{{
                  itemValue(item, column.field) | formatDate("LLL")
                }}</span>
                <span v-else-if="column.array">{{
                  column.array_value[0][itemValue(item, column.field)]
                }}</span>
                <slot v-else-if="column.sorting">
                  <input
                    v-if="!sorting_spin"
                    v-on:keyup.enter="
                      tableSorting(
                        $event.target.value,
                        item.id,
                        column.namespace,
                        column.auto
                      )
                    "
                    :value="item[column.field]"
                    type="text"
                    class="base-table-form-control text-center"
                    style="width: 80px; font-size: 12px"
                  />
                  <i
                    v-if="item.id == sorting_spin"
                    class="spinner-border spinner-border-sm"
                  ></i>
                </slot>
                <img
                  v-else-if="column.image && itemValue(item, column.field)"
                  :src="itemValue(item, column.field)"
                  :style="
                    'width:' + column.imgWidth + ';height:' + column.height
                  "
                />
                <span v-else-if="column.field == 'status'">
                  <span
                    :class="warningClass()"
                    v-if="itemValue(item, column.field) == 'draft'"
                    ><i class="bx bxs-circle me-1"></i>DRAFT</span
                  >
                  <span
                    class="text-uppercase"
                    :class="activeClass()"
                    v-if="
                      itemValue(item, column.field) == 'active' ||
                      itemValue(item, column.field) == 'published' ||
                      itemValue(item, column.field) == 1
                    "
                  >
                    <i class="bx bxs-circle me-1"></i>
                    {{ itemValue(item, column.field) }}</span
                  >
                  <span
                    :class="errorClass()"
                    v-if="
                      itemValue(item, column.field) == 'deactive' ||
                      itemValue(item, column.field) == 0
                    "
                    ><i class="bx bxs-circle me-1"></i>DEACTIVE</span
                  >
                  <span
                    :class="errorClass()"
                    v-if="itemValue(item, column.field) == 'ur'"
                    ><i class="bx bxs-circle me-1"></i>UNREAD</span
                  >
                  <span
                    :class="activeClass()"
                    v-if="itemValue(item, column.field) == 'r'"
                    ><i class="bx bxs-circle me-1"></i>READ</span
                  >
                  <span
                    :class="errorClass()"
                    v-if="itemValue(item, column.field) == 'pending'"
                    ><i class="bx bxs-circle me-1"></i>PENDING</span
                  >
                  <span
                    :class="activeClass()"
                    v-if="itemValue(item, column.field) == 'success'"
                    ><i class="bx bxs-circle me-1"></i>SUCCESS</span
                  >
                  <span
                    :class="errorClass()"
                    v-if="itemValue(item, column.field) == 'failed'"
                    ><i class="bx bxs-circle me-1"></i>FAILED</span
                  >
                  <span
                    :class="errorClass()"
                    v-if="itemValue(item, column.field) == 'cancelled'"
                    ><i class="bx bxs-circle me-1"></i>CANCELLED
                  </span>
                </span>
                <span v-else>{{ itemValue(item, column.field) }} </span>
              </td>
            </slot>
            <td
              v-if="
                Object.keys(routes).length > 0 &&
                ($root.checkPermission(routes.view) ||
                  $root.checkPermission(routes.edit) ||
                  $root.checkPermission(routes.destroy))
              "
              :class="routes.array ? 'action' : 'action'"
            >
              <router-link
                v-if="routes.view && $root.checkPermission(routes.view)"
                :to="{
                  name: routes.view,
                  params: { id: item.slug ? item.slug : item.id },
                  query: { page: $route.query.page },
                }"
                title="View"
                class="btn btn-outline-success btn-xs"
              >
                <i class="bx bxs-show"></i>
              </router-link>

              <router-link
                v-if="routes.edit && $root.checkPermission(routes.edit)"
                :to="{
                  name: routes.edit,
                  params: { id: item.slug ? item.slug : item.id },
                  query: { page: $route.query.page },
                }"
                title="Edit"
                class="btn btn-outline-primary btn-xs"
              >
                <i class="bx bx-edit me-0"></i>
              </router-link>

              <button
                v-if="
                  routes.destroy &&
                  $root.checkPermission(routes.destroy) &&
                  itemValue(item, 'status', null) != 'deactive'
                "
                @click="
                  notify('Delete', 'confirm', item.slug ? item.slug : item.id)
                "
                title="Delete"
                class="btn btn-outline-danger btn-xs"
              >
                <i class="bx bx-trash me-0"></i>
              </button>

              <!-- custom route -->
              <slot v-if="routes.array">
                <slot v-for="(rt, index) in routes.array">
                  <button
                    :key="index"
                    v-if="
                      rt.callBack &&
                      rt.route &&
                      $root.checkPermission(rt.route) &&
                      !(rt.hidecallBack && rt.hidecallBack(item))
                    "
                    @click.prevent="rt.callBack(item)"
                    :class="'btn btn-xs btn-outline-' + rt.btnColor"
                    class="action-custom mt-1"
                  >
                    <i class="me-0" :class="'bx bx-' + rt.icon"></i>
                    {{ rt.label }}
                  </button>
                  <router-link
                    :key="index"
                    v-if="
                      !rt.callBack &&
                      rt.route &&
                      $root.checkPermission(rt.route)
                    "
                    :to="{
                      name: rt.route,
                      params: { id: item.slug ? item.slug : item.id },
                    }"
                    :class="'btn btn-xs btn-outline-' + rt.btnColor"
                    class="action-custom"
                  >
                    acasc
                    <i class="me-0" :class="'bx bx-' + rt.icon"></i>
                  </router-link>
                </slot>
              </slot>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td :colspan="Object.keys(columns).length + 2">
              <p class="p-2 text-center text-red no-data">No data found.</p>
            </td>
          </tr>
        </tbody>

        <!-- TOTALs -->
        <tbody>
          <tr v-if="totals && totals[0].amount">
            <th :colspan="Object.keys(columns).length + 2" class="text-center">
              <div
                class="row my-4 text-center d-flex justify-content-center align-items-center"
              >
                <template v-for="(total, key) in totals">
                  <div v-if="total.amount" :key="key" class="col-3 text-center">
                    <h6>
                      <b>{{ total.title }} : {{ total.amount | currency }}</b>
                    </h6>
                  </div>
                </template>
              </div>
            </th>
          </tr>
        </tbody>
      </slot>
    </table>

    <!-- Table Spinner -->
    <TableSpinner />
  </div>
</template>


<script>
import TableSpinner from "./TableSpinner.vue";

export default {
  components: { TableSpinner },
  name: "BaseTable",
  data() {
    return {
      order: "desc",
      coloumSort: "",
      sorting_spin: false,
    };
  },
  props: ["data", "columns", "routes", "totals"],
  methods: {
    hasValue(item, column) {
      return item[column.toLowerCase()] !== "undefined";
    },
    itemValue(item, column) {
      let value = String(column)
        .split(".")
        .reduce((prev, curr) => {
          if (prev instanceof Object) return prev[curr];
        }, item);

      return value;
    },
    callBack(id) {
      this.$parent.destroy(id); // destroy item
    },
    tableSorting(number, id, model, auto) {
      $(".change_sorting").removeClass("sorting-success");
      this.sorting_spin = id;
      let data = { number: number, id: id, model: model, auto: auto };
      axios
        .get("table-sorting", { params: data })
        .then((res) => this.$parent.search())
        .catch((error) => console.log(error))
        .then((alw) => {
          this.sorting_spin = "";
          $(".change_sorting" + number).addClass("sorting-success");
        });

      setTimeout(
        () => $(".change_sorting").removeClass("sorting-success"),
        5000
      );
    },
    sort(field) {
      this.coloumSort = field;
      this.data.sort(this.sortBy(field));
    },
    sortBy(property) {
      if (this.order === "desc") {
        this.order = "asc";
      } else {
        this.order = "desc";
      }
      const order = this.order;
      return function (a, b) {
        const varA =
          typeof a[property] === "string"
            ? a[property].toUpperCase()
            : a[property];
        const varB =
          typeof b[property] === "string"
            ? b[property].toUpperCase()
            : b[property];

        let comparison = 0;
        if (varA > varB) comparison = 1;
        else if (varA < varB) comparison = -1;
        return order === "desc" ? comparison * -1 : comparison;
      };
    },
    generatePdf() {
      const doc = new jsPDF();
      $(".action").css("display", "none");
      autoTable(doc, { html: "#pdf-table" });
      doc.save(this.$parent.model + ".pdf");
      setTimeout(() => $(".action").show(), 300);
    },

    activeClass() {
      return "badge rounded-pill text-success bg-light-success p-1 text-uppercase px-3 w-100";
    },
    warningClass() {
      return "badge rounded-pill text-warning bg-light-warning p-1 text-uppercase px-3 w-100";
    },
    errorClass() {
      return "badge rounded-pill text-danger bg-light-danger p-1 text-uppercase px-3 w-100";
    },
  },
};
</script>