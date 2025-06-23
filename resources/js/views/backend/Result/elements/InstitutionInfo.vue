<template>
  <div class="mb-2">
    <h4 class="text-center mb-2">
      {{ data.institution ? data.institution.name : "" }}
    </h4>
    <h6 class="text-center text-uppercase mb-2" v-if="title">
      {{ title }}
    </h6>
    <h6
      v-if="sub_title"
      class="text-center text-uppercase mb-3 border-bottom pb-3"
    >
      {{ sub_title }}
    </h6>
    <table class="table table-striped table-sm">
      <tbody>
        <tr>
          <th style="width: 8%">Session</th>
          <td style="width: 22%">
            {{ data.academic_session ? data.academic_session.name : "" }}
          </td>
          <th style="width: 8%">Exam</th>
          <td style="width: 30%">{{ data.exam ? data.exam.name : "" }}</td>

          <th style="width: 8%">
            <span v-if="subject">Subject</span>
          </th>
          <td style="width: 25%">
            <span v-if="subject">
              {{ subject ? subject.name_en : "" }}
            </span>
          </td>
        </tr>
        <tr>
          <th>Campus</th>
          <td>{{ data.campus ? data.campus.name : "" }}</td>
          <th>Shift</th>
          <td>{{ data.shift ? data.shift.name : "" }}</td>
          <th>Medium</th>
          <td>{{ data.medium ? data.medium.name : "" }}</td>
        </tr>
        <tr>
          <th>Class</th>
          <td>
            {{ data.academic_class ? data.academic_class.name : "" }}
          </td>
          <th>Group</th>
          <td>{{ data.group ? data.group.name : "" }}</td>
          <th>Section</th>
          <td>{{ data.section ? data.section.name : "ALL" }}</td>
        </tr>

        <slot></slot>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ["title", "sub_title", "data", "subject"],
};
</script>