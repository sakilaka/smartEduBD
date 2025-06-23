<template>
  <div class="col-12">
    <table class="table table-sm table-bordered table-striped mb-0">
      <thead>
        <tr>
          <th width="200px">Academic Level</th>
          <th width="200px">Department</th>
          <th width="150px">Class</th>
          <th width="150px">Subject</th>
          <th width="100px">Status</th>
          <th width="100px"></th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(item, key) in $parent.data.subject_assigns" :key="key">
          <td>
            <v-select
              v-model="item.academic_qualification_id"
              label="name"
              :reduce="(obj) => obj.id"
              :options="
                $root.global.academic_levels ? $root.global.academic_levels : []
              "
              placeholder="--Select Any--"
              :closeOnSelect="true"
            ></v-select>
          </td>
          <td>
            <v-select
              v-model="item.department_id"
              label="name"
              :reduce="(obj) => obj.id"
              :options="departments_filter(item.academic_qualification_id)"
              placeholder="--Select Any--"
              :closeOnSelect="true"
            ></v-select>
          </td>
          <td>
            <v-select
              v-model="item.academic_class_id"
              label="name"
              :reduce="(obj) => obj.id"
              :options="class_filter(item.academic_qualification_id)"
              placeholder="--Select Any--"
              :closeOnSelect="true"
            ></v-select>
          </td>
          <td>
            <v-select
              v-model="item.subject_id"
              label="name"
              :reduce="(obj) => obj.id"
              :options="$parent.extraData.subjects"
              placeholder="--Select Any--"
              :closeOnSelect="true"
            ></v-select>
          </td>
          <td>
            <select v-model="item.status" class="form-select form-select-sm">
              <option value="">Status</option>
              <option value="active">Active</option>
              <option value="deactive">Deactive</option>
            </select>
          </td>
          <td>
            <i
              v-if="Object.keys($parent.data.subject_assigns).length > 1"
              @click="$parent.data.subject_assigns.splice(key, 1)"
              class="bx bx-minus btn btn-sm btn-danger"
            >
            </i>
            <i
              v-if="Object.keys($parent.data.subject_assigns).length == key + 1"
              @click="
                $parent.data.subject_assigns.push({
                  department_id: null,
                  academic_qualification_id: null,
                  academic_class_id: null,
                  subject_id: null,
                  status: 'active',
                })
              "
              class="bx bx-plus btn btn-sm btn-primary ml-2"
            >
            </i>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>