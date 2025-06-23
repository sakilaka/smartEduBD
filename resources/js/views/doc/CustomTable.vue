<template>
  <div class="table-responsive" id="printArea">
    <table
      id="pdf-table"
      class="table table-sm table-bordered table-striped table-hover mb-0"
    >
      <thead>
        <tr>
          <th class="sl">#</th>
          <Sorting column="name" title="Name" />
          <th>Name</th>
          <th>Status</th>
          <th class="action">Action</th>
        </tr>
      </thead>

      <slot v-if="!$root.tableSpinner">
        <tbody v-if="Object.keys(table.datas).length > 0">
          <slot v-for="(data, index) in table.datas">
            <tr :key="index">
              <th>{{ index + 1 }}</th>
              <td>{{ data.name }}</td>
              <td>{{ data.name }}</td>
              <td>{{ data.status }}</td>
              <td class="action">
                <ActionButton :routes="table.routes" :data="data" />

                <!-- <router-link
                  v-if="
                    table.routes.view &&
                    $root.checkPermission(table.routes.view)
                  "
                  :to="{ name: table.routes.view, params: { id: data.id } }"
                  title="View"
                  class="btn btn-outline-success btn-xs"
                >
                <i class='bx bxs-show'></i>
                </router-link>

                <router-link
                  v-if="
                    table.routes.edit &&
                    $root.checkPermission(table.routes.edit)
                  "
                  :to="{ name: table.routes.edit, params: { id: data.id } }"
                  title="Edit"
                  class="btn btn-outline-primary btn-xs"
                >
                  <i class="bx bx-edit me-0"></i>
                </router-link>

                <button
                  v-if="
                    table.routes.destroy &&
                    $root.checkPermission(table.routes.destroy)
                  "
                  @click="
                    notify('Delete', 'confirm', data.id)
                  "
                  title="Delete"
                  class="btn btn-outline-danger btn-xs"
                >
                  <i class="bx bx-trash me-0"></i>
                </button> -->
              </td>
            </tr>
          </slot>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="3">
              <p class="p-2 text-center text-red no-data">No data found.</p>
            </td>
          </tr>
        </tbody>
      </slot>
    </table>

    <!-- Table Spinner -->
    <TableSpinner />
  </div>
</template>

<script>
import TableSpinner from "./../../components/backend/elements/Table/TableSpinner.vue";
import ActionButton from "./../../components/backend/elements/Table/ActionButton.vue";
import Sorting from "./../../components/backend/elements/Table/ColumnSorting.vue";

export default {
  components: { ActionButton, TableSpinner, Sorting },

  // components: {
  //   TableSpinner: () =>
  //     import("./../../../components/backend/elements/Table/TableSpinner"),
  //   ActionButton: () =>
  //     import("./../../../components/backend/elements/Table/ActionButton"),
  //   Sorting: () =>
  //     import("./../../../components/backend/elements/Table/ColumnSorting"),
  // },
};
</script>